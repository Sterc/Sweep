Sweep.window.CreateDirectory = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-directory-window-create';
    }
    Ext.applyIf(config, {
        title: _('sweep_directory_create'),
        width: 550,
        autoHeight: true,
        url: Sweep.config.connector_url,
        action: 'Sweep\\Processors\\Directory\\Create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    Sweep.window.CreateDirectory.superclass.constructor.call(this, config);
};
Ext.extend(Sweep.window.CreateDirectory, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('sweep_item_path'),
            name: 'path',
            id: config.id + '-path',
            anchor: '99%'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('sweep_item_active'),
            name: 'active',
            id: config.id + '-active',
            checked: true,
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('sweep-directory-window-create', Sweep.window.CreateDirectory);


Sweep.window.UpdateDirectory = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-directory-window-update';
    }
    Ext.applyIf(config, {
        title: _('sweep_directory_update'),
        width: 550,
        autoHeight: true,
        url: Sweep.config.connector_url,
        action: 'Sweep\\Processors\\Directory\\Update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    Sweep.window.UpdateDirectory.superclass.constructor.call(this, config);
};
Ext.extend(Sweep.window.UpdateDirectory, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('sweep_item_path'),
            name: 'path',
            id: config.id + '-path',
            anchor: '99%',
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('sweep_item_active'),
            name: 'active',
            id: config.id + '-active',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('sweep-directory-window-update', Sweep.window.UpdateDirectory);
