<?php

namespace Sweep\Processors\Directory;

use MODX\Revolution\Processors\Model\GetProcessor;
use Sweep\Model\SweepDirectory;

class Get extends GetProcessor
{
    public $objectType = 'SweepDirectory';
    public $classKey = SweepDirectory::class;
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
