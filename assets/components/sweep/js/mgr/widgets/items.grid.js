Sweep.grid.Items = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-grid-items';
    }

    let SelectionModel = new Ext.grid.CheckboxSelectionModel();

    SelectionModel.on('selectionchange', function () {
        this.updateInfoBar();
    }, this);

    Ext.applyIf(config, {
        url: Sweep.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(SelectionModel),
        tbar: this.getTopBar(config),
        sm: SelectionModel,
        baseParams: {
            action: 'Sweep\\Processors\\Item\\GetList',
            used: 0
        },
        listeners: {
            /*
            rowDblClick: function (grid, rowIndex, e) {
                const row = grid.store.getAt(rowIndex);
                this.updateItem(grid, e, row);
            }
            */
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                return !rec.data.active
                    ? 'sweep-grid-row-disabled'
                    : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    Sweep.grid.Items.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function (store, records, success) {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    
        this.updateInfoBar();
    }, this);
};
Ext.extend(Sweep.grid.Items, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        const ids = this._getSelectedIds();

        const row = grid.getStore().getAt(rowIndex);
        const menu = Sweep.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    scanFiles: function(btn, e) {
        if (!this.console) {
            this.console = MODx.load({
                xtype: 'sweep-window-console',
                title: _('sweep_item_scan')
            });
        }
    
        this.console.show(Ext.getBody());
        this.console.clear();
        this.console.append(_('console_running'));

        var start = 0;
        var limit = 10;

        var runStep = function(start) {
            MODx.Ajax.request({
                url: Sweep.config.connectorUrl,
                params: {
                    action: 'Sweep\\Processors\\Item\\Scan',
                    start: start,
                    limit: limit
                },
                listeners: {
                    success: {fn: function(r) {
                        if (r.success && r.object) {
                            if (r.object.messages && r.object.messages.length) {
                                Ext.each(r.object.messages, function(msg) {
                                    this.console.append(msg);
                                }, this);
                            }
                            if (r.object.finished) {
                                this.console.append(_('sweep_item_scan_complete'));
                                Ext.defer(function() {
                                    this.refresh();

                                    const gridUsed = Ext.getCmp('sweep-grid-used');
                                    if (gridUsed) {
                                        gridUsed.getStore().reload();
                                    }

                                    const gridDirs = Ext.getCmp('sweep-grid-directories');
                                    if (gridDirs) {
                                        gridDirs.getStore().reload();
                                    }
                                }, 1000, this);
                            } else {
                                runStep.call(this, start + limit);
                            }
                        } else {
                            this.console.append(_('sweep_item_scan_error'));
                        }
                    }, scope: this},
                    failure: {fn: function(r) {
                        this.console.append(_('sweep_item_scan_error'));
                    }, scope: this}
                }
            });
        };

        runStep.call(this, start);
    },

    createItem: function (btn, e) {
        const w = MODx.load({
            xtype: 'sweep-item-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues({active: true});
        w.show(e.target);
    },

    updateItem: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        const id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'Sweep\\Processors\\Item\\Get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        const w = MODx.load({
                            xtype: 'sweep-item-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    removeItem: function () {
        const ids = this._getSelectedIds();
        if (ids.length > 0) {
            MODx.msg.confirm({
                title: ids.length > 1
                    ? _('sweep_items_remove')
                    : _('sweep_item_remove'),
                text: ids.length > 1
                    ? _('sweep_items_remove_confirm')
                    : _('sweep_item_remove_confirm'),
                url: this.config.url,
                params: {
                    action: 'Sweep\\Processors\\Item\\Remove',
                    ids: Ext.util.JSON.encode(ids),
                },
                listeners: {
                    success: {
                        fn: function () {
                            this.refresh();
                        }, scope: this
                    }
                }
            });
        } else {
            MODx.msg.confirm({
                title: _('sweep_items_remove_all'),
                text: _('sweep_items_remove_all_confirm'),
                url: this.config.url,
                params: {
                    action: 'Sweep\\Processors\\Item\\Remove',
                },
                listeners: {
                    success: {
                        fn: function () {
                            this.refresh();
                        }, scope: this
                    }
                }
            });
        }
        return true;
    },

    arciveItem: function () {
        const ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'Sweep\\Processors\\Item\\Archive',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    disableItem: function () {
        const ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'Sweep\\Processors\\Item\\Disable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    enableItem: function () {
        const ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'Sweep\\Processors\\Item\\Enable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    getFields: function () {
        return ['id', 'name', 'path', 'size', 'active', 'actions'];
    },

    getColumns: function (SelectionModel) {
        return [
            SelectionModel,
        /*{
            header: _('sweep_item_id'),
            dataIndex: 'id',
            sortable: true,
            width: 70
        },*/ {
            header: _('sweep_item_name'),
            dataIndex: 'name',
            sortable: false,
            renderer: function (value, props, row) {
                return '<a href="' + row.data.path + '" target="_blank">' + value + '</a>';
            },
            width: 200,
        }, {
            header: _('sweep_item_path'),
            dataIndex: 'path',
            sortable: true,
            width: 250,
        }, {
            header: _('sweep_item_size'),
            dataIndex: 'size',
            align: 'right',
            renderer: Sweep.utils.renderSize,
            sortable: true,
            width: 70
        }, /*{
            header: _('sweep_item_active'),
            dataIndex: 'active',
            renderer: Sweep.utils.renderBoolean,
            sortable: true,
            width: 100,
        },*/ {
            header: _('sweep_grid_actions'),
            dataIndex: 'actions',
            renderer: Sweep.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actions'
        }];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-refresh"></i>&nbsp;&nbsp;' + _('sweep_item_scan'),
            handler: this.scanFiles,
            cls: 'primary-button',
            scope: this
        }, /* {
            text: '<i class="icon icon-plus"></i>&nbsp;&nbsp;' + _('sweep_item_create'),
            handler: this.createItem,
            scope: this
        }, */ '->', {
            xtype: 'tbtext',
            id: 'sweep-info-total-unused',
            text: '',
            style: 'display: flex; align-items: center; height: 34px;'
        }, {
            text: '<i class="icon icon-trash-o"></i>&nbsp;&nbsp;' + _('sweep_items_remove_all'),
            id: 'sweep-button-remove',
            handler: this.removeItem,
            cls: 'red',
            style: 'margin-right: 20px;',
            scope: this
        }, {
            xtype: 'sweep-field-search',
            width: 250,
            listeners: {
                search: {
                    fn: function (field) {
                        this._doSearch(field);
                    }, scope: this
                },
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this._clearSearch();
                    }, scope: this
                },
            }
        }];
    },

    onClick: function (e) {
        const elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            const row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                const action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    const ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    updateInfoBar: function () {
        const sm = this.getSelectionModel();
        const selected = sm.getSelections();
        const count = selected.length;
        const infoBlock = Ext.getCmp('sweep-info-total-unused');
        const btnRemove = Ext.getCmp('sweep-button-remove');
    
        if (count > 0) {
            let totalSize = 0;
            for (let i = 0; i < count; i++) {
                totalSize += parseInt(selected[i].data.size) || 0;
            }
            const renderedSize = Sweep.utils.renderSize(totalSize);
    
            infoBlock.setText(`<div class="topbar-text">${_('sweep_selected')} <b>${count}</b> ${_('sweep_unused_files')} (${_('sweep_total_size')} <b>${renderedSize}</b>)</div>`);
            btnRemove.setText('<i class="icon icon-trash-o"></i>&nbsp;&nbsp;' + _('sweep_items_remove'));
        } else {
            const raw = this.store.reader.jsonData;
            if (raw && raw.total_size !== undefined && raw.unused_size !== undefined) {
                const total = raw.total;
                const unused = Sweep.utils.renderSize(raw.unused_size);
                infoBlock.setText(`<div class="topbar-text">${_('sweep_total_found')} ${total} ${_('sweep_unused_files')} (${_('sweep_total_size')} <b>${unused}</b>)</div>`);
            } else {
                infoBlock.setText('—');
            }

            btnRemove.setText('<i class="icon icon-trash-o"></i>&nbsp;&nbsp;' + _('sweep_items_remove_all'));
        }
    },

    _getSelectedIds: function () {
        const ids = [];
        const selected = this.getSelectionModel().getSelections();

        for (const i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },
});
Ext.reg('sweep-grid-items', Sweep.grid.Items);
