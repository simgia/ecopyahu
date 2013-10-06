<?php
if( !defined('BASEPATH')) exit('No se permite el acceso directo a este archivo');
/**
 * Clase inicial, punto de entrada al sistema
 * @author pablo
 *
 */
class inicio extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		echo "Hola, aca empezamos";
	}
	
	public function test(){
		$this->output->enable_profiler(true);
	}
}