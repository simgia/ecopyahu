<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author jbauer
 * @package ecopyahu
 *
 */
class ws extends CI_Controller{
                /**
	 * Constructor donde levanta las librerias:
	 */
	public function __construct(){
                    parent::__construct();
                    $this->load->model('denuncias_m','denuncias');
	}
        
                public function index(){
                    
                }
                
                public function getDenuncias(){
                    $cantidad = $this->input->get('cant',true);
                    $pagina = $this->input->get('pag',true);
                    $orden = $this->input->get('ord',true); //asc or desc por fecha
                    
                    if(!$cantidad or $cantidad > 200)
                        $cantidad = 200;
                    if(!$pagina)
                        $pagina = 1;
                    if(!$orden && $orden != 'asc' && $orden != 'desc')
                        $orden = 'desc';
                    
                    
                    $offset = $pagina * $cantidad - $cantidad;
                    
                    $denuncias = $this->denuncias->get_denuncias_ws($cantidad,$offset,$orden);
                    
                   /* foreach($denuncias as $denuncia){
                    $multimedias = $this->multimedias->get_multimedias($denuncia->denuncia_id)->result();
                        $denuncia['multimedias'] = $multimedias;
                    }*/
                    
                    echo json_encode($denuncias);
                    
                }
    
}
?>
