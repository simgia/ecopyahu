Ext.define("ecopyahuMovil.view.Main", {
    extend: 'Ext.Panel',
    requires: [
        'Ext.TitleBar',
        'Ext.Button',
        'Ext.Label',
        'Ext.Img'
    ],
    xtype: 'mainviewport',
    config: {
        fullscreen: true,
        cls: 'snapp',
        styleHtmlContent: true,
        scrollable: true,
        items: [{
            docked: 'top',
            xtype: 'titlebar',
            title: 'Ecopyahu'
        },{
            xtype: 'capturarImagen'
        }/*,{
            xtype: 'button',
            html: '<h1 style="color:#FFF">Mapa</h1>',
            action: 'marcar_mapa',
            ui: 'action rounded',
            height: 100,
            style: 'margin-bottom:1em;'
        }*/,{
            xtype: 'label',
            id: 'hconsole'
            //html: 'log:'
        },{
            xtype: 'container',
            layout: 'fit',
            margin: '10 0 0 0',
            itemId: 'imgContainer',
            items: [{
                xtype : 'image',
                itemId: 'img',
                height: 400
            }]
        },{
            xtype: 'button',
            html: '<h1 style="color:#FFF">Enviar</h1>',
            action: 'enviar_denuncia',
            ui: 'action rounded',
            height: 100,
            style: 'margin-bottom:1em;'
        }]
    }
});