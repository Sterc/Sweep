Sweep.grid.Used = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sweep-grid-used';
    }

    let SelectionModel = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        url: Sweep.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(SelectionModel),
        tbar: this.getTopBar(config),
        sm: SelectionModel,
        baseParams: {
            action: 'Sweep\\Processors\\Item\\GetList',
            used: 1
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
        showActionsColumn: false
    });
    Sweep.grid.Used.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function (store, records, success) {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }

        const raw = store.reader.jsonData;
    
        if (raw && raw.total_size !== undefined && raw.unused_size !== undefined) {
            const total = raw.total;
            const used = Sweep.utils.renderSize(raw.used_size);

            Ext.getCmp('sweep-info-total-used').setText(`<div class="topbar-text">${_('sweep_total_found')} ${total} ${_('sweep_used_files')} (${_('sweep_total_size')} <b>${used}</b>)</div>`);
        } else {
            Ext.getCmp('sweep-info-total-used').setText('—');
        }
    }, this);
};
Ext.extend(Sweep.grid.Used, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        const ids = this._getSelectedIds();

        const row = grid.getStore().getAt(rowIndex);
        const menu = Sweep.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    getFields: function () {
        return ['id', 'name', 'path', 'usedin', 'size', 'active', 'actions'];
    },

    getColumns: function (SelectionModel) {
        return [
        /* SelectionModel,*/
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
            width: 150,
        }, {
            header: _('sweep_item_path'),
            dataIndex: 'path',
            sortable: true,
            width: 220,
        }, {
            header: _('sweep_item_size'),
            dataIndex: 'size',
            align: 'right',
            renderer: Sweep.utils.renderSize,
            sortable: true,
            width: 70
        }, {
            header: _('sweep_item_usedin'),
            dataIndex: 'usedin',
            sortable: true,
            width: 220,
        }, /*{
            header: _('sweep_item_active'),
            dataIndex: 'active',
            renderer: Sweep.utils.renderBoolean,
            sortable: true,
            width: 100,
        },*/ /* {
            header: _('sweep_grid_actions'),
            dataIndex: 'actions',
            renderer: Sweep.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actions'
        }*/];
    },

    getTopBar: function () {
        return [/* {
            text: '<i class="icon icon-plus"></i>&nbsp;&nbsp;' + _('sweep_item_create'),
            handler: this.createItem,
            scope: this
        }, */ {
            xtype: 'tbtext',
            id: 'sweep-info-total-used',
            text: '',
            style: 'display: flex; align-items: center; height: 34px;'
        }, '->', {
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
Ext.reg('sweep-grid-used', Sweep.grid.Used);
