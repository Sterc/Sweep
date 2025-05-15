Sweep.window.CreateDirectory = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-directory-window-create';
    }
    Ext.applyIf(config, {
        title: _('sweep_item_create'),
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
        title: _('sweep_item_update'),
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

Sweep.window.Console = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        title: config.title || 'Console',
        width: config.width || '960px',
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
