Ext.define('ecopyahuMovil.controller.Main',{
    extend: 'Ext.app.Controller',
    requires: [
        'Ext.device.Camera',
        'Ext.device.Connection',
        'Ext.data.JsonP',
        'ecopyahuMovil.model.Denuncias.Categorias',
        'ecopyahuMovil.store.Denuncias.Categorias',
        'Ext.data.proxy.JsonP'
    ],

    /**
     * Se guarda la latitud actual.
     */ 
    latitud: null,
    
    /**
     * Se guarda la longitud actual.
     */ 
    longitud: null,
        
    config: {		
        refs: {
	    principal: 'mainviewport',
            Img: '#img',
            imgContainer: '#imgContainer'
        },
	control: {		
            'mainviewport button[action=marcar_mapa]': {
	        tap: 'abrirMapa'
	    },
            
            'mainviewport button[action=enviar_denuncia]': {
	        tap: 'enviarDenuncia'
	    },
            
            'capturarImagen': {
                sacar_foto: 'openCamera'
            }
	},
        areImagesUploading: false
    },

    /**
     * Metodo que abre la camara del dispositivo. 
     */
    openCamera: function(p_button, p_eve){
        var me = this;

        // Se obtiene la referencia de la imagen.        
        var v_imagen = me.getImg();
        
        Ext.device.Camera.capture({
            success: fotoExitosa,
            scope: this,
            quality : 85,
            source: 'camera',
            destinationType: 'file'
        });
        
        function fotoExitosa(p_imagen) {
            // Se obtiene la ruta de la imagen.
            var v_ruta_imagen = p_imagen; 
            v_imagen.setSrc(v_ruta_imagen);
        }
    },
      
    /**
     * Metodo que abre la ventana donde se encuentra el mapa (OSM).
     */
    abrirMapa: function(){
        alert("Entro Mapa");        
        // Initialize the main view
        // Ext.Viewport.add(Ext.create('DenunciasCamara.view.Mapa.Main'));
    },
            
    hola: function(){
        // This is our true launch function
        ecopyahuMovil.bothReady = true; 
        ecopyahuMovil.semiConsole = Ext.getCmp('hconsole'); // this is an "alias" for our log label defined in Main.js
    },
            
    addLog: function(toAdd){
        var msgta = ecopyahuMovil.semiConsole.getHtml()+'<br>'+toAdd;    
        ecopyahuMovil.semiConsole.setHtml(msgta);
        console.log( msgta );
    },
             
    launch: function(){
        if(oneReady || Ext.os.is.Desktop){
        	//if we are on desktop we assume there's no phonegap.
            this.hola();
        }else{
            oneReady = true;
        }
    },
    
    /**
     * Metodo que inicializa y obtiene la latitud y longitud del dispositivo movil.
     */
    init: function () {
        var v_scope = this;
        var onSuccess = function(position){
            // Se obtiene la latitud actual.
            v_scope.latitud = position.coords.latitude;
            
            // Se obtiene la longitud actual.
            v_scope.longitud = position.coords.longitude;
        };

        // onError Callback receives a PositionError object
        var onError = function(error){
            alert('código: ' + error.code + '\n' +
                'mensaje: ' + error.message + '\n');
        }
        
        navigator.geolocation.getCurrentPosition(onSuccess, onError);
    },
    
    /**
     * Metodo que envia una denuncia al servidor.
     */
    enviarDenuncia: function(){
        var v_scope = this;
        
        console.log("sa", v_scope.getApplication().getName());
        
        
        var v_formulario = v_scope.getPrincipal();
        var v_categoria = v_formulario.down('selectfield');
        //var v_denuncia_descripcion = v_formulario.down('fullscreentextarea');
        var v_denuncia_descripcion = v_formulario.down('textareafield');
        
        // Obtener los datos del GPS.
        var v_latitud = v_scope.latitud;
        var v_longitud = v_scope.longitud;
        
        Ext.data.JsonP.request({
            url: v_scope.getApplication().app_url + 'denuncias_movil/insertar_denuncia',
            params: {
                latitud: v_latitud,
                longitud: v_longitud,
                descripcion: v_denuncia_descripcion.getValue(),
                categoria: v_categoria.getValue(),
                fuente: 'movil'
            },
            //scope: this,
            success: function(p_response, p_options){
                if(p_response.resultado == true){
                    var v_denuncia_id = p_response.denuncia_id;
                    var v_imagen = v_scope.getImg(); 
                    var v_imagen_url = v_imagen.getSrc();
                    var v_win = function(){
                        Ext.Msg.alert('Exito', p_response.mensaje);
                    
                        // Se limpia el formulario.
                        v_scope.limpiarFormulario();
                    };
                    var v_fail = function(p_error){
                        //<debug>
                        console.log("An error has occurred: Code = " + p_error.code);
                        console.log("upload error source " + p_error.source);
                        console.log("upload error target " + p_error.target);
                        //</debug>
                        Ext.Msg.alert('Error','Codigo: '+ p_error.code);
                    };
                    var v_optiones_transferencia = new FileUploadOptions();

                    // Subida de archivos usando Cordova.
                    v_optiones_transferencia.fileKey = 'archivo_1';
                    v_optiones_transferencia.fileName = v_imagen_url;
                    v_optiones_transferencia.mimeType = "image/jpg";											        
                    v_optiones_transferencia.params = {
                        denuncia_id: v_denuncia_id
                    };
                    var v_ft = new FileTransfer();
                    
                    v_ft.upload(v_imagen_url, encodeURI(v_scope.getApplication().app_url + 'denuncias_movil/subirMultimedia'), v_win, v_fail, v_optiones_transferencia);
		}else{
		     Ext.Msg.alert('Error', p_response.mensaje);
                } 
            },
            failure: function(){
                //<debug>
                Ext.Msg.alert('Exito', 'Error en JsonP');
                console.log('Error en ajax');
		console.log(arguments);
		//</debug>			
            }
        });
    },
    
    /**
     * Metodo publico que limpia el formulario.
     */
    limpiarFormulario: function(){
        var v_scope = this;
        var v_formulario = v_scope.getPrincipal();
        
        // Se resetea la imagen.
        v_scope.resetImagen();

        // Limpia el formulario.
        v_formulario.reset(); 
    },
    
    /**
     * Metodo publico que resetea la imagen.
     */
    resetImagen: function(){
        var v_scope = this;
        var v_imagen = v_scope.getImg(); 
     
        v_imagen.destroy();
        
        // Crea una imagen vacia.
        v_scope.crearImagen();   
    },
    
    /**
     * Metodo que crea una imagen pero vacia.
     */
    crearImagen: function(){
        var v_scope = this;
        var v_imgContainer = v_scope.getImgContainer();
        var v_imagen;
        
        v_imagen = {
           xtype : 'image',
           itemId: 'img',
           height: 200       
        };

        v_imgContainer.add(v_imagen);
    },
    
    /**
     * @method tieneConexionInternet
     * Metodo publico que retorna true o false
     * para saber si tiene coneccion a internet.
     * @reutn boolean
     */
    tieneConexionInternet: function(){
        var v_scope = this;
        var v_nombre_aplicacion = v_scope.getApplication().getName();
        
        v_nombre_aplicacion.connectionType = checkConnection();
        if(v_nombre_aplicacion.connectionType == 'WIFI' || v_nombre_aplicacion.connectionType == 'CELL_3G'
            || v_nombre_aplicacion.connectionType == 'CELL_4G'){
            // Si se tiene coneccion wifi, 3g o 4g.
            return true;
        }
        return false;
    }
});