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
var app_url = 'http://192.168.1.155/';
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
    if(!navigator.network){
        return 'UNKNOWN';
    }  
    var networkState = navigator.network.connection.type;
    var states = {};
        states[Connection.UNKNOWN] = 'UNKNOWN';
        states[Connection.ETHERNET] = 'ETHERNET';
        states[Connection.WIFI] = 'WIFI';
        states[Connection.CELL_2G] = 'CELL_2G';
        states[Connection.CELL_3G] = 'CELL_3G';
        states[Connection.CELL_4G] = 'CELL_4G';
        states[Connection.NONE] = 'NONE';
    
    return states[networkState];
}

function onDeviceReady(){
    if(oneReady){
        ecopyahuMovil.app.getController('Main').hola();
    }else{
         oneReady = true;
    }
}
document.addEventListener("deviceready", onDeviceReady, false);
