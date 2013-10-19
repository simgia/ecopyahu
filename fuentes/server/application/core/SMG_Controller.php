<?php
/**
 * Super controlador  para Conacyt.
 * Todos los controladores que tienen funcionalidades en comun haran un extends de este controlador
 * @author Juan Bauer
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 */
class SMG_Controller extends CI_Controller{
        /**
         * @var Seguridad_M
         */
        var $seguridad;
       /**
       * Instancia del Singleton Pattern del CI
       * @var CI_Base
       */
       var $ci;
       /**
        * @var CI_Loader
        */
       var $load;
       /**
        * @var CI_Session
        */
       var $session;
       
       /**
        * @var $usuario_perfil
        */
       var $usuario_perfil;
			/**
        * @var $fecha_hora_actual
        */
       var $fecha_hora_actual;       
       
       /**
        * Aqui va la lista de acciones disponibles en total para el 
        * sistema
        */
       const LISTAR = 'LISTAR';
       const MODIFICAR  = 'MODIFICAR';
       const CREAR = 'CREAR';
       const BORRAR='BORRAR';
       //const BUSCAR = 'BUSCAR';
       /**
        * Desde aqui la lista de modulos del sistema 
        */
       const MODULO_USUARIOS = 'usuarios';
       const MODULO_PERFILES = 'perfiles';
       const MODULO_MODULOS = 'modulos';
       const MODULO_SEGURIDAD = 'seguridad';
       
    /**
     * Consutructor.
     * Verifica si esta logeado y mantiene con vida la sesion, redirecciona al inicio de la aplicacion.
     */
    public function __construct(){	
    	parent::__construct();
    	$this->ci =& get_instance();
    	$this->db = $this->ci->db;
    	
    	$this->fecha_hora_actual = $this->db->query('select now() as fecha;')->row()->fecha;
    	
    	$this->load->model('seguridad_m','seguridad');
    	srand(time()); //inicia semilla
    	if ((rand() % 100) < 5) //probabilidad menor a 5%
    	{
    		$this->db->where("estado",'inactivo'); //se borra los usuarios que esten inactivos
    		$this->db->delete('sesiones');
    		log_message('debug', 'Borrada la tabla de sessiones inactivas del sistema'); //escribimos en el log
    	}
                $this->verificacion_inicial();
    }

    
    public function verificacion_inicial(){
    	if ($this->session->userdata(LOGGED)===true  && $this->seguridad->is_killed_session()===false){
    		$this->seguridad->keepAlive();
    		$this->usuario_modulos_acciones = $this->seguridad->getModulosUsuario();
    		
                                //FALTA ARMAR EL MENU DE A CUERDO A LOS MODULOS Y ACCIONES A LOS QUE TIENE PERMISO EL USUARIO
                                /*$menu_usuario =array(
    				'editar'=>array("mostrar"=>1,"nombre"=>"Editar Perfil","url"=>"usuarios/editar_usuario"),
    				'cambiar_pass'=>array("mostrar"=>1,"nombre"=>"Cambiar Contraseña","url"=>"usuarios/cambiar_password"),
    				'logout'=>array("mostrar"=>1,"nombre"=>"Salir","url"=>"seguridad/logout")
    		);
    		if($this->usuario_perfil == 'administrador'){
    			$menu["lista_usuario"] = array("mostrar"=>1,"nombre"=>"Ver Usuarios","url"=>"usuarios/lista_usuarios");
    		}*/
    		$menu["menu_usuario"] = $menu_usuario;
    		$menu["usuario"] = $this->session->userdata('usuario')->USUARIO_EMAIL;
    		$menu["ver_denuncias"] = array("mostrar"=>1,"nombre"=>"Denuncias","url"=>"denuncias/lista_denuncias");
    		$menu["mostrar_menu"] ='usuario';
    		$this->data = array('menu'=>$menu);
    	}else{
    		$this->usuario_perfil = "anonimo";
    		 
                //MENU BASICO
    		$menu_usuario = array(
    				'login'=>array("mostrar"=>1,"nombre"=>"Iniciar Sesi&oacute;n","url"=>"seguridad/login"),
    				'registrarse'=>array("mostrar"=>1,"nombre"=>"Registrarse","url"=>"usuarios/registro_usuario")
    		);
    		 
    		$menu = array("menu_usuario"=>$menu_usuario);
    		$this->data = array('menu'=>$menu);
    	}
    }
    
    public function activar_profiler(){
    	$this->ci->output->enable_profiler(true);
    }
    
    /**
     * Comprueba si la llamada actual es una llamada AJAX
     * @return boolean
     */
    public function is_ajax_request(){
    	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest');
    }

    /**
     * validar si tipo de usuario tiene el permiso que se pregunta por
     * @param String $p_modulo
     * @param String $p_accion CREAR|BORRAR|MODIFICAR|BUSCAR|LISTAR
     * @param boolean $no_tomar_accion si se envia TRUE no hace nada y devuelve el boolean
     * @return boolean
     */
    public function validar_permiso($p_modulo,$p_accion,$no_tomar_accion=false){
    	$v_permiso = false;
    	$this->load->library('session');
    	$query = $this->seguridad->getModulosAccion();
    	$v_data['tiene_permiso'] = false;
    	if($query->num_rows() > 0){
    		foreach($query->result() as $ma){
    			if($ma->modulo_nombre_corto==$p_modulo && $ma->accion_nombre == $p_accion){
    				return true;
    			}
    		}
    	}
    	if(!$no_tomar_accion){
    		$this->denegar_acceso();
    	}
    	return false;
    }
    /**
     * Imprime JSON de error y finaliza la ejecucion del sistema
     * @param boolean $logout si queremos que se envie el parametro de logout
     */
    public function denegar_acceso($logout=false,$redirect=false,$mensaje='Acceso denegado'){
    	$v_data['mensaje'] = $mensaje;
    	$v_data['success'] = false;
    	$v_data['datos'] = array();
    	$v_data['resultado'] = false;
    	$v_data['logout'] = $logout;
    	$v_data['redirect'] = $redirect;
	$v_data['error'] = 'acceso_denegado';
    	
    	$this->load->view('output',array('p_output'=>$v_data));
    	echo $this->output->get_output();
    	exit;
    }
    
    /**
     * Funcion que envia un mail.
     * @param string $p_mail_envio Destinatario
     * @param string $p_mensaje Mensaje
     * @param string $p_subject Opcional Asunto del mensaje
     * @return boolean
     */
    public function enviar_email($p_mail_envio, $p_mensaje, $p_subject = ""){
    	// Se carga la libreria de email. Se se encuentra configurado el smtp en el archivo (System -> libraries -> Email.php)
    	$this->load->library('email');
    	
    	$config['protocol'] = 'sendmail';
    	$config['mailpath'] = '/usr/sbin/sendmail';
    	//$config['charset'] = 'iso-8859-1'; usa el que esta por defecto en la libreria
    	$config['wordwrap'] = TRUE;
    	
    	$this->email->initialize($config);
    
    	// Parametros para la construccion del email.
    	$v_from = EMAIL_ADDRESS;
                $v_from_desc = EMAIL_ADDRESS_NAME;
    	$v_to = $p_mail_envio;
    	$v_subject = $p_subject;
    
    	$this->email->set_newline("\r\n");
    	$this->email->from($v_from, FROM_EMAIL_DESC);
    	$this->email->to($v_to);
    	$this->email->subject($v_subject);
    	$this->email->message($p_mensaje);
    
    	if($this->email->send()){
    		return true;
    	}else{
    		//show_error($this->email->print_debugger());
    		return false;
    	}
    } // Fin de la funcion publica enviar_email.
}