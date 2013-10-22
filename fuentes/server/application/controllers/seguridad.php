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
}
/* End of file seguridad.php */