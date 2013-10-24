<?php 
/**
 * @author jbauer
 * listar usuarios
 * */
$this->load->view('comunes/cabecera_ext');?>

<body id='pag_login' class='color_body'>
<?php $this->load->view('comunes/menu');?>
<!--CUERPO-->

<div id="cuerpo" class="wrap clearfix">

<div id="id-mensajes"></div> 
<br>
<h3>Usuarios</h3>
<div id="id-grid-usuarios"></div>


<script type='text/javascript'>
	var v_grid;
	var v_store;
	var v_toolbar;
	Ext.onReady(function(){
		v_toolbar = Ext.create('Ext.toolbar.Paging', {
		   	height : 37,
		   	displayMsg:'Mostrando {0} - {1} de {2}',
            emptyMsg:'No hay registros con el filtro especificado',
            firstText:'Primera P&aacute;gina',
            lastText:'&Uacute;ltima P&aacute;gina',
            nextText:'Siguiente P&aacute;gina',
            prevText:'P&aacute;gina Anterior',
            afterPageText:' de {0}',
            beforePageText:'P&aacute;gina',
		    items: ['->',
		        {
		            text: 'Activo/Inactivo',
		            action: 'cambiar_estado',
		            scale: 'medium',
		            hidden: true,
		            handler : function(){
						var v_seleccionado = v_grid.getSelectionModel().getLastSelected();
			            if(!v_seleccionado){
				            Ext.getDom('id-mensajes').style.cssText="color: red;";
							Ext.getDom('id-mensajes').innerHTML = "Debe seleccionar un usuario.";
							return;
				        }

			            if(v_grid.getSelectionModel().getLastSelected().data.USUARIO_ESTADO == 'activo')
			            	v_estado = 'inactivo';
			            else
			            	v_estado = 'activo';
			            Ext.MessageBox.show({
		    				title:'Se cambiar&aacute; de estado.',
		    				msg: '&iquest;Esta seguro de pasar a '+v_estado+' al usuario?',
		    				buttons: Ext.MessageBox.YESNO,
		    				//heigth: 150,
		    				closable: false,		// Se elimino el botton porque no se visualizaba bien.
		    				fn: function(p_btn){
		    					if(p_btn=='yes') {
		    		            	Ext.Ajax.request({
		    		            	    url: '<?php echo base_url();?>usuarios/cambiar_estado',
		    		            	    params: {
		    		            	        usuario_id: v_grid.getSelectionModel().getLastSelected().data.USUARIO_ID,
		    		            	        estado:v_estado
		    		            	    },
		    		            	    success: function(p_response){
		    		    					var v_respuesta = Ext.JSON.decode(p_response.responseText);
		    		    					if(v_respuesta.resultado) {
		    		    						Ext.getDom('id-mensajes').style.cssText="color: blue;";
		    									Ext.getDom('id-mensajes').innerHTML = "Se a cambiado de estado exitosamente.";
		    									v_store.load();
		    		    					}else{
		    		    						Ext.getDom('id-mensajes').style.cssText="color: red;";
		    									Ext.getDom('id-mensajes').innerHTML = "Ocurrio un error mientras se intentaba cambiar de estado.";
		    			    				}
		    		    				},
		    		    				failure: function(response){
		    		    					Ext.getDom('id-mensajes').style.cssText="color: red;";
		    								Ext.getDom('id-mensajes').innerHTML = "Ocurrio un error de conexión.";
		    		    				}
		    		            	});
		    					} 
		    				},
		    				//animEl: v_scope,
		    				icon: Ext.MessageBox.QUESTION
		    			});
			        }
		        }
		    ],
		    listeners: {
	               render: function(p_this) {
	                   p_this.down('#refresh').hide();
	               }
	           }
		});
		
		Ext.define('lista_usuario', {
		    extend: 'Ext.data.Model',
		    fields: [   {name:'USUARIO_ID',type:'int'},
		                {name:'USUARIO_EMAIL',type:'string'},
		                {name:'USUARIO_NOMBRE',type:'string'},
		                {name:'USUARIO_APELLIDO',type:'string'},
		                {name:'USUARIO_ESTADO',type:'string'},
		                {name:'USUARIO_FECHA_REGISTRO',type:'date'}
		                
		            ],
		    idProperty:'USUARIO_ID'
		});

		v_store = Ext.create('Ext.data.Store', {
			model: 'lista_usuario',
			autoLoad: true,
			proxy: {
		        type: 'ajax',
				api: {
					read: '/usuarios/consulta_usuarios'
				},
		        reader: {
		            type: 'json',
		            root: 'datos',
		            successProperty: 'success',
		            totalProperty: 'cantidadTotal'
		        }
		    },
		    pageSize: 200,
		    remoteSort: true
		});

		v_grid = Ext.create('Ext.grid.Panel', {
		    store: v_store,
		    columns: [
		        { header: 'Nombre',  dataIndex: 'USUARIO_NOMBRE', width: 150},
		        { header: 'Apellido',  dataIndex: 'USUARIO_APELLIDO', width: 200},
		        { header: 'Email', dataIndex: 'USUARIO_EMAIL', flex: 1 },
		        { header: 'Estado',  dataIndex: 'USUARIO_ESTADO' },
		        { header: 'F. Registro',  dataIndex: 'USUARIO_FECHA_REGISTRO', xtype:'datecolumn', format:'d/F/Y', width:150}
		    ],
		    height: 400,
		    bbar :v_toolbar,
		    renderTo: 'id-grid-usuarios',
		    listeners:{
			       deselect:function(p_rowmodel, p_record) {
	                   v_toolbar.down('button[action=cambiar_estado]').hide();
	               },
	               select: function(p_rowmodel, p_record) {
	                   var v_boton = v_toolbar.down('button[action=cambiar_estado]');
	                   
	                   if(!v_boton.isVisible() /*&& p_record.data.USUARIO_ESTADO!='pendiente'*/) {
	                       v_boton.show();
	                   }
	                   //console.log(p_record.data.USUARIO_ESTADO);
	                   if(p_record.data.USUARIO_ESTADO=='activo') {
	                       v_boton.setText('Inactivar');
	                   }
	                   
	                   if(p_record.data.USUARIO_ESTADO=='inactivo'||p_record.data.USUARIO_ESTADO=='pendiente') {
	                       v_boton.setText('Activar');
	                   }
	                   
	                   /*if(p_record.data.USUARIO_ESTADO=='pendiente' && v_boton.isVisible()) {
	                       v_boton.hide();
	                   }*/
	               }
	           }
		});
	});
</script>
</div>
<!--CUERPO--> 
<?php $this->load->view('comunes/pie')?>
</body>
</html>