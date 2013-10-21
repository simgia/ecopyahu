Ext.define('ecopyahuMovil.controller.Main',{
    extend: 'Ext.app.Controller',
    requires: [
        'Ext.device.Camera',
        'Ext.device.Connection'
    ],
    config: {		
        refs: {
	    principal: 'mainviewport',
            Img: '#img',
        },
	control: {		
            'mainviewport button[action=snapPicture]': {
	        tap: 'openCamera'
	    },
            
            'mainviewport button[action=marcar_mapa]': {
	        tap: 'abrirMapa'
	    }
	},
        areImagesUploading: false,
    },

    /**
     * Metodo que abre la camara del dispositivo. 
     */
    openCamera: function(p_button, p_eve){
        //this.addLog('Se quiere sacar una foto.');
        
        var me = this;
        //var deleteBtn = me.getDeleteBtn();
        
        // Se obtiene la referencia de la imagen.
        var v_imagen = me.getImg();
        
        Ext.device.Camera.capture({
            //success: this.onCaptureSuccess,
            success: fotoExitosa,
            scope: this,
            quality : 85,//for testing havving this at 50 does faster uploads
            source: 'camera',
            destination: 'data'
        });
        
        function fotoExitosa(p_imagen) {
            // Se obtiene la ruta de la imagen.
            var v_ruta_imagen = 'data:image/jpeg;base64,'.concat(p_imagen);
            
            v_imagen.setSrc(v_ruta_imagen);

            //hide delete button
            //deleteBtn.setHidden(false);
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
    
    onCaptureSuccess: function(uri) {
        this.addLog('got foto:' + uri);
        
        var lostor = Ext.getStore('theImageQueue');
        lostor.add({
            src: uri,
            timestamp: new Date().getTime(),
            fails: 0        
	});
        lostor.sync();
        this.addLog('after sync store has count:'+lostor.getRange().length);
    },
            
    oneImageSuccess:function(){
        var jso = Ext.JSON.decode(response);
        
        if(jso){
            response = '<a href="'+jso[0].url+'">'+jso[0].url+'</a>';
        }  
        this.addLog('Uploaded file:'+response);
        
        var imstor = Ext.getStore('theImageQueue');
        imstor.removeAt(0);
        imstor.sync();
        this.uploadNextImage();
    },
            
    oneImageFail:function(msg){
        this.addLog('We have failure:'+msg);
        var imstor = Ext.getStore('theImageQueue');
        var oneImg = imstor.getAt(0);
        var failedTimes = Number(oneImg.get('fails'))+1;
        
        imstor.add(oneImg);
        imstor.removeAt(0);
        imstor.last().set('fails',failedTimes);
        imstor.sync();// I could do a messier job than the above, but javascript still behaves mysteriously for me.
        this.uploadNextImage();
    },
            
    uploadNextImage: function(){
        this.addLog('starting to upload next');
        
        var imstor = Ext.getStore('theImageQueue');
        
        if(!imstor.getRange().length){
            this.setAreImagesUploading( false );
            Ext.Msg.alert('Done', 'The file queue is empty.');
            return;
        }
        var oneImg = imstor.getAt(0);
        
        if(oneImg.get('fails') > 2){
            // If this image has failed more than 3 times: delete from queue
            imstor.removeAt(0);
            imstor.sync();
            if (!imstor.getRange().length)return;
            oneImg = imstor.getAt(0);
        }
        this.setAreImagesUploading( oneImg.get('timestamp') );
        uploadPhoto( oneImg.get('src'), oneImg.get('timestamp') );
    },
            
    hola: function(){
        // This is our true launch function
        ecopyahuMovil.bothReady = true; 
        ecopyahuMovil.semiConsole = Ext.getCmp('hconsole'); // this is an "alias" for our log label defined in Main.js
        this.connectionPoll();
        setInterval(this.connectionPoll, 30000);
    },
            
    addLog: function(toAdd){
        var msgta = ecopyahuMovil.semiConsole.getHtml()+'<br>'+toAdd;
    
        ecopyahuMovil.semiConsole.setHtml(msgta);
        console.log( msgta );
    },
       
    connectionPoll: function(){
        var este = ecopyahuMovil.app.getController('General');
        
        ecopyahuMovil.connectionType = checkConnection();
        //este.addLog('...polling... '+ DenunciasCamara.connectionType+' with store count:'+ Ext.getStore('theImageQueue').getRange().length);
        if(ecopyahuMovil.connectionType == 'WIFI' || ecopyahuMovil.connectionType == 'ETHERNET' ){//if we have wi-fi or ethernet
            este.addLog('we have WIFI');
            if(!este.getAreImagesUploading()){//and there aren't any images uploading already
                if(Ext.getStore('theImageQueue').getRange().length){//and finally IF there are images to upload
                    este.addLog('there is stuff in the queue');
                    /********* WE BEGIN A NEW UPLOAD CYCLE OF THE IMAGES ON THE STORE ********/
                    //Ext.Msg.alert('Begin', 'The file queue will start to upload.');
                    este.uploadNextImage();
                }
            }
        }
    },        
            
    launch:function(){
        console.log('This thing has started.');
        if(oneReady || Ext.os.is.Desktop){//if we are on desktop we assume there's no phonegap.
            this.hola();
        }else{
            oneReady = true;
        }
    }
});