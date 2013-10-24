Ext.define('ecopyahuMovil.store.Categorias',{
    extend: 'Ext.data.Store',
    requires: [
        'Ext.data.JsonP',
        'Ext.data.proxy.JsonP',
        'ecopyahuMovil.model.Categorias'
    ],
    config: {
        model: 'ecopyahuMovil.model.Categorias',
        proxy: {
            type: 'jsonp',
            //url: ecopyahuMovil.app.app_url + 'denuncias_movil/getCategorias',
            url: app_url + 'denuncias_movil/getCategorias',
            reader: {
                type: 'json',
                rootProperty: 'data',
                totalProperty: 'cantidad_total'
            }            
        },
        autoLoad: true
    }
});