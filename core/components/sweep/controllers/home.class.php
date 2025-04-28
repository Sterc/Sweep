<?php

use MODX\Revolution\modExtraManagerController;

/**
 * The home manager controller for Sweep.
 *
 */
class SweepHomeManagerController extends modExtraManagerController
{
    /** @var Sweep\Sweep $Sweep */
    public $Sweep;


    /**
     *
     */
    public function initialize()
    {
        $this->Sweep = $this->modx->services->get('Sweep');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['sweep:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('sweep');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->Sweep->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/sweep.js');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->Sweep->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        Sweep.config = ' . json_encode($this->Sweep->config) . ';
        Sweep.config.connector_url = "' . $this->Sweep->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "sweep-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="sweep-panel-home-div"></div>';
        return '';
    }
}
