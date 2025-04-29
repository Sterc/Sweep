<?php

namespace Sweep\Processors\Item;

use MODX\Revolution\Processors\Processor;
use Sweep\Model\SweepItem;

class Scan extends Processor
{
    public $objectType = 'SweepItem';
    public $classKey = SweepItem::class;
    public $languageTopics = ['sweep'];
    //public $permission = 'create';

    public function initialize()
    {
        $this->start = (int)$this->getProperty('start', 0);
        $this->limit = (int)$this->getProperty('limit', 10);
        return parent::initialize();
    }

    public function process()
    {
        $messages = [];
        $cacheKey = 'sweep/files';

        $allFiles = $this->modx->cacheManager->get($cacheKey);

        if (empty($allFiles) || $this->start === 0) {
            $allFiles = $this->getAllFiles();
            $this->modx->cacheManager->set($cacheKey, $allFiles);
        }

        $total = count($allFiles);
        $files = array_slice($allFiles, $this->start, $this->limit);

        foreach ($files as $file) {
            $path  = str_replace(MODX_BASE_PATH, '/', $file);
            $usage = $this->whereFileUsed($path);

            if ($usage) {
                if ($object = $this->modx->getObject($this->classKey, ['path' => $path])) {
                    $object->remove();
                }
                $messages[] = sprintf('File in use: %s %s', $path, $usage);
            } else {
                if (!$object = $this->modx->getObject($this->classKey, ['path' => $path])) {
                    $object = $this->modx->newObject($this->classKey);
                    $object->fromArray([
                        'name' => basename($path),
                        'path' => $path,
                        'size' => round(filesize($file) / 1024)
                    ]);
                    $object->save();
                }
                $messages[] = sprintf('File UNUSED: %s', $path);
            }
        }

        $finished = ($this->start + $this->limit) >= $total;

        if ($finished) {
            $this->modx->cacheManager->delete($cacheKey);
        }

        return $this->success('', [
            'messages' => $messages,
            'finished' => $finished
        ]);
    }

    protected function whereFileUsed($path)
    {
        $relativePath = str_replace('uploads/', '', ltrim($path, '/'));

        if (!$relativePath) {
            return false;
        }

        $tables_fields = [
            'modResource'            => ['id', 'content', 'introtext', 'description', 'properties'],
            'modTemplateVarResource' => ['id', 'value'],
            'modChunk'               => ['id', 'snippet'],
            'modTemplate'            => ['id', 'content'],
            'modSnippet'             => ['id', 'snippet'],
            'modPlugin'              => ['id', 'plugincode'],
            'cgSetting'              => ['id', 'value'],
            'SiteLogo'               => ['id', 'title', 'url', 'logo'],
            'SiteQuotes'             => ['id', 'author', 'img', 'text'],
            'SiteReview'             => ['id', 'author', 'img', 'photo', 'text']
        ];

        foreach ($tables_fields as $class => $fields) {
            $q = $this->modx->newQuery($class);
            $q->select($this->modx->getSelectColumns($class, $class, '', $fields));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
                    foreach ($fields as $field) {
                        if (!empty($row[$field]) && $this->isContentContainsPath($row[$field], $relativePath)) {
                            return sprintf('in %s of %s (%s)', $field, $class, $row['id']);
                        }
                    }
                }
            }
        }

        return false;
    }

    protected function isContentContainsPath($content, $relativePath)
    {
        $relativePathEncoded = str_replace(' ', '%20', $relativePath);

        if (strpos($content, $relativePath) !== false || strpos($content, $relativePathEncoded) !== false) {
            return true;
        }

        $json = json_decode($content, true);
        if (is_array($json)) {
            if ($this->isUsedInJSON($json, $relativePath) || $this->isUsedInJSON($json, $relativePathEncoded)) {
                return true;
            }
        }

        return false;
    }

    public function isUsedInJSON($data, $needle)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->isUsedInJSON($value, $needle)) {
                    return true;
                }
            } elseif (is_string($value)) {
                if (strpos($value, $needle) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function getAllFiles()
    {
        $path = MODX_BASE_PATH . 'uploads/';
        $files = [];

        if (is_dir($path)) {
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($directory);

            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $filePath = $fileinfo->getPathname();
                    $extension = strtolower($fileinfo->getExtension());

                    if ($extension === 'webp') {
                        $baseName = pathinfo($filePath, PATHINFO_FILENAME);
                        $dirName = $fileinfo->getPath();
                        
                        $possibleExtensions = ['jpg', 'jpeg', 'png'];
                        $hasAlternative = false;

                        foreach ($possibleExtensions as $ext) {
                            $alternativePath = $dirName . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;
                            if (file_exists($alternativePath)) {
                                $hasAlternative = true;
                                break;
                            }
                        }

                        if ($hasAlternative) {
                            continue;
                        }
                    }

                    $files[] = $filePath;
                }
            }
        }
        
        return $files;
    }
}
