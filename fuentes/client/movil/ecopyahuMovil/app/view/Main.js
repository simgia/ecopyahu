Ext.define("ecopyahuMovil.view.Main", {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Ext.TitleBar',
        'Ext.Button',
        'Ext.Label',
        'Ext.Img',
        'Ext.ux.field.FullScreenTextArea',
        'ecopyahuMovil.model.Denuncias.Categorias',
        'ecopyahuMovil.store.Denuncias.Categorias'
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
            xtype: 'selectfield',
            labelAlign: 'top',
            label: 'Seleccione una categor\u00eda', 
            displayField: 'categoria_nombre',
            valueField: 'categoria_id',
            //store: Ext.create('ecopyahuMovil.store.Denuncias.Categorias'),
            //store: 'CategoriasID'
            options: [
                {categoria_nombre: 'Contaminaci\u00f3n ambiental',  categoria_id: 1},
                {categoria_nombre: 'Contaminaci\u00f3n sonora', categoria_id: 2},
                {categoria_nombre: 'Poluci\u00f3n visual',  categoria_id: 3},
                {categoria_nombre: 'Espacios p\u00fablicos',  categoria_id: 4},
                {categoria_nombre: 'Animales sueltos',  categoria_id: 5},
                {categoria_nombre: 'Otros',  categoria_id: 6}
            ]
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
            action: 'enviar_denuncia',
            ui: 'action rounded',
            height: 50,
            margin: '20 10'
            //style: 'margin-bottom:1em;'
        }]
    }
});