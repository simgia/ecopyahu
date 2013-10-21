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
		$this->load->view('portada');
	}	
}	
	
