<?php

namespace Sweep\Processors\Item;

use Sweep\Model\SweepItem;
use MODX\Revolution\Processors\Processor;

class Remove extends Processor
{
    public $objectType = 'SweepItem';
    public $classKey = SweepItem::class;
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
        $where = ['usedin:IS' => null, 'OR:usedin:=' => ''];

        $ids = json_decode($this->getProperty('ids'), true);
        if (!empty($ids)) {
            $where['id:IN'] = $ids;
        }

        if (!$objects = $this->modx->getIterator($this->classKey, $where)) {
            return $this->failure($this->modx->lexicon('sweep_item_err_nf'));
        }

        /** @var SweepItem $object */
        foreach ($objects as $object) {
            $path = MODX_BASE_PATH . ltrim($object->path, '/');
            if (file_exists($path)) {
                if (@unlink($path)) {
                    $object->remove();
                } else {
                    return $this->failure($object->path . ': ' . $this->modx->lexicon('sweep_item_err_file_remove'));
                }
            } else {
                $object->remove();
                return $this->failure($this->modx->lexicon('sweep_item_err_file_nf'));
            }
        }

        return $this->success();
    }
}
