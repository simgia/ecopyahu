<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author jbauer
 * @package dmp
 *
 */
class imagenes extends CI_Controller{
	/**
	 * 
	 */
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
	}
	
	public function mostrar_tmp($p_nombre_img=false,$w=false,$h=false){
		if(substr($p_nombre_img,0,32) == $this->session->userdata["session_id"]){
			$config['source_image'] = PATH_IMG_TMP.'/'.$p_nombre_img;
			$config['maintain_ratio'] = TRUE;
			if(!$w===false){
				$config['width'] = $w;
				$config['height'] = $h;
			}
			$config['dynamic_output'] = TRUE;
			
			$this->load->library('image_lib');
			$this->image_lib->initialize($config);
			
			$this->image_lib->resize();
		}
	}
	
	public function mostrar($p_nombre_img,$w=false,$h=false){		
		if(substr($p_nombre_img,0,strlen($this->session->userdata("usuario")->USUARIO_ID)) == $this->session->userdata("usuario")->USUARIO_ID || $this->session->userdata("usuario")->CAT_USUARIO_ID == 3){
			$config['source_image'] = PATH_IMG_ORIGINAL.'/'.$p_nombre_img;
			$config['maintain_ratio'] = TRUE;
			if(!$w===false){
				$config['width'] = $w;
				$config['height'] = $h;
			}
			$config['dynamic_output'] = TRUE;
			$this->load->library('image_lib');
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
		}
	}
}