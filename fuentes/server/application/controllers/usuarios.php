<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 *
*/
class usuarios extends SMG_Controller{
	/**
	 * Instancia del modelo
	 * @var usuarios_m
	 */
	var $usuarios;
	var $nombre_modulo;
	
	function __construct(){
		parent::__construct();
		$this->load->model('usuarios_m');
		$this->usuarios = $this->usuarios_m;
		$this->load->model('seguridad_m','seguridad');
		$this->nombre_modulo = 'principal_usuarios';
		if(!$this->seguridad->logged()){
			$this->denegar_acceso(true);
		}
	}
	
	/**
	 * Metodo por defecto, redirige a listado
	 */
	public function index(){
		$this->listado();
	}
	
	/**
	 * Devuelve el listado de usuarios
	 */
	public function listado(){
		$x = $this->validar_permiso($this->nombre_modulo, self::LISTAR, true);
		//var_dump($x);
		if(!$x){
			$this->denegar_acceso();
		
		}
		$v_data = array('datos' => array(), 'cantidadTotal' => 0);
		$p_limit  = $this->input->get('limit');
		$p_page  = $this->input->get('page');
		$p_sort	 = $this->input->get('sort');
		if($p_sort != null){
			$p_sort = json_decode($p_sort);
		}
		$v_offset = $p_page * $p_limit - $p_limit;
		$r = $this->usuarios->get_lista_usuarios($p_limit, $v_offset, $p_sort);
		if($r->num_rows() > 0){
			$v_data['datos'] = $r->result();
			$v_data['cantidadTotal'] = $this->usuarios->get_cantidad_resultados();
		}
		$v_data['success'] = true;
		$this->load->view('output', array('p_output' => $v_data));
	}
	
	/**
	 * Metodo para cambiar password del usuario actual
	 */
	public function cambiar_password(){
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p>',"</p>");
		$data=array(
				'password_anterior'		=>  $this->input->post('password_anterior',true),
				'usuario_pass'		=>	$this->input->post('usuario_pass',true),
				'confirma_pass'			=>	$this->input->post('confirma_pass',true)
				);
		$this->form_validation->set_rules('usuario_pass', 'Clave', 'required|min_length[6]');
		$this->form_validation->set_rules('confirma_pass', 'Confirme clave', 'required|matches[usuario_pass]|min_length[6]');
		
		if($this->form_validation->run()!==false){
			//guardar
			$usuario = $this->seguridad->getDatosUsuario();
			$resultado = $this->usuarios->cambiar_password($data['password_anterior'],$data['usuario_pass'],$usuario->usuario_id);
			$v_data['resultado'] = false;
			$v_data['mensaje'] = 'Se produjo un error al guardar,revisar la contrase&ntilde;a anterior.';
				
			if($resultado){
				$v_data['resultado'] = true;
				$v_data['mensaje'] = 'Se guardo con exito en la base de datos.';
			}
		}else{
			//error
			$v_data['mensaje'] = 'No se pudo guardar, revise errores de validaci&oacute;n';
			$v_data['error_msg']=validation_errors();
			foreach($data as $key=>$value){
				$v_data['errors'][$key] = form_error($key);
			}
		}
		$v_data['success'] = true;
		//enviando datos para jsonificar
		$this->load->view('output',array('p_output'=>$v_data));
	}

	public function guardar(){
		//$x = $this->validar_permiso('principal');
		$x = $this->validar_permiso($this->nombre_modulo, self::CREAR,true) || $this->validar_permiso($this->nombre_modulo,self::MODIFICAR, true);
		//var_dump($x);
		if( !$x ){
			$this->denegar_acceso();
		
		}
		//		$this->output->enable_profiler(false);
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p>',"</p>");
		
		$data = array(
			'usuario_id'		=>	$this->input->post('usuario_id',true),
			'usuario_nombre'	=>	$this->input->post('usuario_nombre',true),
			'usuario_apellido'	=>	$this->input->post('usuario_apellido',true),
			'usuario_estado'	=>	$this->input->post('usuario_estado',true),
			'usuario_user'		=>	$this->input->post('usuario_user',true),
			'usuario_pass'		=>	$this->input->post('usuario_pass',true),
			'confirma_pass'		=>	$this->input->post('confirma_pass',true),
			'usuario_email'		=>	$this->input->post('usuario_email',true),
			'perfiles'			=>	$this->input->post('perfiles',true)
		);	
		$this->form_validation->set_rules('usuario_nombre', 'Nombre', 'required');
		$this->form_validation->set_rules('usuario_apellido', 'Apellido', 'required');
		$this->form_validation->set_rules('usuario_estado', 'Estado', 'required');

		

		if($data['usuario_id'] == null){
			//Estas reglas solo se aplican para nuevos registros
			$this->form_validation->set_rules('usuario_user', 'Usuario', 'required|is_unique[usuarios.usuario_user]');
			$this->form_validation->set_rules('usuario_pass', 'Clave', 'required|min_length[6]');
			$this->form_validation->set_rules('confirma_pass', 'Confirme clave', 'required|matches[usuario_pass]|min_length[6]');
			$this->form_validation->set_rules('usuario_email', 'Email', 'required|valid_email|is_unique[usuarios.usuario_email]');
				
		}else{
			$tmp_usuario = $this->usuarios->get_usuario($data['usuario_id']);
			if($tmp_usuario !=null){
				//verificacion de usurpaciones de usuarios y emails
				if(strtolower($tmp_usuario->usuario_user) != strtolower($data['usuario_user'])){
					$this->form_validation->set_rules('usuario_user', 'Usuario', 'required|is_unique[usuarios.usuario_user]');
				}
				
				if(strtolower($tmp_usuario->usuario_email) != strtolower($data['usuario_email'])){
					$this->form_validation->set_rules('usuario_email', 'Email', 'required|valid_email|is_unique[usuarios.usuario_email]');
				}else{
					$this->form_validation->set_rules('usuario_email', 'Email', 'required|valid_email');
				}
			}
		}
		if($data['usuario_id'] !=null && ($data['usuario_pass']!='' || $data['confirma_pass'])){	
			//y estas reglas se aplican a ediciones de registros para el caso de los passwords
			$this->form_validation->set_rules('usuario_pass', 'Clave', 'required|min_length[6]');
			$this->form_validation->set_rules('confirma_pass', 'Confirme clave', 'required|matches[usuario_pass]|min_length[6]');
			
		}
		if($this->form_validation->run() !== false){
			$resultado = $this->usuarios->guardar_usuario($data);
			//datos por defecto, si $resultado resulta ser falso
			$v_data['resultado'] = false;
			$v_data['mensaje'] = 'Se produjo un error al guardar los datos en la Base de Datos.';
			
			if($resultado){
				$v_data['resultado'] = true;
				$v_data['mensaje'] = 'Se guardo con exito en la base de datos.';
			}
		}else{
			$v_data['mensaje'] = 'No se pudo guardar, revise errores de validaci&oacute;n';
			$v_data['error_msg']=validation_errors();
			foreach($data as $key=>$value){
				$v_data['errors'][$key] = form_error($key);
			}
		}
		$v_data['success'] = true;
		//enviando datos para jsonificar 
		$this->load->view('output', array('p_output' => $v_data));
	}
	
	public function getProyectosUsuario(){
		if(/* $this->validar_permiso(array('Informes'=>2),true) && */ $this->input->get('usuario_id') != null){
			$v_usuario_id = $this->input->get('usuario_id');
		}else{
			$v_usuario_id = $this->seguridad->getDatosUsuario()->usuario_id;
		}
		$r = $this->usuarios->get_proyectos_usuario($v_usuario_id);
		$v_data['datos'] = array();
		$v_data['resultado'] = false;
		if($r->num_rows() > 0){
			$v_data['datos'] = $r->result();
			$v_data['cantidadTotal'] = $r->num_rows();
			$v_data['resultado']  = true;
		}
		$v_data['success'] = true;
		//enviando datos para jsonificar
		$this->load->view('output', array('p_output' => $v_data));
	}
	
	public function borrar(){
		$x = $this->validar_permiso($this->nombre_modulo, self::BORRAR, true);
		//var_dump($x);
		if(!$x){
			$this->denegar_acceso();
		
		}
		$usuario_id = $this->input->post('usuario_id');
		$v_data['mensaje'] = 'Error al borrar: ';
		$v_data['success'] = true;
		$r = $this->usuarios->borrar_usuario($usuario_id);
		if($r){
			$v_data['resultado'] = true;
			$v_data['mensaje'] = 'Usuario borrado exitosamente';
			$v_data['success'] = true;
		}else{
			$v_data['resultado'] = false;
			$v_data['mensaje'] .= $this->usuarios->get_error();
		}
		$this->load->view('output', array('p_output' => $v_data));
	}

	/**
	 * Comprueba que el email existe para un usuario nuevo
	 * @param string $usuario_email
	 * @param Integer $usuario_id [default null]
	 * @deprecated
	 * @return boolean
	 */
	private function _email_existe($usuario_email, $usuario_id = null){
		//temporalmente desplazados por la regla is_unique[tabla.columna]
		$query = $this->usuarios->comprobar_usuario('usuario_email', $usuario_email);
		if($query->num_rows() > 0 && $usuario_id == null){
			return true;
		}else{
			 return false;
		}
	}
}