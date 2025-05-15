<?php

namespace Sweep\Processors\Item;

use MODX\Revolution\Processors\Processor;
use Sweep\Model\SweepItem;
use Sweep\Model\SweepFile;

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

        if ($this->start === 0) {
            $this->rescanDirectories();
        }

        $total = $this->modx->getCount(SweepFile::class);

        $query = $this->modx->newQuery(SweepFile::class);
        $query->select($this->modx->getSelectColumns(SweepFile::class));
        $query->limit($this->start, $this->limit);
        $query->prepare();
        $query->stmt->execute();
        $files = $query->stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($files as $file) {
            $path = $file['path'];
            $usedon = $this->isFileUsed($path);

            if (!$object = $this->modx->getObject($this->classKey, ['path' => $path])) {
                $object = $this->modx->newObject($this->classKey);
                $object->fromArray([
                    'name'   => basename($path),
                    'path'   => $path,
                    'size'   => round(filesize(MODX_BASE_PATH . trim($path, '/')) / 1024)
                ]);
            }

            if (!empty($usedon)) {
                $object->set('usedon', $usedon);
                $messages[] = sprintf('File in use: %s %s', $path, $used);
            } else {
                $object->set('usedon', '');
                $messages[] = sprintf('File UNUSED: %s', $path);
            }

            $object->save();
        }

        $finished = ($this->start + $this->limit) >= $total;

        if ($finished) {
            $itemTable = $this->modx->getTableName(SweepItem::class);
            $fileTable = $this->modx->getTableName(SweepFile::class);
            $sql = "DELETE `items` FROM $itemTable `items` LEFT JOIN $fileTable `files` ON `items`.`path` = `files`.`path` WHERE `files`.`path` IS NULL";
            $stmt = $this->modx->prepare($sql);
            $stmt->execute();
    
            $this->modx->removeCollection(SweepFile::class, ['path:IS NOT' => null]);
        }

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
            $q->select($this->modx->getSelectColumns($class, $class, '', array_merge(['id'], $fields)));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $output = sprintf('[%s](%s)', $class, $row['id']);
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

    protected function rescanDirectories()
    {
        $path = MODX_BASE_PATH . 'uploads/';

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

                    $filePath = str_replace(MODX_BASE_PATH, '/', $filePath);

                    if (!$object = $this->modx->getObject(SweepFile::class, ['path' => $filePath])) {
                        $object = $this->modx->newObject(SweepFile::class);
                        $object->set('path', $filePath);
                        $object->save();
                    }
                }
            }
        }
    }
}
