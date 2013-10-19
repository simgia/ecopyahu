<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author pablo ruiz diaz
 *  @author juan bauer bauerpy@gmail.com
 * @package ecopyahu 
 */

/**
 * Controller que maneja los valores relacionados al 
 * - Login
 * - Logout
 * - Validacion de Sesiones
 * - Comprobacion y recuperaci�n de m�dulos
 *
 */
class Seguridad extends CI_Controller {
	
	/**
	 * @var Seguridad_M
	 */
	var $seguridad;
	/**
	 * Constructor donde levanta las librerias:
	 * - form_validation: sirve para validar el login del usuario.
	 * - session
	 * - model seguridad.
	 */
	public function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('Seguridad_M', 'seguridad');
		
	}
	
	/**
	 * Metodo para iniciar sesion del usuario.
	 * Recibe por POST
	 * @var POST['usuario]
	 * @var POST['password']	 * 
	 * @return json
	 */
	public function login(){
		//Correccion de error, si estaba logueado y reintenta loguearse, sale error
		//insertar en la base de datos.
		if($this->seguridad->logged()){			
			redirect('/'.$this->uri->segment(3));
		}
        //echo "redireccionando...";
        // $this->load->view('inicio/procesando');
		//Variables recibidas por POST
		$p_user 	= $this->input->post('usuario', true);
		$p_password = $this->input->post('password',true);
		$p_perfil   = $this->uri->segment(3); //Es user o admin 
		$v_admin    = ($p_perfil == 'admin' ? true : false);
		
		//comprobacion de valores recibidos
		if($p_user === false || $p_password ===false){
			$p_user = '**anonymous**';
			$p_password = '**anonymous**';
		}
		//encriptacion del password
		$p_password = md5($p_password);
		//array de salida de datos
		$v_output = array('success'=>true);

		if($this->seguridad->login($p_user,$p_password) === true){
			$modulos = $this->seguridad->getPerfilesUsuario();
			
			$esAdmin=false;
			//busqueda de perfil Administrador
			foreach($modulos->result() as $modulo){
				if($modulo->perfil_nombre=='Administrador'){
					$esAdmin=true;
					break;					
				}
			}

			//Exito
            if ($v_admin && $esAdmin) {
				redirect('/admin', 'refresh');
            } else {
            	redirect('/user', 'refresh');
            }
		}else{
			//Error
			$v_output['resultado'] = false;
			$v_output['mensaje'] = 'Acceso denegado. Usuario y/o Password err&oacute;neo.';
            $v_data = array('p_output'=>$v_output);	
            $this->session->set_flashdata('errores', $v_data);
            if ($p_perfil == 'admin' || $p_perfil == 'user') {
            	//echo "Error ".$p_perfil;
            	redirect('/'.$p_perfil, 'refresh');
            }
            else{
            	//echo "Error ";
            	redirect('/user', 'refresh');
            }
		}
		//Encapsula el array en otro array para que la vista pueda procesarlo como array.
		// $v_data = array('p_output'=>$v_output);
		//llamada a la vista	
		// $this->load->view('output', $v_data);
	}// Fin del metodo login.
	
	/**
	 * Metodo para cerrar sesion del usuario.
	 * @return void
	 */	
	public function logout(){
		$this->seguridad->logout();
		$v_output = array("success"=>true, "mensaje"=>'','resultado'=>true);
		$v_data = array('p_output'=>$v_output);	
		// Encapsula el array en otro array para que la vista pueda procesarlo como array.
		$this->load->view('output', $v_data);
	}// Fin del metodo logout.
	
	/**
	 * Revisa que el usuario este logueado para acceder al sistema.
	 * @author Pablo
	 * @return void
	 */
	public function validarSesion(){
		if($this->seguridad->logged()){
			$v_output = array(	"success"=>true,
								'now'=>date('H:i:s d/m/Y'),
								'resultado'=>true,
								'last_activity_date'=>date('H:i:s d/m/Y',$this->session->userdata('last_activity')),
								"datos_usuario"=>$this->seguridad->getDatosUsuario(), 
								"es_admin"=>$this->seguridad->esAdmin(),
								"mensaje"=>'Sesion Valida');
		}else{
			$v_output = array(	"success"=>true,
								'resultado'=>false,
								"mensaje"=>'La session ha expirado');
		}
		sleep(1);
		$v_data = array('p_output'=>$v_output);
		$this->load->view('output',$v_data);
	}
	/**
	 * Recupera la lista de modulos a los que tiene
	 * acceso el usuario actual
	 * @return {json}
	 */
	public function recuperarModulos(){
		
		if(!$this->seguridad->logged()){
		
			redirect($this->router->routes['default_controller']);			
		}
		$this->load->library('session');
		$v_data['modulos_permiso'] = array();
		$v_data['resultado'] = false;
		$r = $this->seguridad->getModulosUsuario();
		if($r->num_rows()>0){
			$v_data['resultado'] = true;
			$v_data['modulos_permiso'] = $r->result();
		}
		$v_data['success'] = true;
		
		$this->load->view('output',array('p_output'=>$v_data));
	}
	
	/**
	 * Comprueba si el usuario actual tiene acceso
	 * al modulo solicitado.
	 * @deprecated
	 * @see comprobarAccionModulo
	 */
	public function comprobarModulo(){
		if(!$this->seguridad->logged()){
		
			redirect($this->router->routes['default_controller']);
		}
		$p_modulo = $this->input->get('modulo');
		$this->load->library('session');		
		$r = $this->seguridad->getModulosUsuario();
		$v_data['tiene_permiso'] = false;
		//$v_data['permiso_nivel'] = 0;		
		if($r->num_rows()>0){
			$modulos_permiso = $r->result();
			foreach($modulos_permiso as $mod){
				if($mod->modulo_descripcion == $p_modulo){
					$v_data['tiene_permiso'] = true;
					break;
				}
			}
			
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	/**
	 * Este metodo esta desarrollado para sustituir al metodo comprobarModulo
	 * @param $_POST[modulo] nombre del modulo (con prefijo[nombre corto]) que se desea consultar
	 * @param $_POST[accion] nombre de la accion que se desea consultar
	 */
	public function comprobarAccionModulo(){
		if(!$this->seguridad->logged()){
		
			redirect($this->router->routes['default_controller']);
		}
		$p_modulo = $this->input->get('modulo');
		$p_accion = $this->input->get('accion');
		$this->load->library('session');
		$query = $this->seguridad->getModulosUsuario();
		$v_data['tiene_permiso'] = false;
		if($query->num_rows() > 0){
			foreach($query->result() as $ma){
				if($ma->modulo_nombre_corto==$p_modulo && $ma->accion_nombre == $p_accion){
					$v_data['tiene_permiso'] = true;
					//para que sea compatible
					$v_data['permiso_nivel'] = 2;
					$v_data['registro'] = $ma;
					break;
				}
			}
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	
	public function getModulosAccion(){
		if(!$this->seguridad->logged()){		
			redirect($this->router->routes['default_controller']);
		}
		$this->load->library('session');
		$query = $this->seguridad->getModulosUsuario();
		$v_data['success'] = true;
		$v_data['resultado'] = false;
		$v_data['modulos_accion'] = array();
		if($query->num_rows() > 0){
			$v_data['modulos_accion'] = $query->result();
			$v_data['resultado'] = true;
		}
		$this->load->view('output',array('p_output'=>$v_data));
	}
	/**
	 * Realiza un chequeo de las novedades en el sitema
	 *  - Control de sesion
	 *  - Notificaciones
	 *  - Hora del sistema
	 */
	public function comprobarEstado(){
		$v_data= array();
		$cambio = false; //controlar que hay algo nuevo para salir del bloque
		$iteraciones = 0; //que no exceda las 60 veces (aprox. 1 minuto)
		$condicion = false; //simplificacion de la pregunta para no dormir un
							//segundo si no es necesario
                            
                                $p_primera_vez = $this->input->post('primera_vez');
        
		if($this->seguridad->logged()){
			//ciclo de repeticion
			//@todo revisar para optimizar luego
			while(!$condicion){
				$v_data[LOGGED]=true;
				/* 
				 // de momento comentado no se como saber que algo cambio aca
				$r = $this->seguridad->getModulosUsuario();
				if($r->num_rows()>0){			
					$v_data['modulos_permiso'] = $r->result();
				}else{
					$v_data['modulos_permiso'] = array();
				}*/
				
				$n= $this->seguridad->recuperarNotificaciones();
				if($n->num_rows()>0){
					$v_data['notificaciones'] = $n->result();
					$cambio = true;
					$this->seguridad->marcarNotificacionLeida($n);
				}else{
					$v_data['notificaciones'] = array();
				}
                
                $v_data['fechahora'] = $this->seguridad->getFechaHora();
                
				$v_data['fecha'] = explode(" ", $v_data['fechahora']);
				$v_data['hora'] = $v_data['fecha'][1];
                $v_data['fecha'] = $v_data['fecha'][0];

				$iteraciones++;
				/*
				 * Comprueba si hubieron cambios, o si las iteraciones son
				 * mayores a las previstas en un minuto
				 * o si el usuario ya no tiene una sesion valida 
                 * o si es la primera vez que se llama al metodo
				 */
				if($cambio || $iteraciones>= 60 || !$this->seguridad->logged() || $p_primera_vez=='true'){
					$condicion = true;
				}else{
					//dormir un segundo
					sleep(1);
				}
				
			}
		}else{
			$v_data[LOGGED]=false;
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
    
    /**
	 * Revisa la revision SVN del codigo en uso
	 */
	public function comprobarRevisionSVN(){
        if (substr(php_uname(), 0, 7) == 'Windows'){
            $info_svn = shell_exec('version.bat');
        } else {
            $info_svn = shell_exec('./version.sh');
        }
        $v_data['info_svn'] = $info_svn;
        $v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
    }
    /**
     * Funcion copiada del cvpy
     */
    public function registrar(){
    
    	//$v_data = array('datos'=>array());
    	$captcha = $this->nuevoCaptcha();
    	$this->session->set_userdata('t',$captcha['aleatorio']);
    	
    	
    	$this->load->view('solicitud',$captcha);
    }
    
    private function nuevoCaptcha(){
    	$this->load->helper('captcha');
    	$aleatorio = "";
    	$caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$length = 6;
    	$max = strlen($caracteres) - 1;
    	for ($i=0;$i<$length;$i++) {
    		$aleatorio .= substr($caracteres, rand(0, $max), 1);
    	}
    	 //$aleatorio = date('His');
    	$ancho = 200;
    	$alto = 70;
    	$vals = array(
    			'word'	 => $aleatorio,
    			'img_path'	 => './captcha/',
    			'img_url'	 => base_url().'/captcha/',
    			'font_path'	 => './path/to/fonts/texb.ttf',
    			'img_width'	 => $ancho,
    			'img_height' => $alto,
    			'expiration' => 7200
    	);
    	 
    	$cap = create_captcha($vals);
    	$captcha = array(
    			'aleatorio'=>$aleatorio,
    			't'=>$cap['time'],
    			'ancho'=>$ancho,
    			'alto'=>$alto
    	);
    	return $captcha;
    }
    
    public function getCaptcha(){
    	$captcha = $this->input->get('t',true);
    	$archivo = "./captcha/$captcha.jpg";
    	if(file_exists($archivo)){
    		header('Content-type:image/jpg');
    		echo file_get_contents('./captcha/'.$captcha.'.jpg');
    		unlink($archivo);
    	}else{
    		show_404();
    	}
    	
    }
    
    public function crear() {
    	
    	//$this->load->library('email');
    	//$this->email->set_newline("\r\n");
		$data = array (
			//'usuario_id' 		=> $this->input->post ( 'usuario_id', 		true ),
			'usuario_nombre'	=> $this->input->post ( 'usuario_nombre', 	true ),
			'usuario_apellido' 	=> $this->input->post ( 'usuario_apellido', true ),
			'usuario_user' 	=> $this->input->post ( 'usuario_user', 	true ),
			'usuario_pass' 	=> $this->input->post ( 'usuario_pass', 	true ),
			'usuario_email' 	=> $this->input->post ( 'usuario_email', true )
			 
		);
		
		$this->load->helper ( array ( 'form', 'url' ) );
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_error_delimiters ( '<p>', "</p>" );
		
		$this->form_validation->set_rules ( 'usuario_email', '<strong>Email</strong>', 'required|valid_email|is_unique[usuarios.usuario_email]' );
		$this->form_validation->set_rules ( 'usuario_nombre', '<strong>Nombres</strong>', 'required' );
		$this->form_validation->set_rules ( 'usuario_apellido', '<strong>Apellidos</strong>', 'required' );
		$this->form_validation->set_rules ( 'usuario_pass', '<strong>Contrase&ntilde;a</strong>', 'required' );
		$this->form_validation->set_rules ( 'captcha','<strong>Captcha</strong>','required|callback_coincideCaptcha');
		//
				
		if ($this->form_validation->run () !== false ){
			$r = $this->seguridad->crear ( $data );
			if ($r) {
                                                                $mensaje = 'Para activar el acceso de click al sgte enlace '.base_url().'seguridad/aprobar?codigo='.$key;
                                                                $asunto = 'Codigo de Activacion';
                                                                $this->enviar_email($p_data['usuario_email'], $mensaje,$asunto);   
                                                                
				$v_data ['success'] = true;
				$v_data['titulo'] = 'Revise su correo';
				$v_data ['mensaje'] = '<p>Se ha enviado un mensaje de confirmaci&oacute;n al correo prove&iacute;do.';
				$v_data ['mensaje'] .= "<br/> Una vez que confirme los datos podr&aacute; acceder al sistema con los datos ingresados</p>";
				$v_data['mensaje'] .= '<br/><a href="'.base_url('user').'">Ir a pagina inicial</a>';
				//$v_data ['email_debug'] =  $this->email->print_debugger();
				$this->load->view('resultado',$v_data);
			} else {
				$v_data ['success'] = false;
				$v_data ['mensaje'] = 'Error al guardar el registro';
				
				$captcha = $this->nuevoCaptcha();
				$this->session->set_userdata('t',$captcha['aleatorio']);
				$this->load->view ( 'solicitud', array_merge($captcha,$v_data));
			}
		} else {
			$v_data ['mensaje'] = 'No se pudo guardar, revise errores de validaci&oacute;n';
			$v_data ['error_msg'] = validation_errors ();
			$captcha = $this->nuevoCaptcha();
			$this->session->set_userdata('t',$captcha['aleatorio']);
			$this->load->view ( 'solicitud', array_merge($captcha,$v_data));
		}
	}
	
	public function coincideCaptcha($captcha){		
		if($this->session->userdata('t')!=$captcha){
			$this->form_validation->set_message('coincideCaptcha', 'El %s no coincide');
			return false;
		}else{
			return true;
		}
	}
	
	public function aprobar(){
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		$data=array( 'codigo_activacion' => $this->input->get('codigo',true) );
	
		$r = $this->seguridad->aprobar($data);
		if($r===1){
			$v_data['success'] = true;
			//$v_data['mensaje'] = 'Exito al guardar el registro';
			$v_data['titulo'] = 'Cuenta Verificada!';
			$v_data['mensaje'] = "<p>La cuenta ha sido verificada exitosamente, ahora puede ingresar al portal con el email y contrase&ntilde;a que guard&oacute;</p>";
			$v_data['mensaje'] .= '<br/><a href="'.base_url('user').'">Ir a pagina inicial</a>';	
		}elseif($r===2){
			$v_data['success'] = false;
			$v_data['titulo']   = 'Error en la verificaci&oacute;n';
			$v_data['mensaje']  = '<p>La cuenta no pudo ser verificada, esto puede deberse a que ya  han pasado mas de 72 horas desde que se proces&oacute; su registro.</p>';
 			$v_data['mensaje'] .= '<p>Se ha enviado un nuevo c&oacute;digo de verificaci&oacute;n a la cuenta de correo registrada.</p>';
 			$v_data['mensaje'] .= '<br/><a href="'.base_url('user').'">Ir a pagina inicial</a>';
		}else{
			$v_data['success'] = false;
			$v_data['titulo'] = 'Error en la verificaci&oacute;n';
			$v_data['mensaje'] = '<p>El c&oacute;digo de verificaci&oacute;n no es v&aacute;lido</p>';
			$v_data['mensaje'] .= '<br/><a href="'.base_url('user').'">Ir a pagina inicial</a>';
		}
		$this->load->view('resultado',$v_data);
	}	
}
/* End of file seguridad.php */