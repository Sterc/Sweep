<?php

namespace Sweep\Processors\Directory;

use MODX\Revolution\Processors\Model\CreateProcessor;
use Sweep\Model\SweepDirectory;

class Create extends CreateProcessor
{
    public $objectType = 'SweepDirectory';
    public $classKey = SweepDirectory::class;
    public $languageTopics = ['sweep'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $path = trim($this->getProperty('path'), ' /');
        if (empty($path)) {
            $this->modx->error->addField('path', $this->modx->lexicon('sweep_item_err_name'));
        }

        $path = $path . '/';
        if ($this->modx->getCount($this->classKey, ['path' => $path])) {
            $this->modx->error->addField('path', $this->modx->lexicon('sweep_item_err_ae'));
        }

        $this->setProperty('path', $path);

        return parent::beforeSet();
    }
}
