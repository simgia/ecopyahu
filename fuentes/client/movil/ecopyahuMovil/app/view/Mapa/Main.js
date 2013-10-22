Ext.define('ecopyahuMovil.view.Mapa.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'main',
    id: 'mainTabPanel',
    requires: [
        'Ext.ux.LeafletMap'
    ],
	
    config: {
        tabBar: {
            docked: 'bottom'
	},
        items: [{
            title: 'LeafletMap',
            iconCls: 'maps',
            layout: 'fit',
            items: [{
		xtype: 'leafletmap',
		id: 'leafletmap',
		useCurrentLocation: true,
		autoMapCenter: false,
                enableOwnPositionMarker: true,
		mapOptions: {
                    //center: new L.LatLng(-25.31941, -57.58146), // Asuncion en General.
                    zoom: 15
		}
            }]
        }]
    }
});