<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 *
*/
class perfiles extends SMG_Controller{
	/**
	 * Instancia del modelo
	 * @var perfiles_m
	 */
	var $perfiles;
	/**
	 * Constructor de la clase
	 * Controla si esta logueado, sino lo esta, deniega acceso
	 */
	var $nombre_modulo;
	function __construct(){
		parent::__construct();
		$this->load->model('perfiles_m');
		$this->perfiles = $this->perfiles_m;
		$this->nombre_modulo = self::MODULO_PERFILES;
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
	 * Devuelve el listado de perfiles
	 */
	public function listado(){
		$x = $this->validar_permiso($this->nombre_modulo,self::LISTAR,true);
		//var_dump($x);
		if( !$x ){
			$this->denegar_acceso();
		
		}
		$v_data = array('datos'=>array(),'cantidadTotal'=>0);
		$p_limit  = $this->input->get('limit');
		$p_page  = $this->input->get('page');
		$p_sort	 = $this->input->get('sort');
		if($p_sort !=null){
			$p_sort = json_decode($p_sort);
		}
		
		
		$v_offset = $p_page * $p_limit - $p_limit;
		
		$r = $this->perfiles->get_lista_perfiles($p_limit,$v_offset,$p_sort);
		
		if($r->num_rows()>0){
			$v_data['datos'] =$r->result();
			$v_data['cantidadTotal'] = $this->perfiles->get_cantidad_resultados();
		}
		$v_data['resultado'] = true;
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	/**
	 * Devuelve el listado de perfiles del usuario que se le pase por POST
	 * @param int $POST[usuario_id]
	 */
	public function listado_perfiles_user(){
		$x = $this->validar_permiso('principal_usuarios', self::LISTAR,true);		
		//var_dump($x);
		if( !$x ){
			$this->denegar_acceso();
		
		}
		$v_data = array('datos'=>array(),'cantidadTotal'=>0);
		$usuario_id = $this->input->post('usuario_id');
		$v_data['success'] = true;
		$r = $this->perfiles->get_perfiles_usuario($usuario_id);
		if($r->num_rows()>0){
			$v_data['cantidadTotal'] = $r->num_rows();
			$v_data['datos'] = $r->result();
		}
		$this->load->view('output',array('p_output'=>$v_data));
		
	}
	/**
	 * Guarda en la BD un perfil un perfil nuevo o uno existente segun sea
	 * el @param perfil_id
	 * @param (int|null) $_POST[perfil_id
	 * @param string $_POST[perfil_nombre]
	 * @param string $_POST[perfil_estado]
	 */
	public function guardar(){
		$x = $this->validar_permiso($this->nombre_modulo,self::MODIFICAR,true);
		//var_dump($x);
		if( !$x ){
			$this->denegar_acceso();
		
		}
		$perfil_id = $this->input->post('perfil_id');
		$perfil_nombre = $this->input->post('perfil_nombre');
		$perfil_estado = $this->input->post('perfil_estado');
		
		$r = $this->perfiles->guardar($perfil_id, $perfil_nombre,$perfil_estado);
		if($r){
			$v_data['resultado'] = true;
			$v_data['mensaje'] = 'Exito al guardar el registro';
		}else{
			$v_data['resultado'] = false;
			$v_data['mensaje'] = 'Error al guardar el registro';
		}
		$v_data['success'] = true;
		
		$this->load->view('output',array('p_output'=>$v_data));
	}
	/**
	 * Recupera los datos del perfil que se le pase por POST
	 * @param int $_POST[perfil_id]
	 */
	public function datos_perfil(){
		$x = $this->validar_permiso('principal_usuarios', self::LISTAR,true);
		//var_dump($x);
		if( !$x ){
			$this->denegar_acceso();
		
		}
		$perfil_id = $this->input->post('perfil_id',true);
		$r = $this->perfiles->get_perfil($perfil_id);
		if($r->num_rows()>0){
			$v_data['datos'] = $r->row();
			$v_data['cantidadTotal'] = $r->num_rows();
			$v_data['resultado'] = true;
		}else{
			$v_data['resultado'] = false;
			$v_data['cantidadTotal']=0;
			$v_data['datos'] = array();
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
			
	}
	/**
	 * Borra un pefil que se le pase como parametro
	 * @param int $_POST[perfil_id] 
	 */
	public function borrar(){
		$x = $this->validar_permiso($this->nombre_modulo, self::BORRAR,true);
		//var_dump($x);
		if( !$x ){
			$this->denegar_acceso();
		
		}
		$perfil_id = $this->input->post('perfil_id',true);
		$r = $this->perfiles->borrar($perfil_id);
		if($r){			
			$v_data['resultado'] = true;
			$v_data['mensaje'] = 'El perfil ha sido borrado exitosamente.';
		}else{
			$v_data['resultado'] = false;
			$v_data['mensaje'] = 'El perfil no se ha podido borrar.';
			
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	/**
	 * Realiza un cambio de estado del perfil de activo a inactivo y viceversa
	 * segun sea el estado actual
	 * @param int $_POST[perfil_id]
	 */
	public function toggle_estado(){
		
		$x = $this->validar_permiso($this->nombre_modulo,self::MODIFICAR,true);
		//var_dump($x);
		
		if( !$x ){
			$this->denegar_acceso();
		
		}
		$perfil_id = $this->input->post('perfil_id',true);
		$r = $this->perfiles->toggle($perfil_id);
		if($r){
			$v_data['resultado'] = true;
			$v_data['mensaje'] = 'El estado del perfil ha sido cambiado exitosamente.';
		}else{
			$v_data['resultado'] = false;
			$v_data['mensaje'] = 'El estado del perfil no se ha podido cambiar.';
				
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
}