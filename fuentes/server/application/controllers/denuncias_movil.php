<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author josego
 * @package ecopyahu
 *
 */
class denuncias_movil extends SMG_Controller{
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
	//$this->load->library('form_validation');
	$this->load->model('usuarios_m','usuarios');
	$this->load->model('denuncias_m','denuncias');	
    }
	
    /**
     * Metodo que se ejecuta por defecto en el controlador.
     * @return void
     */
    public function index(){
        
    }
    
    /**
     * 
     */
    public function insertar_denuncia(){
        //$this->output->enable_profiler(true);
        /*
        $x = $this->validar_permiso($this->nombre_modulo, self::CREAR, true);
        if(!$x){
            $this->denegar_acceso();
        }*/
        $v_latitud = $this->input->get('latitud', true);
        $v_longitud = $this->input->get('longitud', true);
        $v_descripcion = $this->input->get('descripcion', true);
        $v_subcategoria = $this->input->get('subcategoria', true);
        $v_fuente = $this->input->get('fuente', true);

        $v_data = array();
        $data = array();
        $data['denuncia_lat'] = $v_latitud;
        $data['denuncia_lon'] = $v_longitud;
        $data['denuncia_desc'] = $v_descripcion;
        //$data['denuncia_lon'] = $v_subcategoria;
        $data['denuncia_fuente'] = $v_fuente;

        $v_data['data'] = $data;
        
        $this->denuncias->db->trans_begin();
        
        // Se guarda la denuncia en la base de datos.
	$this->denuncias->insertar_denuncia($data);
        
        // Verifica si todo se guardo correctamente en la base de datos.
	if($this->denuncias->db->trans_status() === true){
            $v_data['resultado'] = true;
            $v_data['mensaje'] = 'Exito al guardar la denuncia';
            $this->denuncias->db->trans_commit();
	}else{
             $v_data['resultado'] = false;
             $v_data['mensaje']  = 'Error al insertar denuncia';
             $this->denuncias->db->trans_rollback();
        }		
        $v_data['success'] = true;
        $this->load->view('output',array('p_output' => $v_data));
    }
} // Fin del controlador denuncias_movil.