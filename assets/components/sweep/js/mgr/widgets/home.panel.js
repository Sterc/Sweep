Sweep.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'sweep-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('sweep') + '</h2>',
            cls: 'modx-page-header',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('sweep_items'),
                layout: 'anchor',
                items: [{
                    html: _('sweep_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'sweep-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    Sweep.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(Sweep.panel.Home, MODx.Panel);
Ext.reg('sweep-panel-home', Sweep.panel.Home);
