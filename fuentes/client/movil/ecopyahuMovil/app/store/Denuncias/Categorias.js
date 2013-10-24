Ext.define('ecopyahuMovil.store.Denuncias.Categorias',{
    extend: 'Ext.data.Store',
    requires: [
        'Ext.data.JsonP',
        'Ext.data.proxy.JsonP',
        'ecopyahuMovil.model.Denuncias.Categorias'
    ],
    config: {
        model: 'ecopyahuMovil.model.Denuncias.Categorias',
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