<?php

namespace Sweep\Processors\Item;

use MODX\Revolution\Processors\Model\CreateProcessor;
use Sweep\Model\SweepItem;

class Create extends CreateProcessor
{
    public $objectType = 'SweepItem';
    public $classKey = SweepItem::class;
    public $languageTopics = ['sweep'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('sweep_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('sweep_item_err_ae'));
        }

        return parent::beforeSet();
    }
}
