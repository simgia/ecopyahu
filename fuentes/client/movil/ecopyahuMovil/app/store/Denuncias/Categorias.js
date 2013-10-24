Ext.define('ecopyahuMovil.store.Denuncias.Categorias',{
    extend: 'Ext.data.Store',
    requires: [
        'Ext.data.JsonP'
    ],
    config: {
        model: 'ecopyahuMovil.model.Denuncias.Categorias',
        storeId: 'CategoriasID',
        proxy: {
            type: 'jsonp',
            url: 'http://192.168.1.152/denuncias_movil/getCategorias',
            reader: {
                type: 'json',
                rootProperty: 'data',
                //totalProperty: 'cantidadTotal'
                totalProperty: 'cantidad_total'
            },
            callbackKey : 'callback'
        },
        autoLoad: true
    }
});