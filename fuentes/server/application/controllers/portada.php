<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author juan bauer @bauerpy
 * @package ecopyahu
 * @subpackage controllers
 */
class portada extends SMG_Controller{
    /**
     * Constructor 
     */
    public function __construct(){
    	parent::__construct();
    }
	
    /**
     * Metodo para mostrar la portada inicial
     * @return void
     */
    public function index(){
    	//$this->load->view('portada');
        $this->load->view('denuncias/lista_mapa');
    }
        
    /**
     * Metodo para mostrar la portada inicial
     * @return void
     */
    public function denunciasMapa(){
        $this->load->view('denuncias/lista_mapa');
    }
            
    /**
     * 
     */
    public function movil(){
        $this->load->view('movil');
    }       
}