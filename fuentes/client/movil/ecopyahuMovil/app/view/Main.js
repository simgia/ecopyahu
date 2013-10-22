Ext.define("ecopyahuMovil.view.Main", {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Ext.TitleBar',
        'Ext.Button',
        'Ext.Label',
        'Ext.Img',
        'Ext.ux.field.FullScreenTextArea'
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
                height: 200
            }]
        },{
            xtype: 'fieldset',
            title: 'Seleccione una subcategor\u00eda', 
            items: [{
                xtype: 'selectfield',
                options: [
                    {text: 'Contaminaci\u00f3n ambiental',  value: 'contaminacion_ambiental'},
                    {text: 'Contaminaci\u00f3n sonora', value: 'contaminacion_sonora'},
                    {text: 'Poluci\u00f3n visual',  value: 'polucion_visual'},
                    {text: 'Espacios p\u00fablicos',  value: 'espacios_publicos'},
                    {text: 'Animales sueltos',  value: 'animales_sueltos'},
                    {text: 'Otros',  value: 'otros'}
                ]
            }]
        },{
            /*
            xtype: 'textareafield',
            label: 'Descripci\u00f3n',
            itemId: 'textarea_descripcion',
            maxRows: 4,
            name: 'descripcion'
            */
            xtype: 'fullscreentextarea',
            label: 'Descripci\u00f3n',
            itemId: 'textarea_descripcion',
            maxRows: 4,
            name: 'descripcion'
        },{
            xtype: 'button',
            html: '<h3 style="color:#FFF">Enviar</h3>',
            //text: 'Enviar',
            action: 'enviar_denuncia',
            ui: 'action rounded',
            height: 50,
            margin: '20 10'
            //style: 'margin-bottom:1em;'
        }]
    }
});