<?php 
$this->load->view('comunes/cabecera');
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url() ?>js/OpenLayers-2.13.1/theme/default/style.css" type="text/css">
    
    
    
    <script src="<?php echo base_url() ?>js/OpenLayers-2.13.1/OpenLayers.js"></script>
    <script src="<?php echo base_url() ?>js/JQuery/jquery-1.9.1.js"></script>
    <script src="<?php echo base_url() ?>js/magnific-popup.js"></script>
    
    <link rel="stylesheet" href="<?php echo base_url() ?>css/magnific-popup.css" type="text/css">
    
    

    
    <style type="text/css">
        /*
         * Para la Atribucion del Mapa. En este caso a OpenStreetMap 
         * Y Para la Linea de Escala.
         */
        div.olControlAttribution, div.olControlScaleLine {
            font-family: Verdana;
            font-size: 0.7em;
            bottom: 3px;
        }
        
        /*
         * Para la Posicion del Mouse en el Mapa.
         */
        div.olControlMousePosition {
            position: absolute;
            right: 10px;
            top: 515px;
            height: 15px;
            font-size: 8pt;
            background-color: white
        }
        
        /*
        .olControlAttribution {
            bottom: 5px;
            font-size: 9px;
        }
        */
        #customZoom {
            z-index: 1001;
            position: relative;
            top: 10px;
            left: 10px;
        }
        #customZoom a {
            text-decoration: none;
            position: absolute;
            display: block;
            width: 50px;
            text-align: center;
            font-weight: bold;
            color: #fff;
            background: #369;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        #customZoom a:hover {
            background: #036;
        }
        #customZoomOut {
            top: 25px;
        }    
    </style>
    <script type="text/javascript">
        
        
                /**
         * abrir popup
         */
        function prepararWindow(){
            console.log('adsf');
        
            $('.simple-ajax-popup').magnificPopup({
                     type: 'ajax',
                     overflowY: 'scroll' // as we know that popup content is tall we set scroll overflow by default to avoid jump
             });
        }
        
    	//Objetos
    	var scope = this;
    	var v_mapa;				// Mapa.
    	var v_layer_osm;			// El layer OSM.
    	var v_layer_marcador;			// El layer marcador
    	var v_tamanio;				// Tamanio del icono.
    	var v_icono;				// El icono del marcador.
    	var v_offset;
    	var v_popup = null;
        
    	function inicializar(){
            // Proyecciones
            var v_fromProjection = new OpenLayers.Projection("EPSG:4326"); 			// Transformar from WGS 1984
            var v_toProjection = new OpenLayers.Projection("EPSG:900913");			// a Spherical Mercator Projection.
    		
            // Dimensiones del mapa. 
            var v_extension = new OpenLayers.Bounds(-57.5936300, -25.3309900, -57.5692900, -25.3078300).transform(v_fromProjection, v_toProjection);
    		
            // El objeto mapa.
            v_mapa = new OpenLayers.Map('mapa', {
                maxExtent : v_extension,
        	units: 'm',
    		projection: v_toProjection,
    		displayProjection: v_fromProjection,
            	controls: [
                    new OpenLayers.Control.Navigation({                                 // Navegar por el mapa.
            	        dragPanOptions: {
            	            enableKinetic: true
            	        }
            	    }),			
                    //new OpenLayers.Control.LayerSwitcher({'ascending': false}),       // Para cambiar los diferentes layers e mostrar el nombre de cada uno.
                    new OpenLayers.Control.ScaleLine(),					// La escala utilizada en el mapa.
                    new OpenLayers.Control.MousePosition(),				// Muestra la latitud y longitud de la posicion del mouse sobre el mapa.
                    new OpenLayers.Control.KeyboardDefaults(),				// Mover el mapa con el teclado.
                    new OpenLayers.Control.Attribution(),				// Atribucion a OpenStreetMap
                    new OpenLayers.Control.Zoom()                                       // Control de flecha y zoom.    
                ]
            }); 
            // Crear layer OSM.
            var v_arrayOSM = [
                "http://otile1.mqcdn.com/tiles/1.0.0/map/${z}/${x}/${y}.jpg",
                "http://otile2.mqcdn.com/tiles/1.0.0/map/${z}/${x}/${y}.jpg",
                "http://otile3.mqcdn.com/tiles/1.0.0/map/${z}/${x}/${y}.jpg",
                "http://otile4.mqcdn.com/tiles/1.0.0/map/${z}/${x}/${y}.jpg"
            ];
            var v_layer_osm = new OpenLayers.Layer.OSM('osm', v_arrayOSM);
	
            // Agregar el layer OSM al mapa.
            v_mapa.addLayer(v_layer_osm);
        	
            // Se instancia el layer Markers.
            v_layer_marcador = new OpenLayers.Layer.Markers( "Denuncias" );
        	
            // Agregar el layer marcador al mapa.
            v_mapa.addLayer(v_layer_marcador);
           	
            v_tamanio = new OpenLayers.Size(21, 25);
            v_offset = new OpenLayers.Pixel(-(v_tamanio.w / 2), -v_tamanio.h);
           
            // Iconos de los marcadores.
            v_icono_denuncia_unica = new OpenLayers.Icon('<?php echo base_url(); ?>js/OpenLayers-2.13.1/img/marker-gold.png', v_tamanio, v_offset);
            v_icono_denuncias_varias = new OpenLayers.Icon('<?php echo base_url(); ?>js/OpenLayers-2.13.1/img/marker.png', v_tamanio, v_offset);
            
            // Se agrega un marcador en el Layer de marcadores.
            <?php
            	$primer_punto = false; 
            	$promedio_lat = 0;
            	$promedio_long = 0;
            	$cant = 0;
                if(isset($puntos)){
            	foreach($puntos as $punto){
                    $cant++;
                    $promedio_lat += $punto->latitud;
                    $promedio_long += $punto->longitud;
                    if($primer_punto == false){ 
            		if($punto->cantidad == 1){		
            ?>
                            v_marcador = new OpenLayers.Marker(new OpenLayers.LonLat(<?php echo $punto->longitud; ?>, <?php echo $punto->latitud; ?>).transform(
                                v_fromProjection, 				// Transformar from WGS 1984
    				v_toProjection 					// a Spherical Mercator Projection.
                            ), v_icono_denuncia_unica);
            <?php
			}else{
			?>
                             v_marcador = new OpenLayers.Marker(new OpenLayers.LonLat(<?php echo $punto->longitud; ?>, <?php echo $punto->latitud; ?>).transform(
                                v_fromProjection, 				// Transformar from WGS 1984
	            		v_toProjection 					// a Spherical Mercator Projection.
                             ), v_icono_denuncias_varias);
			<?php
                        } // Fin del else.
            		$primer_punto = true; 
            	}else{
            	     if($punto->cantidad == 1){		
           ?>
		         // Se agrega otro marcador en el Layer de marcadores.
		         v_marcador = new OpenLayers.Marker(new OpenLayers.LonLat(<?php echo $punto->longitud; ?>, <?php echo $punto->latitud; ?>).transform(
                            v_fromProjection, 					// Transformar from WGS 1984
                            v_toProjection 					// a Spherical Mercator Projection.
                         ), v_icono_denuncia_unica.clone()); 
           <?php 
                    }else{
		   ?>
		         v_marcador = new OpenLayers.Marker(new OpenLayers.LonLat(<?php echo $punto->longitud; ?>, <?php echo $punto->latitud; ?>).transform(
       			     v_fromProjection, 					// Transformar from WGS 1984
                             v_toProjection 					// a Spherical Mercator Projection.
   			 ), v_icono_denuncias_varias.clone());
		   <?php
                    }// Fin del else.
            	}// Fin del else.
           ?>
           	v_marcador.events.register('click', v_marcador, function(evt) {
                    
                                       if(v_popup == null){
                        v_popup = new OpenLayers.Popup("Denuncias",
                            new OpenLayers.LonLat(<?php echo $punto->longitud; ?>, <?php echo $punto->latitud; ?>).transform(
		                v_fromProjection, 					// Transformar from WGS 1984
		            	v_toProjection 						// a Spherical Mercator Projection.
		            ),
		            <?php
		            	if($punto->cantidad == 1){
		            ?>
                                    new OpenLayers.Size(220, 40),
		            	<?php
		            	}else{
		            	?>
		            	     new OpenLayers.Size(150, 130),
                                <?php
		            	}
		            	?>
	           	        "<?php 
                                     if($punto->cantidad == 1){
                                         $v_url = base_url('denuncias/consulta_detalle_denuncia').'/?denuncia_id='.trim($punto->denuncia_id);
	           	        	 //echo "<br>La denuncia es: <a id= href='".$v_url."' target='_blank' >" . $punto->denuncia_id . '</a>';
                                         echo "<br>La denuncia es: <a  class='simple-ajax-popup' href='denuncias/getDenuncia/".$punto->denuncia_id."'>" . $punto->denuncia_id . '</a>';
                                         //echo "<br>La denuncia es: <a href='http://google.com' onclick='window.open(this.href, 'windowName', 'width=1000, height=700, left=24, top=24, scrollbars, resizable'); return false;'>" . $punto->denuncia_id . '</a>';
                                         //echo "<a id='some_id' onclick='ver_detalle_denuncia' class='optional_has_click'>Click Me</a>";
                                         
                                         //echo "<a href = '#' onClick='javascript:popUp(".$v_url.")'‌​>link</a>";
                                         //echo "$('element_to_pop_up').bPopup();";
	           	             }else{
	           	        	 echo '<br>Las denuncias son: <ul>';
	           	        	 for($i = 0; $i < count($punto->denuncias); $i++){
                                             $v_url = base_url('denuncias/getDenuncia').'/'.trim($punto->denuncias[$i]->denuncia_id);
	           	        	     echo "<li><a class='simple-ajax-popup' ' href='".$v_url."'>".$punto->denuncias[$i]->denuncia_id."</a></li>";
                                         }
                                         echo '</ul>';
                                     }
                                ?>
	           	        ",
	           	        true,
				function(){
                                    v_mapa.removePopup(v_popup);
                                    v_popup.destroy();
                                    v_popup = null;
	           	        }
	           	    );
	            	    v_popup.setBackgroundColor("white");
	            	    v_popup.setBorder("1px solid #CCCCCC");
	            	    v_mapa.addPopup(v_popup);
                                    prepararWindow();
           		}else{
           		     v_popup.destroy();
           		     v_popup = null;
                             //v_popup.toggle();
                	}
                    

                    OpenLayers.Event.stop(evt);
           	}); // Fin del evento click del v_marcador.
           	v_layer_marcador.addMarker(v_marcador); 
           <?php
            	}//end foreach.
                }// end if.
            	if($cant == 0){ 
                    // Control de que no hay nada que haya pasado por el foreach.
                    $cant++;
                    $promedio_long = -57.58146;
                    $promedio_lat  = -25.31941;
            	}
            ?>
            // Posicionar para la primera visualizacion el mapa en una latitud y longitud elegida.
            v_mapa.setCenter(new OpenLayers.LonLat(<?php echo $promedio_long/$cant;?>, <?php echo $promedio_lat/$cant;?>) 	// Centrar el mapa.
                .transform(
                    v_fromProjection, 					// Transformar from WGS 1984
                    v_toProjection 					// a Spherical Mercator Projection.
                ), 13 							// Nivel de zoom
            ); 
        } // Fin de la funcion inicializar.
        

        
	</script>
</head>
<!--CUERPO-->
<body onload='inicializar();'>
    <div id="id-ventana" style="display: none;">
        <p>por finnnnnn</p>
    </div>

    <div id="content-denunciar">
        <?php $this->load->view('comunes/menu')?>
            <h2>Denuncias</h2>
            <br/>
            <br/>
            <center>
                <div id="mapa" style="width: 960px; height: 550px;"></div>
                <a class="twitter-timeline" href="https://twitter.com/ecopyahu" data-widget-id="393921229297430528">Tweets por @ecopyahu</a>
            </center>
<!--CUERPO-->
<?php $this->load->view('comunes/pie')?>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </div>
</body>
</html>