Ext.define('ecopyahuMovil.controller.Mapa.Main', {
    extend: 'Ext.app.Controller',

    config: {
        refs: {
            mapCmp: '#leafletmap'
        },
        control: {
            mapCmp: {
                maprender: 'onMapRender',
                zoomend: 'onZoomEnd',
                movestart: 'onMoveStart',
                moveend: 'onMoveEnd'
            }
        }
    },

    onMapRender: function(p_component, p_map, p_layer) {
        console.log("map render");
    },
            
    onZoomEnd: function(p_component, p_map, p_layer, p_zoom) {
        console.log("zoom end -> new zoom level: " + p_zoom);
    },
            
    onMoveStart: function(p_component, p_map, p_layer) {
        console.log("move start");
    },
            
    onMoveEnd: function(p_component, p_map, p_layer) {
        console.log("move end");
    }
});