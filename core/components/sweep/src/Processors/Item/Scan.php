<?php

namespace Sweep\Processors\Item;

use MODX\Revolution\Processors\Processor;
use Sweep\Model\SweepItem;
use Sweep\Model\SweepFile;
use Sweep\Model\SweepDirectory;

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

        foreach ($files as $path) {
            $usedin = $this->isFileUsed($path);

            if (!$object = $this->modx->getObject($this->classKey, ['path' => $path])) {
                $object = $this->modx->newObject($this->classKey);
                $object->fromArray([
                    'name'   => basename($path),
                    'path'   => $path,
                    'size'   => round(filesize(MODX_BASE_PATH . trim($path, '/')) / 1024)
                ]);
            }

            if (!empty($usedin)) {
                $object->set('usedin', $usedin);
                $messages[] = sprintf('File in use: %s %s', $path, $usedin);
            } else {
                $object->set('usedin', '');
                $messages[] = sprintf('File UNUSED: %s', $path);
            }

            $object->save();
        }

        $finished = ($this->start + $this->limit) >= $total;

        return $this->success('', [
            'messages' => $messages,
            'finished' => $finished
        ]);
    }

    protected function isFileUsed($path)
    {
        $relativePath = str_replace('uploads/', '', ltrim($path, '/'));
        $relativePathEncoded = str_replace(' ', '%20', $relativePath);
    
        if (!$relativePath) {
            return false;
        }
    
        $tables_fields = [
            'modResource' => ['content', 'introtext', 'description', 'properties'],
            'modChunk' => ['snippet'],
            'modTemplate' => ['content'],
            'modSnippet' => ['snippet'],
            'modPlugin' => ['plugincode'],
            'modTemplateVarResource' => ['value'],
        ];
    
        if ($this->modx->getCount('modNamespace', ['name' => 'clientconfig'])) {
            $tables_fields['cgSetting'] = ['value'];
        }
    
        if ($this->modx->getCount('modNamespace', ['name' => 'digitalsignage'])) {
            $tables_fields['DigitalSignageSlides'] = ['data'];
        }

        foreach ($tables_fields as $class => $fields) {
            $q = $this->modx->newQuery($class);
            $q->select($this->modx->getSelectColumns($class));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $output = sprintf('[%s](%s)', $class, $row['id']);

                    if ($class == 'modTemplateVarResource') {
                        $output = sprintf('[%s](%s){%s}', $class, $row['tmplvarid'], $row['contentid']);
                    }

                    foreach ($fields as $field) {
                        if (!empty($row[$field])) {
                            $content = $row[$field];

                            if (
                                strpos($content, $relativePath) !== false ||
                                strpos($content, $relativePathEncoded) !== false
                            ) {
                                return $output;
                            }

                            $json = json_decode($content, true);
                            if (is_array($json)) {
                                if (
                                    $this->isUsedInJSON($json, $relativePath) ||
                                    $this->isUsedInJSON($json, $relativePathEncoded)
                                ) {
                                    return $output;
                                }
                            }
                        }
                    }
                }
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
        $itemTable = $this->modx->getTableName(SweepItem::class);
        $sql = "DELETE `items` FROM $itemTable `items`";
        $stmt = $this->modx->prepare($sql);
        $stmt->execute();

        $allFiles = [];
        $directories = $this->modx->getCollection(SweepDirectory::class, ['active' => true]);
        foreach ($directories as $directory) {
            $path = trim($directory->path, ' /');
            if (!empty($path)) {
                $files = $this->scanDirectory(MODX_BASE_PATH . $path);
                $allFiles = array_merge($allFiles, $files);
            }
        }

        return $allFiles;
    }
    
    protected function scanDirectory($path)
    {
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

                    $files[] = str_replace(MODX_BASE_PATH, '/', $filePath);
                }
            }
        }

        return $files;
    }
}
