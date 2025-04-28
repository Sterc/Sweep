Sweep.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'sweep-panel-home',
            renderTo: 'sweep-panel-home-div'
        }]
    });
    Sweep.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(Sweep.page.Home, MODx.Component);
Ext.reg('sweep-page-home', Sweep.page.Home);