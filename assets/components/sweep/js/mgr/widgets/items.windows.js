Sweep.window.CreateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-item-window-create';
    }
    Ext.applyIf(config, {
        title: _('sweep_item_create'),
        width: 550,
        autoHeight: true,
        url: Sweep.config.connector_url,
        action: 'Sweep\\Processors\\Item\\Create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    Sweep.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(Sweep.window.CreateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('sweep_item_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textarea',
            fieldLabel: _('sweep_item_path'),
            name: 'path',
            id: config.id + '-path',
            height: 150,
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
Ext.reg('sweep-item-window-create', Sweep.window.CreateItem);


Sweep.window.UpdateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-item-window-update';
    }
    Ext.applyIf(config, {
        title: _('sweep_item_update'),
        width: 550,
        autoHeight: true,
        url: Sweep.config.connector_url,
        action: 'Sweep\\Processors\\Item\\Update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    Sweep.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(Sweep.window.UpdateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('sweep_item_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textarea',
            fieldLabel: _('sweep_item_path'),
            name: 'path',
            id: config.id + '-path',
            anchor: '99%',
            height: 150,
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
Ext.reg('sweep-item-window-update', Sweep.window.UpdateItem);

Sweep.window.Console = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        title: config.title || 'Console',
        width: config.width || '600px',
        layout: 'fit',
        closeAction: 'hide',
        maximizable: true,
        minimizable: true,
        items: [{
            xtype: 'textarea',
            id: config.textareaId || 'custom-console-textarea',
            style: 'font-family: monospace; font-size: 12px; border: 0;',
            height: config.height || '350px',
            readOnly: true,
            enableKeyEvents: false,
            border: false
        }]
    });

    Sweep.window.Console.superclass.constructor.call(this, config);

    this.textareaId = config.textareaId || 'custom-console-textarea';
};

Ext.extend(Sweep.window.Console, Ext.Window, {
    append: function(text) {
        var textarea = Ext.getCmp(this.textareaId);
        if (textarea) {
            var current = textarea.getValue();
            textarea.setValue(current + (current ? "\n" : '') + text);
            textarea.el.dom.scrollTop = textarea.el.dom.scrollHeight;
        }
    },

    clear: function() {
        var textarea = Ext.getCmp(this.textareaId);
        if (textarea) {
            textarea.setValue('');
        }
    }
});

Ext.reg('sweep-window-console', Sweep.window.Console);
