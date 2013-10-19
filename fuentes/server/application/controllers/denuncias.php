<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author josego
 * @package dmp
 *
 */
class denuncias extends SMG_Controller{
        /**
	 * @var denuncias_m
	 */
        var $denuncias;
   	
        /**
	 * Constructor donde levanta las librerias:
	 * - form_validation: sirve para validar el formulario de registro de usuario.
	 * - model usuario.
	 */
	public function __construct(){
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->library('form_validation');
		$this->load->model('usuarios_m','usuarios');
		$this->load->model('denuncias_m','denuncias');	
	}
	
	/**
	 * Metodo que se ejecuta por defecto en el controlador.
	 * @return void
	 */
	public function index(){
		$this->basico('true');
	}
    
    public function basico($p_es_controller_inicial){
    	if(isset($this->session->userdata['datos_denuncia']['no_mostrar_aviso']) && $this->session->userdata['datos_denuncia']['no_mostrar_aviso'] == 'true' ){
    		$this->data['p_mostrar_aviso'] = false;
    	}else{
    		$this->data['p_mostrar_aviso'] = true;
    	}

    	if($this->seguridad->logged()||isset($_COOKIE['no_mostrar_aviso'])){
    		$this->data['p_mostrar_aviso'] = false;
    	}
    		
    	
    	//se define campos obligatorios
    	$this->data['p_campos_obligatorios'] = $this->get_campos_obligatorios();
    	$this->data['p_menu_actual'] = 'realizar_denuncia';
    	//Recupera los datos actualmente guardado
    	$v_datos_denuncias_array = $this->get_denuncia_temp();
    	$v_textos_denuncias_array = $this->get_denuncia_texto_narrado($v_datos_denuncias_array);
    	$v_nombre_imagenes = $this->recuperar_imagenes("array");
    	$this->data['p_tipos_denuncias'] = $this->denuncias->get_tipos_denuncias(null);
    	// $this->data['p_tipos_denuncias'] = $v_consulta->result();
    	// print_r($this->data['p_tipos_denuncias']);
    	$this->data['p_datos_denuncias_array'] = $v_datos_denuncias_array;
    	$this->data['p_textos_denuncias_array'] = $v_textos_denuncias_array;
    	$this->data['p_nombre_imagenes'] = $v_nombre_imagenes;								
			
    	if(isset($p_es_controller_inicial) && $p_es_controller_inicial == 'true'){
    		$opciones_controller['ruta_controller'] = '';		
    	}else{
    		$opciones_controller['ruta_controller'] = 'denuncias';
    	}
    	$this->data['opciones_controller'] = $opciones_controller;
    	
    	$this->load->view('denuncias/denuncias_basico', $this->data);
    }
    
    public function extendido(){
    	$this->session->set_userdata("extendida",1);
    	$this->data['p_campos_obligatorios'] = $this->get_campos_obligatorios();
        $this->data['p_menu_actual'] = 'realizar_denuncia';
        //Recupera los datos actualmente guardado
        $v_datos_denuncias_array = $this->get_denuncia_temp();
        $v_textos_denuncias_array = $this->get_denuncia_texto_narrado($v_datos_denuncias_array);
        $v_nombre_imagenes = $this->recuperar_imagenes("array");
        $this->data['p_tipos_explotaciones'] = $this->denuncias->get_tipos_explotaciones(null);
        $this->data['p_datos_denuncias_array'] = $v_datos_denuncias_array;
        $this->data['p_textos_denuncias_array'] =$v_textos_denuncias_array ;
        $this->data['p_nombre_imagenes'] = $v_nombre_imagenes;

        $this->load->view('denuncias/denuncias_extendido', $this->data);
    }
    
    public function finalizar(){
    	$this->session->set_userdata("finalizar",1);
    	$this->data['p_campos_obligatorios'] = $this->get_campos_obligatorios();
        $this->data['p_menu_actual'] = 'realizar_denuncia';
        //Recupera los datos actualmente guardado
        $v_datos_denuncias_array = $this->get_denuncia_temp();
        $v_textos_denuncias_array = $this->get_denuncia_texto_narrado($v_datos_denuncias_array);
        $v_nombre_imagenes = $this->recuperar_imagenes("array");
        $this->data['p_datos_denuncias_array'] = $v_datos_denuncias_array;
        $this->data['p_textos_denuncias_array'] =$v_textos_denuncias_array ;
        $this->data['p_nombre_imagenes'] = $v_nombre_imagenes;
        
        $this->load->view('denuncias/denuncias_finalizar', $this->data);
    }
    
    public function guardar_denuncia(){
        $this->data['p_menu_actual'] = 'realizar_denuncia';
        //Si no hay una denuncia que se haya comenzado envia al inicio del proceso
        //  tipicamente si le dan "refresh" despues de guardar una denuncia
        if(!$this->denuncia_temp_iniciada())
            redirect('/denuncias/basico', 'refresh');
    
        $v_sin_usuario = array();
        $v_sin_usuario["nombre_apellido"] = $this->input->post('nombre_apellido', true);
		$v_sin_usuario["telefono"] = $this->input->post('telefono', true);
        
        $v_usuario = null;
        if($this->session->userdata('usuario')) {
            $v_email = $this->session->userdata('usuario')->USUARIO_EMAIL;
            $v_usuario = $this->usuarios->get_datos_usuario($v_email);
        }
        
        $v_datos = $this->get_denuncia_temp();
        
        //si aun queda campos obligatorios
        if(count($v_datos['obligatorios'])>0)
        	redirect('/denuncias/finalizar', 'refresh');
        
        $v_datos["fecha_hora"] = $this->fecha_hora;
        
        $v_denuncia = array("datos"=>$v_datos, "usuario"=>$v_usuario, "sin_usuario"=>$v_sin_usuario);
        
        $v_imagenes = $this->guardar_imagenes();
        
        $v_ip_denunciante = $this->input->ip_address();
        
        if($v_id_denuncia = $this->denuncias->guardar_denuncia($v_denuncia, $v_imagenes, $v_ip_denunciante)) {
            //Se pudo guardar exitosamente en la DB
            
            
            $this->delete_denuncia_temp();
            $this->session->unset_userdata("extendida");
            $this->session->unset_userdata("finalizar");
            $this->data['v_titulo'] = 'Denuncia enviada correctamente.';
            $this->data['v_mensaje'] =  '<p>Muchas gracias por su colaboraci&oacute;n.</p>';
            $this->data['v_mensaje'] .= '<p>Su Ticket ID es <b style="font-size:24px;">'.dechex($v_id_denuncia).'</b></p>';
            $this->data['v_mensaje'] .= '<p>Guardelo para futuras referencias.</p>';
            $this->data['v_mensaje'] .= '<p><b>OBS:</b></p>';
            $this->data['v_mensaje'] .= '<p>En caso de perder su Ticket, puede comunicarse al (+595 21) 454 611.</p>';
            
            // El id denuncia pero en hexadecimal
            $v_id_denuncia_hexadecimal = dechex($v_id_denuncia);
            
            // Se envia un mail a la persona encargada de recibir las denuncias, como constancia.
            // Se envia la denuncia con las imagenes adjuntas. 
            if($this-> enviar_email_persona_responsable($v_denuncia, $v_imagenes, $v_id_denuncia_hexadecimal)){
            	//echo "Envio.";
            	// Envio correctamente el mail. 
            	;
            }else{
            	//echo "No Envio.";
            	// Si no se llega a enviar el mail, se va a quedar en la cola de mails del sistema. En este caso se usa sendmail del propio dominio del Ministerio publico.
            	;
            }
            
            $this->load->view('comunes/informaciones', $this->data);

        } else {
            //Hubo errores
            redirect('/denuncias/finalizar', 'refresh');
        }
        
    }

    
    /*******************************************************************************************************
    * INICIO SESIONES
    */
    /**
	* Function public que devuelve un array con todos textos a usarse al convertir a denuncia narrada
    * @param string p_datos un array con los datos cargados por el usuario a partir del formulario
	* @return array
	*/
    public function get_denuncia_texto_narrado($p_datos) {
        $v_textos = array();//inicializamos
    
        if($p_datos) {
            // print_r($p_datos);
            $v_textos["tipo_denuncia"]="";
            
            //Excepcion si vienen los datos de base de datos
            //---------------------------------------------------------
            if(isset($p_datos["DENUNCIA_ID"])) {
                $v_textos["fecha_registro"] = "Denuncia registrada el ".$p_datos["DENUNCIA_FECHA_REGISTRO"];
                if(isset($p_datos["DENUNCIA_ESTADO"])){
                	$v_textos["denuncia_estado"] = "Estado: ".$p_datos["DENUNCIA_ESTADO"];
                }else{
                	$v_textos["denuncia_estado"] = "Estado: ". 'Nuevo';
                }
                $v_textos["tipo_denuncia"] = $p_datos["TIP_DENUNCIA_DES"];
                if($p_datos['DENUNCIA_VICTIMA_SEXO']!=''){
                	$v_textos["sexo_victima"] = "Sexo victima(s): ".$p_datos['DENUNCIA_VICTIMA_SEXO'];
                } 
                
                
                //Datos del denunciante
                $v_textos["denunciante"] = "Denunciante";
                if($p_datos["USUARIO_EMAIL"]!="") {
                    $v_textos["denunciante"] = "Denunciante registrado: ";
                    $v_textos["denunciante"] .= "<ul><li>E-mail: ".$p_datos["USUARIO_EMAIL"]."</li>";
                    if($p_datos["USUARIO_NOMBRE"]!="" || $p_datos["USUARIO_APELLIDO"]!="")
                        $v_textos["denunciante"] .= "<li>".trim($p_datos["USUARIO_NOMBRE"]." ".$p_datos["USUARIO_APELLIDO"])."</li>";
                    if($p_datos["USUARIO_CELULAR"]!="")
                        $v_textos["denunciante"] .= "<li>Celular: ".$p_datos["USUARIO_CELULAR"]."</li>";
                    if($p_datos["USUARIO_FORMA_CONTACTO"]!="")
                        $v_textos["denunciante"] .= "<li>Forma de Contacto: ".$p_datos["USUARIO_FORMA_CONTACTO"]."</li>";
                   /* if($p_datos["USUARIO_FECHA_NACIMIENTO"]!="")
                        $v_textos["denunciante"] .= "<li>Nac.: ".$p_datos["USUARIO_FECHA_NACIMIENTO"]."</li>";*/
                    if($p_datos["INSTITUCION_NOMBRE"]!="")
                        $v_textos["denunciante"] .= "<li>Instituci&oacute;n: ".$p_datos["INSTITUCION_NOMBRE"]."</li>";
                    $v_textos["denunciante"] .= "</ul>";
                } elseif($p_datos["DENUNCIA_NOM_APE_DENUNCIANTE"]!="" || $p_datos["DENUNCIA_TELEFONO_DENUNCIANTE"]!="" || $p_datos["DENUNCIA_EMAIL_DENUNCIANTE"]!="") {
                    $v_textos["denunciante"] = "Denunciante Casual<ul>";
                    if($p_datos["DENUNCIA_NOM_APE_DENUNCIANTE"]!="")
                        $v_textos["denunciante"] .= "<li>Nombre: ".$p_datos["DENUNCIA_NOM_APE_DENUNCIANTE"]."</li>";
                    if($p_datos["DENUNCIA_EMAIL_DENUNCIANTE"]!="")
                        $v_textos["denunciante"] .= "<li>E-mail: ".$p_datos["DENUNCIA_EMAIL_DENUNCIANTE"]."</li>";
                    if($p_datos["DENUNCIA_TELEFONO_DENUNCIANTE"]!="")
                        $v_textos["denunciante"] .= "<li>Tel.: ".$p_datos["DENUNCIA_TELEFONO_DENUNCIANTE"]."</li>";
                    $v_textos["denunciante"] .= "</ul>";
                }
                
                //Fecha del hecho
                $p_datos["fecha_inicial"] = $p_datos["DENUNCIA_FECHA_INICIO"];
                $p_datos["fecha_final"] = $p_datos["DENUNCIA_FECHA_FIN"];
                if($p_datos["fecha_inicial"]!="" && $p_datos["fecha_final"]!="")
                    $p_datos["tipo_evento"] = "periodo";
                
                
                //Si hay coordenadas
                if($p_datos["DENUNCIA_LATITUD"]!="") {
                    $p_datos["google_localizacion"]["latitud_longitud_str"]=$p_datos["DENUNCIA_LATITUD"].", ".$p_datos["DENUNCIA_LONGITUD"];
                }
                
                //Localizacion
                //if($p_datos["DENUNCIA_LOCAL_PRIORIDAD"]=="google" || $p_datos["DENUNCIA_LOCAL_PRIORIDAD"]=="") {
                if($p_datos["DENUNCIA_LOCAL_PRIORIDAD"]=="mapa" || $p_datos["DENUNCIA_LOCAL_PRIORIDAD"]=="") {
                    $v_textos["denuncia_local_prioridad"] = "Para la localizaci&oacute;n solo utiliz&oacute; Google Maps.";
                    if($p_datos["DEPARTAMENTO_DES"]!="")
                        $v_geo["departamento"] = $p_datos["DEPARTAMENTO_DES"];
                    if($p_datos["CIUDAD_DES"]!="")
                        $v_geo["ciudad"] = $p_datos["CIUDAD_DES"];
                    if($p_datos["BARRIO_DES"]!="")
                        $v_geo["barrio"] = $p_datos["BARRIO_DES"];
                    if($p_datos["DENUNCIA_CALLE_MAPA"]!="")
                        $v_geo["calles"] = $p_datos["DENUNCIA_CALLE_MAPA"];
                        
                } else {
                    $v_textos["denuncia_local_prioridad"] = "Para la localizaci&oacute;n edit&oacute; los campos manualmente.";
                    if($p_datos["DENUNCIA_CIUDAD_DEPARTAMENTO"]!="")
                        $v_geo["ciudad"] = $p_datos["DENUNCIA_CIUDAD_DEPARTAMENTO"];
                    if($p_datos["DENUNCIA_BARRIO"]!="")
                        $v_geo["barrio"] = $p_datos["DENUNCIA_BARRIO"];
                    if($p_datos["DENUNCIA_CALLE_USUARIO"]!="")
                        $v_geo["calles"] = $p_datos["DENUNCIA_CALLE_USUARIO"];
                }
                $p_datos["rescate_inmediato"] = $p_datos["DENUNCIA_RESCATE_INMEDIATO"];
                $p_datos["usted_victima"] = $p_datos["DENUNCIA_USTED_VICTIMA"];
                $p_datos["sospechas"] = $p_datos["DENUNCIA_SOSPECHAS"];
                
                //Detalles tipos explotaciones
                $codigo_decimal = hexdec($p_datos["DENUNCIA_ID"]);
                $v_detalles_tipos_explotaciones = $this->denuncias->get_denuncias_detalle_tipos_explotaciones($codigo_decimal);
                // echo "se acaba de ejecutar<br>";
                $v_detalles_tipos_explotaciones = $v_detalles_tipos_explotaciones->result();
                // print_r($v_detalles_tipos_explotaciones);
                foreach($v_detalles_tipos_explotaciones as $v_detalle) {
                    $p_indice = "tipo_explotacion_".$v_detalle->TIP_EXPLOTACION_ID;
                    $p_datos[$p_indice] = "true";
                }
                
                //Cantidad victimas
                if($p_datos["DENUNCIA_CANTIDAD_VICTIMA"]!="")
                    $p_datos["cantidad_victimas"] = $p_datos["DENUNCIA_CANTIDAD_VICTIMA"];
                if($p_datos["DENUNCIA_RANGO_EDAD"]!="")
                    $p_datos["rango_edad"] = $p_datos["DENUNCIA_RANGO_EDAD"];
                //Nombre del explotador
                if($p_datos["DENUNCIA_NOMBRE_EXPLOTADOR"]!="")
                    $p_datos["nombre_explotador"] = $p_datos["DENUNCIA_NOMBRE_EXPLOTADOR"];
                //Datos de la victima
                if($p_datos["DENUNCIA_NOMBRE_VICTIMA"]!="")
                    $p_datos["dato_victima"] = $p_datos["DENUNCIA_NOMBRE_VICTIMA"];
                //Ultimo contacto con la victima
                if($p_datos["DENUNCIA_FECHA_ULT_CONTACTO"]!="")
                    $p_datos["fecha_ultimo_contacto"] = $p_datos["DENUNCIA_FECHA_ULT_CONTACTO"];
                //Contacto con la victima
                if($p_datos["DENUNCIA_CONTACTO_VICTIMA"]!="")
                    $p_datos["contacto_victima"] = $p_datos["DENUNCIA_CONTACTO_VICTIMA"];
            }
            //---------------------------------------------------------
            
            
            //-----------------------------------------------------------------
            //Basico
            //-----------------------------------------------------------------
            
            //Tipo Denuncia
            
            if(isset($p_datos["tipo_denuncia"]) && $p_datos["tipo_denuncia"]!="")
                $v_textos["tipo_denuncia"] = $this->denuncias->get_tipos_denuncias($p_datos["tipo_denuncia"]);
            
            //Fecha
            $v_textos["cuando"] = "";
            if(isset($p_datos["fecha_inicial"]) && $p_datos["fecha_inicial"]!="") {
                $v_textos["cuando"] = "Ocurri&oacute; el ".$p_datos["fecha_inicial"];
                if(isset($p_datos["tipo_evento"]) && $p_datos["tipo_evento"]=="periodo" && isset($p_datos["fecha_final"]) && $p_datos["fecha_final"]!="")
                    $v_textos["cuando"] .= " hasta el ".$p_datos["fecha_final"].".";
            } else {
                if(isset($p_datos["tipo_evento"]) && $p_datos["tipo_evento"]=="periodo" && isset($p_datos["fecha_final"]) && $p_datos["fecha_final"])
                    $v_textos["cuando"] = "No se puede precisar la fecha de incio pero ocurri&oacute; hasta el ".$p_datos["fecha_final"].".";
            }
            
            //Localizacion
            if(isset($p_datos["google_localizacion"])) {
                if(isset($p_datos["google_localizacion"]["latitud_longitud_str"])) {
                    $v_textos["latitud_longitud_str"] = $p_datos["google_localizacion"]["latitud_longitud_str"];
                    if(isset($p_datos["google_localizacion"]["zoom_mapa"])) {
                        $v_textos["zoom_mapa"] = $p_datos["google_localizacion"]["zoom_mapa"];
                        $v_textos["tipo_mapa"] = $p_datos["google_localizacion"]["tipo_mapa"];
                    }
                }
            }
            
            $v_textos["localizacion"] = "";
            if(isset($p_datos["origen_localizacion"]) && $p_datos["origen_localizacion"]=="google") {
                $v_geo = $p_datos["google_localizacion"];   //Facilita el codigo que sigue
                
                $v_textos["latitud_longitud_str"] = $v_geo["latitud_longitud_str"];
                $v_textos["zoom_mapa"] = $v_geo["zoom_mapa"];
                $v_textos["tipo_mapa"] = $v_geo["tipo_mapa"];
                
            } elseif(isset($p_datos["origen_localizacion"]) && $p_datos["origen_localizacion"]=="usuario") {
                $v_geo = $p_datos;
            }
            
            if(isset($v_geo)) {
                $v_textos["ciudad"] = "";
                if(isset($v_geo["ciudad"]))
                    $v_textos["ciudad"] = $v_geo["ciudad"];
                    
                if(isset($v_geo["departamento"]) && $v_geo["departamento"]!="") {
                    if($v_textos["ciudad"]!="")
                        $v_textos["ciudad"].="/";
                    $v_textos["ciudad"].=$v_geo["departamento"];
                }
                
                $v_textos["barrio"] = "";
                if(isset($v_geo["barrio"]))
                    $v_textos["barrio"] = $v_geo["barrio"];
                
                $v_textos["calles"] = "";
                if(isset($v_geo["calles"]))
                    $v_textos["calles"] = $v_geo["calles"];
                
                if(isset($v_textos["calles"]) && $v_textos["calles"]!="") {
                    $v_textos["localizacion"] = $v_textos["calles"];
                }
                
                if(isset($v_textos["barrio"]) && $v_textos["barrio"]!="") {
                    if($v_textos["localizacion"]!="")
                        $v_textos["localizacion"] .= ", ";
                    $v_textos["localizacion"] .= "barrio ".$v_textos["barrio"];
                }
                
                if(isset($v_textos["ciudad"]) && $v_textos["ciudad"]!="") {
                    if($v_textos["localizacion"]!="")
                        $v_textos["localizacion"] .= ", ";
                    $v_textos["localizacion"] .= $v_textos["ciudad"];
                }
                
                if($v_textos["localizacion"]!="") {
                    $v_textos["localizacion"] = "Ubicado en ".$v_textos["localizacion"].".";
                }
            }
        
            //Rescate Inmediato
            if(isset($p_datos["rescate_inmediato"]) && $p_datos["rescate_inmediato"]=="si")
                $v_textos["rescate_inmediato"] = "Se requiere rescate inmediato.";
            elseif(isset($p_datos["rescate_inmediato"])&&$p_datos["rescate_inmediato"]=="no")
                $v_textos["rescate_inmediato"] = "No se requiere rescate inmediato.";
                
            //Usted fue victima
            if(isset($p_datos["usted_victima"])&&$p_datos["usted_victima"]=="si")
                $v_textos["usted_victima"] = "El denunciante fue v&iacute;ctima.";
            elseif(isset($p_datos["usted_victima"])&&$p_datos["usted_victima"]=="no")
                $v_textos["usted_victima"] = "El denunciante no fue v&iacute;ctima.";
                
            //Sospechas
            if(isset($p_datos['sospechas'])&&$p_datos["sospechas"]!="")
                $v_textos["sospechas"] = "Sospechas: ".$p_datos["sospechas"];
                
            //Obligan a prostituirse
            $v_textos["tipo_explotacion_1"]="";
            if(isset($p_datos["tipo_explotacion_1"]) && $p_datos["tipo_explotacion_1"]=="true")
                $v_textos["tipo_explotacion_1"] = "Se obligan a ni&ntilde;os/adolescentes a prostituirse.";
                
            
            //Venta de pronografia infantil
            $v_textos["tipo_explotacion_2"]="";
            if(isset($p_datos["tipo_explotacion_2"])&&$p_datos["tipo_explotacion_2"]=="true")
            	$v_textos["tipo_explotacion_2"] = "Venden/distribuyen pornograf&iacute;a infantil.";
            
            //Utilizan ninhos en pornografia
            $v_textos["tipo_explotacion_3"]="";
            if(isset($p_datos["tipo_explotacion_3"])&&$p_datos["tipo_explotacion_3"]=="true")
                $v_textos["tipo_explotacion_3"] = "Utilizan ni&ntilde;os en pornograf&iacute;a.";
            
            //Producen pronografia infantil
            $v_textos["tipo_explotacion_4"]="";
            if(isset($p_datos["tipo_explotacion_4"])&&$p_datos["tipo_explotacion_4"]=="true")
                $v_textos["tipo_explotacion_4"] = "Producen/reproducen material de pornograf&iacute;a infantil.";
                
            //Exportan al exterior
            $v_textos["tipo_explotacion_5"]="";
            if(isset($p_datos["tipo_explotacion_5"])&&$p_datos["tipo_explotacion_5"]=="true")
                $v_textos["tipo_explotacion_5"] = "Personas que reclutan personas adultas/ni&ntilde;os/ni&ntilde;as/adolescentes con promesas falsas para enviarlas fuera del pa&iacute;s y explotarlas.";
            
            //Otros
            $v_textos["tipo_explotacion_6"]="";
            if(isset($p_datos["tipo_explotacion_6"])&&$p_datos["tipo_explotacion_6"]=="true")
            	$v_textos["tipo_explotacion_6"] = "Otros tipos de explotaciones.";
                
            //Victimas (Cantidades y edades)
            $v_textos["victimas"] = "";
            //Cantidad de victimas definidas
            if(isset($p_datos["cantidad_victimas"])&&$p_datos["cantidad_victimas"]>0) {
                if($p_datos["cantidad_victimas"]>1) {
                    $v_textos["victimas"] = "Hay ".$p_datos["cantidad_victimas"]." ";  //Primera parte del texto
                    
                    //n victimas sin edades
                    if(!isset($p_datos["rango_edad"])||$p_datos["rango_edad"]=="")
                        $v_textos["victimas"] .= "personas con edades desconocidas como v&iacute;ctimas.";
                    
                    //Ninhos como victimas
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="0_13"){
                    	if(isset($p_datos["sexo"]) &&$p_datos["sexo"]=="varon")
                    		$v_textos["victimas"] .= " ni&ntilde;os";
                    	elseif(isset($p_datos["sexo"]) &&$p_datos["sexo"]=="mujer")
                    		$v_textos["victimas"] .= " ni&ntilde;as";
                    	else
                    		$v_textos["victimas"] .= " ni&ntilde;os o ni&ntilde;as";
                        $v_textos["victimas"] .= " entre 0 y 13 a&ntilde;os como v&iacute;ctimas.";
                    }
                    //Adolescentes como victimas
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="14_17"){
                        $v_textos["victimas"] .= "adolescentes de entre 14 y 17 a&ntilde;os como v&iacute;ctimas.";
                    }
                    //Adultos como victimas
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="18_N")
                        $v_textos["victimas"] .= "adultos como v&iacute;ctimas.";
                    
                    //Diferentes edades
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="diferentes_edades")
                        $v_textos["victimas"] .= "personas de diferentes edades como v&iacute;ctimas.";
                
                //Cantidad de victimas == 1
                }elseif(isset($p_datos["cantidad_victimas"])&&$p_datos["cantidad_victimas"]==1) {
                    $v_textos["victimas"] = "Hay un";  //Primera parte del texto
                    
                    //Una victima sin edad
                    if(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="")
                        $v_textos["victimas"] .= "a persona con edad desconocida como v&iacute;ctima.";
                    
                    //Ninho como victima
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="0_13"){
                    	if(isset($p_datos["sexo"]) &&$p_datos["sexo"]=="varon")
                    		$v_textos["victimas"] .= " ni&ntilde;o";
                    	elseif(isset($p_datos["sexo"]) &&$p_datos["sexo"]=="mujer")
                    		$v_textos["victimas"] .= " ni&ntilde;a";
                    	else
                    		$v_textos["victimas"] .= " ni&ntilde;o o ni&ntilde;a";
                    	$v_textos["victimas"] .= "  entre 0 y 13 a&ntilde;os como v&iacute;ctima.";
                    }
                        
                    
                    //Adolescente como victima
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="14_17")
                        $v_textos["victimas"] .= " adolescente entre 14 y 17 a&ntilde;os como v&iacute;ctima.";
                    
                    //Adulto como victima
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="18_N")
                        $v_textos["victimas"] .= " adulto como v&iacute;ctima.";
                    
                    //Diferentes edades
                    elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="diferentes_edades")
                        $v_textos["victimas"] .= "a persona con edad desconocida como v&iacute;ctima.";
                }
                
            //Cantidad de victimas desconocidas
            } elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]!="") {
                $v_textos["victimas"] = "Hay una cantidad desconocida de v&iacute;ctimas ";  //Primera parte del texto
            
                //Ninho como victima
                if(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="0_13"){
                   if(isset($p_datos["sexo"]) &&$p_datos["sexo"]=="varon")
                    		$v_textos["victimas"] .= " ni&ntilde;os";
                    	elseif(isset($p_datos["sexo"]) &&$p_datos["sexo"]=="mujer")
                    		$v_textos["victimas"] .= " ni&ntilde;as";
                    	else
                    		$v_textos["victimas"] .= " ni&ntilde;os o ni&ntilde;as";
                        $v_textos["victimas"] .= " entre 0 y 13 a&ntilde;os como v&iacute;ctimas.";
                }
                
                //Adolescente como victima
                elseif(isset($p_datos["rango_edad"]) && $p_datos["rango_edad"]=="14_17")
                    $v_textos["victimas"] .= "adolescentes entre 14 y 17 a&ntilde;os.";
                
                //Adulto como victima
                elseif(isset($p_datos["rango_edad"]) && $p_datos["rango_edad"]=="18_N")
                    $v_textos["victimas"] .= "adultas.";
                
                //Diferentes edades
                elseif(isset($p_datos["rango_edad"]) &&$p_datos["rango_edad"]=="diferentes_edades")
                    $v_textos["victimas"] .= "con diferentes edades.";
            }
            if($v_textos["victimas"]!="") $v_textos["victimas"]="".$v_textos["victimas"]."";
            
            $v_textos["sexo"]="";
            if(isset($p_datos["sexo"])&&$p_datos["sexo"]!=""){
            	if(isset($p_datos["cantidad_victimas"])&&$p_datos["cantidad_victimas"]==1){
            		if($p_datos["sexo"]=="ambos"){
            			$v_textos["sexo"] = "La v&iacute;ctima es de " .$p_datos["sexo"]." sexos.";
            		}else{
            			$v_textos["sexo"] = "La v&iacute;ctima es " .$p_datos["sexo"].".";
            		}           		
            	}else{
            		if($p_datos["sexo"]=="ambos")
            			$v_textos["sexo"] = "Existen v&iacute;ctimas de ambos sexos.";
            		else{
            			$v_textos["sexo"] = "Las v&iacute;ctimas son ".$p_datos["sexo"]."es.";
            		}		
            	}
            }
            
            //Nombre del explotador
            $v_textos["nombre_explotador"]="";
            if(isset($p_datos["nombre_explotador"])&&$p_datos["nombre_explotador"]!="")
                $v_textos["nombre_explotador"] = "La persona que les explota es conocida como ".$p_datos["nombre_explotador"]."";
            
            //Datos de la victima
            $v_textos["dato_victima"]="";
            if(isset($p_datos["dato_victima"])&&$p_datos["dato_victima"]!="")
                $v_textos["dato_victima"] = "Datos de la v&iacute;ctima: ".$p_datos["dato_victima"]."";
                
            //Ultimo contacto con la victima
            $v_textos["fecha_ultimo_contacto"]="";
            if(isset($p_datos["fecha_ultimo_contacto"])&&$p_datos["fecha_ultimo_contacto"]!="")
                $v_textos["fecha_ultimo_contacto"] = "El &uacute;ltimo contacto con la v&iacute;ctima fue el ".$p_datos["fecha_ultimo_contacto"]."";
            
            //Forma de contacto con la victima.
            $v_textos["contacto_victima"]="";
            if(isset($p_datos["contacto_victima"])&&$p_datos["contacto_victima"]!="")
                $v_textos["contacto_victima"] = "La forma de contacto es: ".$p_datos["contacto_victima"]."";
        }
        
        return $v_textos;
    } // Fin de la funcion public get_denuncia_texto_narrado.
    
    /**
	 * Function public que devuelve un array con todos los datos de la denuncia en curso y sus textos para el resumen
     * @param string p_format [via POST] Formato en el que debe devolver los datos.
     * @param string p_texto [via POST o normal] Indica si se requiere los textos narrados de la denuncia.
	 * @return array/void
	 */
    public function get_denuncia_temp($p_texto=null) {
    
         $p_format = isset($_POST['format'])?$_POST['format']:'array';      //Se utiliza este formato para recuperar POST
                                                                            // porque es un Ext.Ajax
        
        //Se obtienen los datos de la denuncia
        $v_datos = $this->ci->session->userdata("datos_denuncia", true);
        //print_r($v_datos);
        if(isset($v_datos["google_localizacion"]) && gettype($v_datos["google_localizacion"])=="string")
        	$v_datos["google_localizacion"] = $v_datos["google_localizacion"];
            //$v_datos["google_localizacion"] = json_decode($v_datos["google_localizacion"],true);

        //Se pregunta si viene un valor como parametro simple
        if(empty($p_texto) && isset($_POST['texto']))
            // si no se intenta cargar por POST
            $p_texto = $_POST['texto'];
            
        //Si se solicita referencia y se adjunta
        $v_obligatorios = $this->get_errores($v_datos);
        if($p_texto) {
            $v_denuncia_texto_narrado = $this->get_denuncia_texto_narrado($v_datos);
            $v_imagenes = $this->recuperar_imagenes("array");
            $v_datos = array("datos"=>$v_datos, "textos"=>$v_denuncia_texto_narrado, "imagenes"=>$v_imagenes, "obligatorios"=>$v_obligatorios);
        }
       
        if($p_format=='json') {
            $this->load->view('comunes/output',array('p_output'=>$v_datos));
            return;
        } else{
        	$v_datos['obligatorios'] = $v_obligatorios;
            return $v_datos;     
        }            //Formato ARRAY
    } // Fin de la funcion public get_denuncia_temp.
    
    
    /**
	 * Function public que guarda un elemento de la denuncia en curso dentro de un array.
     * @param string p_who [via POST] Cuál es el control que se esta guardando.
	 * @param string p_what [via POST] Que informacion se desea guardar.
	 * @return void
	 */
    public function put_denuncia_temp(){
    
        $p_who = $_POST['who'];     //Se utiliza este formato para recuperar POST 
        $p_what = $_POST['what'];   // porque es un Ext.Ajax
        
        if($p_who=='no_mostrar_aviso')
        	setcookie('no_mostrar_aviso',$p_what);
        
        //Recupera los datos actualmente guardado
        $v_datos_denuncias_array = $this->get_denuncia_temp();
        // print_r($v_datos_denuncias_array);
        
        //Marca que los ultimos datos fueron de Google
        if($p_who=='google_localizacion') {
            $v_datos_denuncias_array["origen_localizacion"] = "google";
            
            //Limpia los datos ya guardados del usuario
            $v_datos_denuncias_array["ciudad"] = "";
            $v_datos_denuncias_array["barrio"] = "";
            $v_datos_denuncias_array["calles"] = "";
            
            //convierte a array para poder agregarle scapeadores a los string
            $p_what = (array) json_decode($p_what);
          	//$p_what = array_map('addslashes', $p_what);
            //$p_what = stripslashes(json_encode($p_what));
        }/*else{
        	$p_what = addslashes($p_what);
        }*/
            
        //Marca que los ultimso datos fueron del usuario
        if($p_who=='ciudad' || $p_who=='barrio' || $p_who=='calles') {
            //Si antes el origen era Google se copian todos los datos generados para el Usuario
            if($v_datos_denuncias_array["origen_localizacion"]== "google") {
                $v_datos_denuncias_array["ciudad"] = $v_datos_denuncias_array["google_localizacion"]["ciudad"];
                $v_datos_denuncias_array["barrio"] = $v_datos_denuncias_array["google_localizacion"]["barrio"];
                $v_datos_denuncias_array["calles"] = $v_datos_denuncias_array["google_localizacion"]["calles"];
            }
            $v_datos_denuncias_array["origen_localizacion"] = "usuario";
        }

        //Agrega o reemplaza el valor del elemento que se pasa como parametro
        $v_datos_denuncias_array[$p_who] = $p_what;

        
       // $v_datos_denuncias_array = array_map('addslashes', $v_datos_denuncias_array);
        
        
		// Guardar todo el array en la variable de session.
		//echo serialize($v_datos_denuncias_array);
		$this->session->set_userdata("datos_denuncia", $v_datos_denuncias_array);
        
        $this->load->view('comunes/output');
    } // Fin de la funcion public put_denuncia_temp.
    
    
    /**
	 * Elimina todos los datos temporales de la denuncia.
	 * @return void
	 */
    public function delete_denuncia_temp(){
        $this->session->set_userdata("datos_denuncia", "");
        
        return;
    }
    
    /**
	 * Verifica si hay datos de denuncia en la sesion actual.
	 * @return bool
	 */
    public function denuncia_temp_iniciada(){
        
        if($this->ci->session->userdata("datos_denuncia")!="")
            return true;
        else
            return false;
    }
    
    /**
    * FIN SESIONES
    ******************************************************************************************************/
	
    
    /**
     * Metodo para mostrar la vista consultar denuncia cuando se realizo una denuncia segura o comunicacion anonima.
     */
    public function consultar_denuncia(){
    	$this->data['p_menu_actual'] = 'denuncias';
    	$this->data['selector']='lista_denuncias';
    	$this->load->view('denuncias/consulta_denuncia', $this->data);
    }
    
	/**
	 * Metodo para mostrar la vista lista de denuncias.
	 * 
	*/
	public function lista_denuncias(){
        $this->data['p_menu_actual'] = 'denuncias';
		$this->validar_permiso(array("administrador","usuario","institucion","anonimo"));
		$this->data['es_administrador'] = $this->validar_permiso(array('administrador'),true);
		$this->data['selector']='lista_denuncias';
		if($this->validar_permiso(array('anonimo'),true)){
			redirect("/denuncias/consultar_denuncia/");
			//$this->load->view('denuncias/consulta_denuncia', $this->data);
		}else{
			if($this->input->post('filtros') != null){
				$v_filtros = json_decode( $this->input->post('filtros') );
				$this->data['filtros_post'] = $v_filtros;
			}
			$this->load->view('denuncias/lista_denuncias', $this->data);
		}
	}
	
	/**
	 * Metodo que consulta al modelo y recupera un listado de denuncias.
	 * @param integer start [via GET]
	 * @param integer limit [via GET]
	 * @param integer page  [via GET]
	 * @param json    sort  [via GET]
	 * @return json
	 */
	public function consulta_denuncias(){
		$tiene_permiso = $this->validar_permiso(array("administrador","usuario","institucion"),true);
		if($tiene_permiso){
			//if($this->usuario_perfil)
			//$v_data['es_administrador'] = $this->validar_permiso(array('administrador'),true);
			
			$v_start = $this->input->get('start',true);
			$v_limit = $this->input->get('limit',true);
			$v_page  = $this->input->get('page',true);
			$v_sort  = json_decode($this->input->get('sort'));
			## posibles filtros ##
			$v_desde  = $this->input->get('fecha_desde',true);
			$v_hasta  = $this->input->get('fecha_hasta',true);
			$v_texto  = $this->input->get('texto_buscado',true);
			## fin filtros ##
			
			$v_offset = $v_limit*$v_page - $v_limit;
			$v_data['cantidadTotal'] =0;
			$v_data['success']=false;
			$v_data['datos'] = array();
			
			
			
			$consulta = $this->denuncias->get_lista_denuncias($v_limit,$v_offset,$v_sort,null,null,$v_desde,$v_hasta,$v_texto);
			$v_nro_registros = $consulta->num_rows();
			if($v_nro_registros >0){
				$v_data['datos'] = $consulta->result();
				$v_data['cantidadTotal'] = $v_data['datos'][0]->FOUND_ROWS;
				
			}
			$v_data['success']=true;
			//imprimimos salida
			$this->load->view('comunes/output',array('p_output'=>$v_data));
		}
	}
    
    /**
	 * Metodo que consulta el detalle de la denuncia.
	 * @param string p_denuncia_id [via GET]
	 */
	public function consulta_detalle_denuncia(){
		$this->validar_permiso(array("administrador","usuario","institucion"));
        $p_denuncia_id = $this->input->get('p_denuncia_id',true);
        
        $v_datos_denuncia = $this->denuncias->get_datos_denuncia($p_denuncia_id);
        
        if($v_datos_denuncia->num_rows()>0){
            $v_datos_denuncia = $v_datos_denuncia->result_array();
            $v_datos_denuncia = $v_datos_denuncia[0];
            $this->data["id_denuncia"] = $p_denuncia_id;
            $v_textos_denuncias_array = $this->get_denuncia_texto_narrado($v_datos_denuncia);
            
            $this->data["datos_denuncia"] = $v_textos_denuncias_array;
                    
            //Detalles imagenes
            $codigo_decimal = hexdec($p_denuncia_id);
            $this->data["imagenes"] = $this->denuncias->get_denuncias_detalle_imagenes($codigo_decimal);
            $this->data["imagenes"] = $this->data["imagenes"]->result_array();
            $this->load->view('denuncias/denuncias_detalle',$this->data);
        } else {
            //redirect("/denuncias/index", 'refresh');
        	redirect("/denuncias/basico", 'refresh');
            //redirect('index', 'refresh');
        }
    }

	
    public function consultar(){
		$codigo_consulta = trim($this->input->post('ticket_consulta',true));
		$codigo_consulta = strtolower($codigo_consulta);
		if($this->validarHex($codigo_consulta)){
			$datos = $this->denuncias->get_datos_denuncia($codigo_consulta);
			$this->data['hexadecimal_ok'] =true;
			if($datos->num_rows()>0){
				$this->data['v_titulo'] = '<h3>Resultado de la consulta</h3>';
				//@todo cambiar el mensaje por algo que indique el estado real
				$registro = $datos->row();
				$estado = $registro->DENUNCIA_ESTADO;
				if($estado == null || $estado == ''){
					$this->data['v_mensaje'] ='<p>Su denuncia est&aacute; sin novedades.</p>';
				}else{
					$this->data['v_mensaje'] ="<p>El estado de su denuncia es: $estado</p>";
				}
				
			}else{
				$this->data['v_titulo'] = '<h3>Resultado de la consulta</h3>';
				$this->data['v_mensaje'] ='No tenemos registros con este ticket de consulta.';
			}
		}else{
			$this->data['v_titulo'] = '<h3>Error</h3>';
			$this->data['v_mensaje'] ='El ticket prove&iacute;do no es correcto.';
		}
		$this->load->view('comunes/informaciones',$this->data);
	
	}
	
	private function validarHex($numero_hex){
		$dec = hexdec($numero_hex);
		if($numero_hex==dechex($dec)){
			return true;
		}
		return false;
	}
	
	/**
	 * Funcion que arma un array con nodo hoja o nodo hijo dependiendo de la latitud y longitud.
	 * Ejemplo: si en el mismo punto (lat,lon) tiene varias denuncias, entonces tiene un padre con hijos;
	 * caso contrario es nodo hoja.
	 * Despues de armar todo, se envia esa nueva estrutura al mapa.
	 * @param json $puntos_json [via POST]
	 * @param json $puntos_json [via POST]
	 * @return void
	 */
	public function consultarEnMapa(){
		$this->data['p_menu_actual'] = 'denuncias';
		$this->data['selector']='consultarEnMapa';
		$this->data['es_administrador'] = $this->validar_permiso(array('administrador'),true);
		//$puntos_json = $this->input->post('puntos',true);
		$filtros = json_decode($this->input->post('filtros',true));
		
		$this->data['filtros_post'] = $filtros;
		//$this->data['filtros']=$filtros;
		//$this->data['filtros'] =$filtros_json;
		$v_start = $this->input->get('start',true);
		$v_limit = $this->input->get('limit',true);
		$v_page  = $this->input->get('page',true);
		$v_sort  = json_decode($this->input->get('sort'));
		## posibles filtros ##
		if($filtros!=null){
			$v_desde = $filtros->fecha_desde;
			$v_hasta = $filtros->fecha_hasta;
			$v_texto = $filtros->texto_buscado;	
			$v_limit = 100;
			$v_page =1;
			$v_sort = null;
			
		}else{
			$v_desde  = $this->input->get('fecha_desde',true);
			$v_hasta  = $this->input->get('fecha_hasta',true);
			$v_texto  = $this->input->get('texto_buscado',true);
		}
		## fin filtros ##		
		$v_offset = $v_limit*$v_page - $v_limit;
		$v_data['cantidadTotal'] =0;
		$v_data['success']=false;
		$v_data['datos'] = array();		
		$consulta = $this->denuncias->get_lista_denuncias($v_limit,$v_offset,$v_sort,null,null,$v_desde,$v_hasta,$v_texto);
		$puntos = array();
		$contador_denuncias_omitidos =0;
		$contador_denuncias_ubicados =0;
		if($consulta->num_rows()>0){
			foreach ($consulta->result() as $punto){
				$tmp = new stdClass();
				$tmp->latitud = $punto->DENUNCIA_LATITUD;
				$tmp->longitud = $punto->DENUNCIA_LONGITUD;
				$tmp->fecha_registro = $punto->DENUNCIA_FECHA_REGISTRO;
				$tmp->denuncia_estado = $punto->DENUNCIA_ESTADO;
				$tmp->denuncia_id = $punto->DENUNCIA_ID;
				$tmp->estado = $punto->DENUNCIA_ESTADO;
				if($tmp->latitud!=null&&$tmp->latitud!=''){
					$contador_denuncias_ubicados++;
					$puntos[]=$tmp;
				}else{
					$contador_denuncias_omitidos++;
				}
			}
		}
		$this->data['contador_omitidos'] =$contador_denuncias_omitidos;
		$this->data['contador_ubicados'] =$contador_denuncias_ubicados;
		//$puntos = json_decode($puntos_json);
		
		
		// Ordenar el array de objetos.
		/*
		 * Funcion interna para comparar la latitud
		 * @param double $p_punto1
		 * @param double $p_punto2
		 * @return integer
		 */
		function cmp($p_punto1, $p_punto2){
			if (($p_punto1->latitud == $p_punto2->latitud)){
				return 0;
			}elseif($p_punto1->longitud == $p_punto2->longitud){
				return 0;
			}else
				return ($p_punto1->latitud < $p_punto2->latitud) ? -1 : 1;
		}
		usort($puntos, "cmp");
		
		//print_r($puntos);

		$v_indice_nodo = 0;
		$v_indice_hijos_denuncia = 0;
		$v_primer_nodo = true;
		$v_es_hoja = false;
		
		for($i = 0; $i < count ($puntos); $i++){
			//echo "<br>Decimal: " . $puntos[$i]->denuncia_id . " hexa: " . dechex($puntos[$i]->denuncia_id);
			if($v_primer_nodo){
				$puntos_nodos[$v_indice_nodo]->latitud  = $puntos[$i]->latitud;
				$puntos_nodos[$v_indice_nodo]->longitud = $puntos[$i]->longitud;
				$puntos_nodos[$v_indice_nodo]->cantidad = 0;
				$v_primer_nodo = false;
			}
			if($v_es_hoja){
				$puntos_nodos[$v_indice_nodo]->latitud  = $puntos[$i]->latitud;
				$puntos_nodos[$v_indice_nodo]->longitud = $puntos[$i]->longitud;
				$puntos_nodos[$v_indice_nodo]->cantidad = 0;
				$v_es_hoja = false;
			}
		
			//if(isset($puntos[$i+1]->latitud) && $puntos[$i]->latitud == $puntos[$i+1]->latitud){
			if(isset($puntos[$i+1]->latitud) && ($puntos[$i]->latitud == $puntos[$i+1]->latitud) && ($puntos[$i]->longitud == $puntos[$i+1]->longitud)){
				// Nodo Padre e hijos.
				$puntos_nodos[$v_indice_nodo]->cantidad = $puntos_nodos[$v_indice_nodo]->cantidad + 1;
				$puntos_nodos[$v_indice_nodo]->estado = 'null';
				$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->id_denuncia = /*dechex*/($puntos[$i]->denuncia_id);
				$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->estado = $puntos[$i]->denuncia_estado;
				$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->fecha_registro = $puntos[$i]->fecha_registro;
				$v_indice_hijos_denuncia = $v_indice_hijos_denuncia + 1;
			}else{
				if(isset($puntos_nodos[$v_indice_nodo]->estado) && $puntos_nodos[$v_indice_nodo]->estado == 'null'){
					$puntos_nodos[$v_indice_nodo]->cantidad = $puntos_nodos[$v_indice_nodo]->cantidad + 1;
					$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->id_denuncia = /*dechex*/($puntos[$i]->denuncia_id);
					$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->estado = $puntos[$i]->denuncia_estado;
					$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->fecha_registro = $puntos[$i]->fecha_registro;
				}else{
					// Nodo hoja.
					$puntos_nodos[$v_indice_nodo]->cantidad = 1;
					$puntos_nodos[$v_indice_nodo]->estado = $puntos[$i]->denuncia_estado;
					$puntos_nodos[$v_indice_nodo]->id_denuncia = /*dechex*/($puntos[$i]->denuncia_id);
					$puntos_nodos[$v_indice_nodo]->fecha_registro = $puntos[$i]->fecha_registro;	
				}
				$v_indice_nodo = $v_indice_nodo + 1;
				$v_indice_hijos_denuncia = 0;
				$v_es_hoja = true;	
			}
		}
/*
		echo "<br><br>El nuevo array es: <br>";
		print_r($puntos_nodos);
		echo "<br>Fin del nuevo array!!!";
*/
		//$this->data['puntos'] = $puntos;
		$this->data['puntos'] = $puntos_nodos;
		foreach($filtros as $key=>$value){
			if(!empty($value)){
				$this->data['filtros'][$key]=$value;
			}
		}
	    $this->load->view('denuncias/lista_denuncia_mapa',$this->data);
	}
	
    /**
	 * Sube una imagen al servidor en forma temporal, en el paso 2:
	 * @param file imagen [via POST] Campo imagen del segundo formulario.
	 * @return void
	 */
	public function subir_imagen(){
		if($_FILES['imagen_denuncia']['error'] == 0){
			$v_cant =  count(glob(PATH_IMG_TMP."/{".$this->session->userdata["session_id"]."*.jpg,".$this->session->userdata["session_id"]."*.gif,".$this->session->userdata["session_id"]."*.png}",GLOB_BRACE));
			if($v_cant>4){
				$v_data=array('success'=> false, 'mensaje'=> "Solo puede subir hasta 5 im&aacute;genes.");
				$this->load->view('comunes/output',array('p_output'=>$v_data));
			}else{
	            //upload and update the file
	            $config['upload_path'] = PATH_IMG_TMP;
	            $config['allowed_types'] = 'gif|jpg|png';
	            $config['overwrite'] = false;
	            $config['remove_spaces'] = true;
	            
	            $this->load->library('upload', $config);
	
	            if ( ! $this->upload->do_upload('imagen_denuncia')){
	                $v_data=array('success'=> false, 'mensaje'=> $this->upload->display_errors());
	                $this->load->view('comunes/output',array('p_output'=>$v_data));
	            }else{
	                $v_file_data = $this->upload->data();
	                
	                //Extension
	                $v_extension = ".jpg";
	                switch($v_file_data["file_type"]) {
	                    case "image/png":
	                        $v_extension = ".png";
	                        break;
	                    case "image/gif":
	                        $v_extension = ".gif";
	                        break;
	                }
	               	//$v_nombre_archivo = $this->session->userdata["session_id"]."_".$v_cant.$v_extension;
	               	$v_nombre_archivo = $this->session->userdata["session_id"]. time() ."_".$v_cant.$v_extension;
	             	rename($this->upload->upload_path.$this->upload->file_name, $this->upload->upload_path.$v_nombre_archivo);
	                $v_data=array('success'=> true, 'name'=> $v_nombre_archivo, 'debug'=> $v_file_data["file_type"]);
	                $this->load->view('comunes/output',array('p_output'=>$v_data));
	            }
       		}
		}else{
			$v_data=array('success'=> false, 'mensaje'=> "No puede subir im&aacute;genes con m&aacute;s de 2 mb.");
			$this->load->view('comunes/output',array('p_output'=>$v_data));
		}
		
	} // Fin de la funcion publica denuncias_paso2.
	
	/**
	 * borrar imagen
	 * @param file name [via POST] 
	 * @return json
	 */
	public function borrar_imagen(){
		$v_nombre_imagen = $this->input->post('nombre_imagen', true);
		@unlink(PATH_IMG_TMP.'/'.$v_nombre_imagen);
		//@unlink(PATH_IMG_TMP.'/'.str_replace('_mediano','_mediano',$v_nombre_imagen));
		//@unlink(PATH_IMG_TMP.'/'.str_replace('_mediano','_chico',$v_nombre_imagen));
		$v_data=array('success'=> true, 'mensaje'=> "La imagen ha sido borrada exitosamente.");
		$this->load->view('comunes/output',array('p_output'=>$v_data));
	}
    
	/**
	 * recupera las imagenes cargadas previamente
     * @param p_format formato que decide si va a ser un array o JSON
	 * @return json/array
	 */
	public function recuperar_imagenes($p_format="json"){
		$v_imgs = glob(PATH_IMG_TMP."/{".$this->session->userdata["session_id"]."*.jpg,".$this->session->userdata["session_id"]."*.gif,".$this->session->userdata["session_id"]."*.png}",GLOB_BRACE);
        $v_nombres = array();
		
		foreach ($v_imgs as $v_img)
			$v_nombres[]=array_pop(explode("/",$v_img));
		
        if(!isset($p_format) || $p_format=="" || $p_format=='json') {
            $v_data=array('success'=> true, 'img'=> $v_nombres);
            $this->load->view('comunes/output',array('p_output'=>$v_data));
            
        } else {
            return $v_nombres;
        }
	}
	
	/**
	 * recupera las imagenes cargadas previamente, las copia en el paht final y guarda los nombres en la bd
	 * @return array
	 */
	public function guardar_imagenes(){
		$v_imgs = glob(PATH_IMG_TMP."/{".$this->session->userdata["session_id"]."*.jpg,".$this->session->userdata["session_id"]."*.gif,".$this->session->userdata["session_id"]."*.png}",GLOB_BRACE);
		$v_archivos = array();
		if($this->seguridad->logged())
			$v_usuario_id = $this->session->userdata("usuario")->USUARIO_ID;
		else
			$v_usuario_id = 'anonimo';
		foreach ($v_imgs as $v_img){
			$v_nombre = array_pop(explode("/",$v_img));
			$v_nombre_nuevo = str_replace($this->session->userdata["session_id"], $v_usuario_id.mktime(), $v_nombre);
			$v_archivos[] = $v_nombre_nuevo;
			rename($v_img, PATH_IMG_ORIGINAL.'/'.$v_nombre_nuevo); 
		}
		return $v_archivos;
	}
	
	/**
	 * Function publica que va a la vista lista_denuncia_mapa.
	 * @return void
	 */
	public function lista_denuncia_mapa(){
		//$this->validar_permiso(array("administrador"));
		$this->load->view('denuncias/lista_denuncia_mapa', $this->data);
	} // Fin de la funcion publica lista_denuncia_mapa.
	
	private function get_errores($v_denuncias_tmp){
		$v_errores = array();
		//print_r($v_denuncias_tmp);
		if($this->session->userdata("extendida")||$this->session->userdata("finalizar")){
			if(!isset($v_denuncias_tmp['tipo_denuncia'])||$v_denuncias_tmp['tipo_denuncia']==""){
				$v_errores[]=array("Debe elegir un tipo de denuncia.","basico");
			}
			if(!isset($v_denuncias_tmp['fecha_inicial'])||$v_denuncias_tmp['fecha_inicial']==""){
				$v_errores[]=array("Debe se&ntilde;alar una fecha o un per&iacute;odo de tiempo.","basico");
			}
			if((!isset($v_denuncias_tmp['ciudad'])||$v_denuncias_tmp['ciudad']=="")&&(!isset($v_denuncias_tmp['google_localizacion']['ciudad'])||$v_denuncias_tmp['google_localizacion']['ciudad']=="")){
				$v_errores[]=array("Debe elegir una ciudad.","basico");
			}
			if((!isset($v_denuncias_tmp['calles'])||$v_denuncias_tmp['calles']=="")&&(!isset($v_denuncias_tmp['google_localizacion']['calles'])||$v_denuncias_tmp['google_localizacion']['calles']=="")){
				$v_errores[]=array("Debe elegir una calle.","basico");
			}
			if(!isset($v_denuncias_tmp['rescate_inmediato'])||$v_denuncias_tmp['rescate_inmediato']==""){
				$v_errores[]=array("Debe marcar si se trata de un rescate inmediato o no.","basico");
			}
		}
		if(!$this->seguridad->logged()){
			if($this->session->userdata("extendida")||$this->session->userdata("finalizar")){
				if(!isset($v_denuncias_tmp['usted_victima'])||$v_denuncias_tmp['usted_victima']==""){
					$v_errores[]=array("Debe marcar si es usted v&iacute;ctima o no.","basico");
				}
				if(!isset($v_denuncias_tmp['sospechas'])||$v_denuncias_tmp['sospechas']==""){
					$v_errores[]=array("Debe ingresar alguna descripci&oacute;n en sospechas.","basico");
				}
			}
			if($this->session->userdata("finalizar")){
				if(!isset($v_denuncias_tmp['rango_edad'])||$v_denuncias_tmp['rango_edad']==""){
					$v_errores[]=array("Debe elegir un rango de edad.","extendido");
				}
				if(!isset($v_denuncias_tmp['dato_victima'])||$v_denuncias_tmp['dato_victima']==""){
					$v_errores[]=array("Debe indicar algunos datos de la persona a ser rescatada.","extendido");
				}
				if(!isset($v_denuncias_tmp['contacto_victima'])||$v_denuncias_tmp['contacto_victima']==""){
					$v_errores[]=array("Debe indicar si la v&iacute;ctima tiene alguna forma de contacto y cu&aacute;l es.","extendido");
				}
			}
		}
		return $v_errores;
		//$v_data=array('success'=> true, 'obligatorios'=> $v_errores);
		//$this->load->view('comunes/output',array('p_output'=>$v_data));		
	}
	
	/**
	 * Function que retorna los campos obligatorios
	 * @return array
	 */
	private function get_campos_obligatorios(){
		$v_obligatorios['tipo_denuncia']=1;
		$v_obligatorios['cuando']=1;
		$v_obligatorios['ciudad']=1;
		$v_obligatorios['calle']=1;
		$v_obligatorios['rescate_inmediato']=1;
		if(!$this->seguridad->logged()){
			$v_obligatorios['usted_victima']=1;
			$v_obligatorios['describir_sospechas']=1;	
			//extendido
			$v_obligatorios['rango_edad']=1;
			$v_obligatorios['dato_persona']=1;
			$v_obligatorios['forma_contacto']=1;	
		}
		return $v_obligatorios;		
	} // Fin de la funcion publica lista_denuncia_mapa.
	
	/**
	 * Funcion que envia un mail a la persona responsable enviando la denuncia que se realizo. Se usa como constancia.
	 * @param array $p_datos
	 * @param array $p_imagenes
	 * @return boolean
	 */
	private function enviar_email_persona_responsable($p_datos, $p_imagenes, $p_id_denuncia_hexadecimal){
		// Se carga la libreria de email. Se se encuentra configurado el smtp en el archivo (System -> libraries -> Email.php)
		$this->load->library('email');
		
		$config['protocol'] = 'sendmail';
    	$config['mailpath'] = '/usr/sbin/sendmail';
    	//$config['charset'] = 'iso-8859-1'; usa el que esta por defecto en la libreria
    	$config['wordwrap'] = TRUE;
    	
    	$this->email->initialize($config);
    	
		/*
		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.googlemail.com';
		$config['smtp_user'] = '';
		$config['smtp_pass'] = '';
		$config['smtp_port'] = '465';
		$this->email->initialize($config);
		*/
		
		$p_denuncia = $p_datos["datos"];
		$v_textos = $this->get_denuncia_texto_narrado($p_denuncia);
		
		/*
		 * Se obiene los datos del usuario. 
		 */
		if(isset($this->session->userdata('usuario')->USUARIO_EMAIL)){
			$v_email = $this->session->userdata('usuario')->USUARIO_EMAIL;
			$v_usuario = $this->usuarios->get_datos_usuario($v_email);
			$v_institucion_usuario = $this->usuarios->get_institucion_usuario($v_email);		// Obtener institucion.
		}
		
		// El asunto del mail.
		$v_asunto_mensaje = "El TICKET de la denuncia es: <b>". $p_id_denuncia_hexadecimal . "</b><br><br>";
			
		// Armar una parte del asunto del mail. En esta parte se coloca los datos de la denuncia.
		foreach($v_textos as $v_elemento) {
			if(trim($v_elemento) == ""){
				continue;
			}

			if($v_textos['tipo_denuncia'] == $v_elemento ){
				$v_asunto_mensaje = $v_asunto_mensaje . "<b>". $v_elemento . "</b>.<br>";
			}elseif($v_textos['localizacion'] == $v_elemento || $v_textos['cuando'] == $v_elemento || $v_textos['rescate_inmediato'] == $v_elemento || (isset($v_textos['usted_victima']) && $v_textos['usted_victima'] == $v_elemento)
					|| (isset($v_textos['sospechas']) && $v_textos['sospechas'] == $v_elemento) || $v_textos['tipo_explotacion_1'] == $v_elemento || $v_textos['tipo_explotacion_2'] == $v_elemento || $v_textos['tipo_explotacion_3'] == $v_elemento
					|| $v_textos['tipo_explotacion_4'] == $v_elemento || $v_textos['tipo_explotacion_5'] == $v_elemento || $v_textos['tipo_explotacion_6'] == $v_elemento || $v_textos['victimas'] == $v_elemento
					|| $v_textos['sexo'] == $v_elemento || $v_textos['nombre_explotador'] == $v_elemento || $v_textos['dato_victima'] == $v_elemento || $v_textos['fecha_ultimo_contacto'] == $v_elemento
					|| $v_textos['contacto_victima'] == $v_elemento){
				$v_asunto_mensaje = $v_asunto_mensaje . $v_elemento . "<br>";
			}elseif($v_textos['latitud_longitud_str'] == $v_elemento){
				$v_asunto_mensaje = $v_asunto_mensaje . " Las coordenadas geogr&aacute;ficas son: " . $v_elemento . " (latitud, longitud).<br>" ;
			}elseif($v_textos['zoom_mapa']  == $v_elemento || $v_textos['tipo_mapa'] == $v_elemento){
				;
			}elseif($v_textos['ciudad']  == $v_elemento || $v_textos['barrio'] == $v_elemento || (isset($v_textos['calle']) && $v_textos['calle'] == $v_elemento)){
				;
			}
		}
		
		$v_asunto_mensaje = $v_asunto_mensaje . "<br><br>";
		
		if(isset($v_usuario['USUARIO_NOMBRE']) || isset($v_usuario['USUARIO_APELLIDO']) || isset($v_usuario['USUARIO_CELULAR'])){
			$v_asunto_mensaje = $v_asunto_mensaje . "<b>Datos del Denunciante:</b><br>";
			$v_asunto_mensaje = $v_asunto_mensaje . "<b>Email: </b>". $v_email . "<br>";
		}
		if((isset($p_datos["datos"]['email']) && $p_datos["datos"]['email'] != "") 
				|| ((isset($p_datos["datos"]['nombre_apellido']) && $p_datos["datos"]['nombre_apellido'] != "")) 
				|| ((isset($p_datos["datos"]['telefono']) && $p_datos["datos"]['telefono'] != ""))){
			$v_asunto_mensaje = $v_asunto_mensaje . "<b>Datos del Denunciante:</b><br>";
		}

		// Armar una parte del asunto del mail. En esta parte se coloca los datos del usuario registrado. En este caso podria no aparecer sus datos.
		if(isset($v_usuario)){
			foreach($v_usuario as $v_elemento) {
				if(trim($v_elemento) == ""){
					continue;
				}
			
				if($v_usuario['USUARIO_NOMBRE'] == $v_elemento ){
					$v_asunto_mensaje = $v_asunto_mensaje . "<b>Nombre: </b>". $v_elemento . "<br>";
				}elseif($v_usuario['USUARIO_APELLIDO'] == $v_elemento ){
					$v_asunto_mensaje = $v_asunto_mensaje . "<b>Apellido: </b>". $v_elemento . "<br>";
				}elseif($v_usuario['USUARIO_CELULAR'] == $v_elemento ){
					$v_asunto_mensaje = $v_asunto_mensaje . "<b>Celular: </b>". $v_elemento . "<br>";
				}
			}
			// Para saber si el usuario esta en una institucion.
			if(isset($v_institucion_usuario) && $v_institucion_usuario != -1){
				$v_asunto_mensaje = $v_asunto_mensaje . "<b>Institución: </b>". $v_institucion_usuario . "<br>";
			}
		} // Fin del if.
		
		// Armar una parte del asunto del mail. En esta parte se coloca los datos del anonimo en caso que especifico. En este caso podria no aparecer algunos datos.
		if(isset($p_datos["datos"]['email']) && $p_datos["datos"]['email'] != ""){
			$v_asunto_mensaje = $v_asunto_mensaje . "<b>Email: </b>". $p_datos["datos"]['email'] . "<br>";
		}
		if(isset($p_datos["datos"]['nombre_apellido']) && $p_datos["datos"]['nombre_apellido'] != ""){
				$v_asunto_mensaje = $v_asunto_mensaje . "<b>Nombre: </b>". $p_datos["datos"]['nombre_apellido'] . "<br>";
		}
		if(isset($p_datos["datos"]['telefono']) && $p_datos["datos"]['telefono'] != ""){
			$v_asunto_mensaje = $v_asunto_mensaje . "<b>T&eacute;lefono: </b>". $p_datos["datos"]['telefono'] . "<br>";
		}

		// Parametros para la construccion del email.
		$v_from = EMAIL_ADMINISTRADOR;
		$v_to = EMAIL_PERSONA_RESPONSABLE;
		$v_subject = "Denuncia recibida " . $p_id_denuncia_hexadecimal . " - Sistema Trata";
	
		$this->email->set_newline("\r\n");
		$this->email->from($v_from, 'Ministerio Público');
		$this->email->to($v_to);
		$this->email->subject($v_subject);
		$this->email->message($v_asunto_mensaje);
		
		foreach($p_imagenes as $v_imagen) {
			// Se adjuntan las imagenes
			$this->email->attach(PATH_IMG_ORIGINAL . "/" .  $v_imagen);
		}
	
		if($this->email->send()){
			return true;
		}else{
			//show_error($this->email->print_debugger());
			return false;
		}
	} // Fin de la funcion publica enviar_email. 
} // Fin del controlador denuncias.