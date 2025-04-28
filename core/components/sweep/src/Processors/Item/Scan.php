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
            $path = str_replace(MODX_BASE_PATH, '/', $file);

            if (!$object = $this->modx->getObject($this->classKey, ['path' => $path])) {
                $object = $this->modx->newObject($this->classKey);
                $object->fromArray([
                    'name' => basename($path),
                    'path' => $path
                ]);
                $object->save();
            }

            $messages[] = sprintf('Scanned file: %s', $path);
            sleep(1);
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

    protected function getAllFiles()
    {
        $path = MODX_BASE_PATH . 'uploads/';
        $files = [];

        if (is_dir($path)) {
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($directory);

            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $files[] = $fileinfo->getPathname();
                }
            }
        }

        return $files;
    }
}
