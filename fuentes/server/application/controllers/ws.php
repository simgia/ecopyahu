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
                    $this->load->model('multimedias_m','multimedias');
	}
        
                public function index(){
                    
                }
                
                /*
                 * lista de denuncias
                 * 
                 *  ej: http://ecopyahu/ws/getDenuncias?cant=2&ord=asc&pag=1
                 */
                
                public function getDenuncias(){
                    $cantidad = $this->input->get('cant',true);
                    $orden = $this->input->get('ord',true); //asc or desc por fecha
                    $pagina = $this->input->get('pag',true);
                    
                    
                    if(!$cantidad or $cantidad > 200)
                        $cantidad = 200;
                    if(!$pagina)
                        $pagina = 1;
                    if(!$orden && $orden != 'asc' && $orden != 'desc')
                        $orden = 'desc';
                    
                    
                    $offset = $pagina * $cantidad - $cantidad;
                    
                    $denuncias = $this->denuncias->get_denuncias_ws($cantidad,$offset,$orden)->result();
                    //echo $this->db->last_query();
                    $cantidad_total = $this->denuncias->get_cantidad_resultados();
                    
                    
                    
                    foreach($denuncias as $denuncia){
                        $multimedias = $this->multimedias->get_multimedias_ws($denuncia->denuncia_id);
                        $denuncia->multimedias = $multimedias;
                    }
                    
                    $datos = array('denuncias'=>$denuncias,'cantidad_total'=>$cantidad_total);
                    //print_r($datos);
                    echo json_encode($datos);
                    
                }
                
                /*
                 * ejemplo para consumir datos
                 */
                
                public function consumirWs(){
                    $denuncias = file_get_contents('http://ecopyahu.simgia.com/ws/getDenuncias?cant=2&ord=asc&pag=1');
                    echo "<pre>";
                        print_r(json_decode($denuncias));
                    echo "</pre>";
                }
    
}
?>
