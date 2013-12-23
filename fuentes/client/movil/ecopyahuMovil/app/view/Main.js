Ext.define("ecopyahuMovil.view.Main", {
    extend: 'Ext.form.FormPanel',
    requires: [ 'Ext.TitleBar', 'Ext.Button', 'Ext.Label', 'Ext.Img',
		'Ext.ux.field.FullScreenTextArea',
		'ecopyahuMovil.model.Denuncias.Categorias',
		'ecopyahuMovil.store.Denuncias.Categorias' ],
    xtype: 'mainviewport',
    config: {
        fullscreen: true,
	cls: 'snapp',
	styleHtmlContent: true,
	scrollable: true,
	items: [{
            docked: 'top',
            xtype: 'titlebar',
            //title : 'ecoPYahu',
            html: '<center><table><tr><td><img src="resources/icons/ico_ecopyahu36.png"></td><td> ecoPYahu</td></tr></table></center>' ,
            style: 'color:white;'
            /*  items:[{
                html:src:'resources/icons/ico_ecopyahu36.png'
            }]*/
        }, {
            xtype: 'capturarImagen'
	}, {
            xtype: 'label',
            id: 'hconsole'
            // html: 'log:'
	}, {
            xtype: 'container',
            layout: 'fit',
            margin: '10 0 0 0',
            itemId: 'imgContainer',
            items: [{
		xtype: 'image',
		itemId: 'img',
		height: 200
            }]
	}, {
            xtype: 'selectfield',
            labelAlign: 'top',
            label: 'Seleccione una categor\u00eda',
            displayField: 'categoria_nombre',
            valueField: 'categoria_id',
            required: true,
            // store: Ext.create('ecopyahuMovil.store.Denuncias.Categorias'),
            options: [{
                categoria_nombre: 'Contaminaci\u00f3n ambiental',
		categoria_id: 1
            }, {
		categoria_nombre: 'Contaminaci\u00f3n sonora',
		categoria_id: 2
            }, {
		categoria_nombre: 'Poluci\u00f3n visual',
		categoria_id: 3
            }, {
		categoria_nombre: 'Espacios p\u00fablicos',
		categoria_id: 4
            }, {
            	categoria_nombre: 'Animales sueltos',
		categoria_id: 5
            }, {
		categoria_nombre: 'Otros',
		categoria_id: 6
            }]
        }, {
            xtype: 'textareafield',
            label: 'Descripci\u00f3n',
            itemId: 'textarea_descripcion',
            maxRows: 4,
            labelAlign: 'top',
            name: 'descripcion',
            required: true
	}, {
            xtype: 'button',
            //html: '<h3 style="color:#FFF;">Enviar</h3>',
            text: 'Enviar',
            style: "color: #FFF",
            action: 'enviar_denuncia',
            ui: 'action rounded',
            height: 50,
            margin: '20 10'
	}]
    }
});