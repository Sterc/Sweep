<?php

namespace Sweep\Processors\Item;

use MODX\Revolution\Processors\Model\GetProcessor;
use Sweep\Model\SweepItem;

class Get extends GetProcessor
{
    public $objectType = 'SweepItem';
    public $classKey = SweepItem::class;
    public $languageTopics = ['sweep:default'];
    //public $permission = 'view';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }
}
