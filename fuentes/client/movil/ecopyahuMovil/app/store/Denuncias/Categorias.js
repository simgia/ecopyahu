Ext.define('ecopyahuMovil.store.Denuncias.Categorias',{
    extend: 'Ext.data.Store',
    config: {
        model: 'ecopyahuMovil.model.Denuncias.Categorias',
        proxy: {
            type: 'ajax',
            url: 'http://192.168.1.152/denuncias_movil/getCategorias',
            reader: {
                type: 'json',
                rootProperty: 'data',
                //totalProperty: 'cantidadTotal'
                totalProperty: 'cantidad_total'
            }
        },
        autoLoad: true
    }
});