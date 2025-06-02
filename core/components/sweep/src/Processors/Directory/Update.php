<?php

namespace Sweep\Processors\Directory;

use Sweep\Model\SweepDirectory;
use MODX\Revolution\Processors\Model\UpdateProcessor;

class Update extends UpdateProcessor
{
    public $objectType = 'SweepDirectory';
    public $classKey = SweepDirectory::class;
    public $languageTopics = ['sweep'];
    //public $permission = 'save';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        if (empty($id)) {
            return $this->modx->lexicon('sweep_directory_err_ns');
        }

        $path = trim($this->getProperty('path'), ' /');
        if (empty($path)) {
            $this->modx->error->addField('path', $this->modx->lexicon('sweep_directory_err_name'));
        }

        $path = $path . '/';
        if ($this->modx->getCount($this->classKey, ['path' => $path, 'id:!=' => $id])) {
            $this->modx->error->addField('path', $this->modx->lexicon('sweep_directory_err_ae'));
        }

        $this->setProperty('path', $path);

        return parent::beforeSet();
    }
}
