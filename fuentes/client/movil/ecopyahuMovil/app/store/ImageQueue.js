Ext.define('ecopyahuMovil.store.ImageQueue',{
    extend: 'Ext.data.Store',
    xtype: 'imagesqueue',
    requires: [
        'Ext.data.proxy.LocalStorage'
    ],
    config: {
	fields:['timestamp','src','fail'],
	storeId: 'theImageQueue',
	autoLoad: true,
	proxy: {
	    type:'localstorage',
	    id: 'idImagesQueue',
            reader: {
                type: 'json'
            }
        }
    }
});