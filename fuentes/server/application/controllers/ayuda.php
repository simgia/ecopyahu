<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author 
 * @package ecopyahu
 *
 */
class ayuda extends SMG_Controller{
    /**
     * @var denuncias_m
     */
    var $ayuda;
   	
    /**
     * Constructor donde levanta las librerias: 
     */
    public function __construct(){
        parent::__construct();
        // $this->load->model('denuncias_m', 'denuncias');	
        // $this->load->model('multimedias_m', 'multimedias');	
    }
	
    public function index(){
        $this->como();
    }
    
    public function como(){
        $this->load->view('ayuda/como');
    }
}