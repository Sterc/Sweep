<?php

namespace Sweep\Processors\Directory;

use Sweep\Model\SweepDirectory;
use MODX\Revolution\Processors\Processor;

class Disable extends Processor
{
    public $objectType = 'SweepDirectory';
    public $classKey = SweepDirectory::class;
    public $languageTopics = ['sweep'];
    //public $permission = 'save';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('sweep_directory_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var SweepItem $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('sweep_directory_err_nf'));
            }

            $object->set('active', false);
            $object->save();
        }

        return $this->success();
    }
}
