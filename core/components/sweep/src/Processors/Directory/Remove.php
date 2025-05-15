<?php

namespace Sweep\Processors\Directory;

use Sweep\Model\SweepDirectory;
use Sweep\Model\SweepItem;
use MODX\Revolution\Processors\Processor;

class Remove extends Processor
{
    public $objectType = 'SweepDirectory';
    public $classKey = SweepDirectory::class;
    public $languageTopics = ['sweep'];
    //public $permission = 'remove';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = json_decode($this->getProperty('ids'), true);
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('sweep_item_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var SweepItem $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('sweep_item_err_nf'));
            }

            $path = trim($object->path, ' /');
            if ($object->remove() && !empty($path)) {
                $itemTable = $this->modx->getTableName(SweepItem::class);
                $sql = "DELETE FROM $itemTable WHERE `path` LIKE \"/$path/%\"";
                $stmt = $this->modx->prepare($sql);
                $stmt->execute();
            }
        }

        return $this->success();
    }
}
