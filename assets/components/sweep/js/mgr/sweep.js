let Sweep = function (config) {
    config = config || {};
    Sweep.superclass.constructor.call(this, config);
};
Ext.extend(Sweep, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('sweep', Sweep);

Sweep = new Sweep();