<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 * @subpackage controllers
 * 
 */
class modulos extends SMG_Controller{
	/**
	 * Instancia del modelo
	 * @var modulos_m
	 */
	var $modulos;
	/**
	 * Constructor de la clase
	 */
	var $nombre_modulo;
	function __construct(){
		parent::__construct();
		$this->load->model('modulos_m');
		$this->modulos = $this->modulos_m;
		if(!$this->seguridad->logged()){
			$this->denegar_acceso(true);
		}
		$this->nombre_modulo = self::MODULO_MODULOS;
	}

	/**
	 * Metodo por defecto, redirige a listado
	 */
	public function index(){
		$this->listado();
	}

	/**
	 * @api
	 * Devuelve el listado de perfiles
	 * 
	 */
	public function listado(){
		$x = $this->validar_permiso($this->nombre_modulo,self::LISTAR,true);		
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

		$r = $this->modulos->get_lista_modulos($p_limit,$v_offset,$p_sort);

		if($r->num_rows()>0){
			$v_data['datos'] =$r->result();
			$v_data['cantidadTotal'] = $this->modulos->get_cantidad_resultados();
		}
		$v_data['resultado'] = true;
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	/**
	 * @api
	 * Recupera la lista de modulos que componen un perfil
	 */
	public function listado_modulos_perfil(){
		$x = $this->validar_permiso($this->nombre_modulo,self::LISTAR,true);
		if( !$x ){
			$this->denegar_acceso();
		
		}
				
		$v_data = array('datos'=>array(),'cantidadTotal'=>0);

		$perfil_id = $this->input->get('perfil_id');
		$v_data['success'] = true;
		$r = $this->modulos->get_modulos_perfil($perfil_id);
		if($r->num_rows()>0){
			$v_data['cantidadTotal'] = $r->num_rows();
			$v_data['datos'] = $r->result();
		}
		$this->load->view('output',array('p_output'=>$v_data));
	
	}
	
	
	/**
	 * @api
	 * Guarda un los permisos de un modulo para un determinado perfil
	 * @param array $_POST[modulos]
	 * @param int $_POST[perfil_id]
	 */
	public function guardar(){
		$x = $this->validar_permiso($this->nombre_modulo,self::MODIFICAR,true);
		if( !$x ){
			$this->denegar_acceso();		
		}
		$modulos = $this->input->post('modulos',true);
		$perfil_id = $this->input->post('perfil_id',true);
	
		$r = $this->modulos->set_permisos_modulo($modulos, $perfil_id);
		$v_data = array('mensaje'=>'No se ha podido realizar el cambio en la BD','resultado'=>false);
		if($r){
			$v_data['mensaje']='Se han guardado exitosamente los cambios';
			$v_data['resultado'] = true;
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	
	public function guardarAcciones(){
		$x = $this->validar_permiso($this->nombre_modulo,self::MODIFICAR);
		if( !$x ){
			$this->denegar_acceso();
		}
		$acciones = json_decode($this->input->post('acciones',true));
		$modulo_id = $this->input->post('modulo_id',true);
		$perfil_id = $this->input->post('perfil_id',true);
		$r = $this->modulos->set_permisos_modulo($modulo_id, $perfil_id,$acciones);
		$v_data = array('mensaje'=>'No se ha podido realizar el cambio en la BD','resultado'=>false);
		if($r){
			$v_data['mensaje']='Se han guardado exitosamente los cambios';
			$v_data['resultado'] = true;
		}
		$v_data['success'] = true;
		$this->load->view('output',array('p_output'=>$v_data));
	}
	

	
	public function listadoAcciones(){
		$x = $this->validar_permiso($this->nombre_modulo,self::LISTAR,true);
		if( !$x ){
			$this->denegar_acceso();
		}
		$v_data = array('datos'=>array(),'cantidadTotal'=>0);
		$v_data['success'] = true;
		//recibir modulo y perfil
		$modulo_id = $this->input->get('modulo_id',true);
		$perfil_id = $this->input->get('perfil_id',true);
		$r = $this->modulos->get_acciones_modulos($perfil_id, $modulo_id);
		
		if($r->num_rows()>0){
			$v_data['cantidadTotal'] = $r->num_rows();
			$v_data['datos'] = $r->result();
		}
		$this->load->view('output',array('p_output'=>$v_data));
	}
}