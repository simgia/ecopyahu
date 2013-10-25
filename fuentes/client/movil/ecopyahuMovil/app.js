/*
    This file is generated and updated by Sencha Cmd. You can edit this file as
    needed for your application, but these edits will have to be merged by
    Sencha Cmd when it performs code generation tasks such as generating new
    models, controllers or views and when running "sencha app upgrade".

    Ideally changes to this file would be limited and most work would be done
    in other places (such as Controllers). If Sencha Cmd cannot merge your
    changes and its generated code, it will produce a "merge conflict" that you
    will need to resolve manually.
*/
//var app_url = 'http://ecopyahu/';
//var app_url = 'http://192.168.1.155/';
var app_url = 'http://ecopyahu.simgia.com/';
Ext.application({
    name: 'ecopyahuMovil',

    requires: [
        'Ext.MessageBox',
        'Ext.device.Camera',
        'Ext.device.Connection',
        'Ext.data.JsonP',
        'ecopyahuMovil.store.Denuncias.Categorias',
        'Ext.data.proxy.JsonP',
        'Ext.field.Select'
    ],

    views: [
        'Main',
        'Mapa.Main',
        'Denuncias.Camara'
    ],
    controllers: [
        'Main',
        'Mapa.Main'
    ],
    stores: [
        'Denuncias.Categorias'
    ],
    icon: {
        '57': 'resources/icons/Icon.png',
        '72': 'resources/icons/Icon~ipad.png',
        '114': 'resources/icons/Icon@2x.png',
        '144': 'resources/icons/Icon~ipad@2x.png'
    },

    isIconPrecomposed: true,
    
    app_url: app_url,

    startupImage: {
        '320x460': 'resources/startup/320x460.jpg',
        '640x920': 'resources/startup/640x920.png',
        '768x1004': 'resources/startup/768x1004.png',
        '748x1024': 'resources/startup/748x1024.png',
        '1536x2008': 'resources/startup/1536x2008.png',
        '1496x2048': 'resources/startup/1496x2048.png'
    },

    launch: function() {
        // Destroy the #appLoadingIndicator element
        Ext.fly('appLoadingIndicator').destroy();

        // Initialize the main view
        Ext.Viewport.add(Ext.create('ecopyahuMovil.view.Main'));
        //Ext.Viewport.add(Ext.create('ecopyahuMovil.view.Mapa.Main'))
    },

    onUpdated: function() {
        Ext.Msg.confirm(
            "Application Update",
            "This application has just successfully been updated to the latest version. Reload now?",
            function(buttonId) {
                if (buttonId === 'yes') {
                    window.location.reload();
                }
            }
        );
    }
});

var oneReady = false;       // This is for checking who loaded first.

function checkConnection(){
    
    console.log("entro checkConnection1");
    if(!navigator.network){
        return 'UNKNOWN';
    }  
    console.log("entro checkConnection2");
    var v_networkState = navigator.network.connection.type;
    
    console.log(v_networkState);
    console.log("entro checkConnection3");
    
    var v_states = {};
        v_states[Connection.UNKNOWN] = 'UNKNOWN';
        v_states[Connection.ETHERNET] = 'ETHERNET';
        v_states[Connection.WIFI] = 'WIFI';
        v_states[Connection.CELL_2G] = 'CELL_2G';
        v_states[Connection.CELL_3G] = 'CELL_3G';
        v_states[Connection.CELL_4G] = 'CELL_4G';
        v_states[Connection.NONE] = 'NONE';
    
    return v_states[v_networkState];
}

function onDeviceReady(){
    if(oneReady){
        ecopyahuMovil.app.getController('Main').hola();
    }else{
         oneReady = true;
    }
}
document.addEventListener("deviceready", onDeviceReady, false);
