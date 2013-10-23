<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author jbauer
 * @author valentin
 * @package ecopyahu
 *
 */
class redes_sociales extends CI_Controller{
                
                private $tw_query = '?q=#ecopyahu';
                private $tw_settings = array(
			    'oauth_access_token' => "1029593413-BsySFq1wdMtPC80pfxngUattESM3eBVgp2PUGI4",
			    'oauth_access_token_secret' => "G5obXx1sjywGNXETKIGRnAtSEezT5d7P6YrnujhlBg",
			    'consumer_key' => "JoGeQHHrKFKO60pY2uHA",
			    'consumer_secret' => "S9TRDfjHl0lG136CPgYyL1NCSHlBYt6AmRQrlAnjESo"
			);
                private $tw_url = 'https://api.twitter.com/1.1/search/tweets.json';
                
                
                private $tw_img_path = 'media/imagen/twitter/';
                private $tw_video_path = 'media/video/twitter/';
   	
                /**
	 * Constructor donde levanta las librerias:
	 */
	public function __construct(){
                    parent::__construct();
                    $this->load->model('redes_sociales_m','redes_sociales');
                    $this->load->model('denuncias_m','denuncias');
	}
        
                public function index(){
                    
                }
	
	
                public function denunciarByTwitter(){
                    
                    $this->load->library('TwitterAPIExchange',  $this->tw_settings);
                    
                    $ultimo_tweet = $this->redes_sociales->get_ultimo_tweet();
                    
                    $this->tw_query.="&since_id=$ultimo_tweet";
                            
                    $tweets = json_decode($this->twitterapiexchange->setGetfield( $this->tw_query )->buildOauth( $this->tw_url, 'GET')->performRequest());
                    
                    //ordenar los mas viejos primero
                    $statuses = array_reverse($tweets->statuses);
                    //print_r($statuses);

                    foreach($statuses as $tweet){
                        $error = false;
                        //inicia transaccion
                        $this->db->trans_begin();
                        //inserta la denuncia y recupera el id
                        $denuncia_id = $this->redes_sociales->insertar_tweet($tweet);
                        //verifica si tiene medios multimedia
                        if(isset($tweet->entities->media)){
                            foreach($tweet->entities->media as $media){
                               // print_r($media);
                                //recuperar el nombre del archivo
                                $file_name = end(explode('/',$media->media_url));
                                //verificar el tipo para el path y el tipo en la bd
                                if($media->type == 'photo'){
                                    $path = $this->tw_img_path;
                                    $tipo = 'img';
                                }else{
                                    $path = $this->tw_video_path;
                                    $tipo = 'video';
                                }
                                //copiar el archivo a un medio local
                                if(copy($media->media_url,$path.$file_name)){
                                    //insertar la referencia del multimedia a la denuncia
                                    $this->denuncias->guardar_multimedia($denuncia_id,$file_name,$tipo, '');
                                }else{
                                    $error = true;
                                    //se recupera la lista ya insertada para eliminar el archivo del servidor
                                    $multimedias = $this->denuncias->get_multimedias($denuncia_id);
                                     foreach($multimedias as $multimedia){
                                         if($multimedia->multimedia_tipo == 'img'){
                                            $path = $this->tw_img_path;
                                        }else{
                                            $path = $this->tw_video_path;
                                        }
                                        unlink($path.$multimedia->multimedia_file_name);
                                     }
                                     break;
                                }
                            }
                        }
                        //si ocurrio un error se para la copia y se guardo el ultimo prosesado correctamente
                        if ($this->db->trans_status() === FALSE || $error){
                            $this->db->trans_rollback();
                            break;
                        }else{
                            $this->db->trans_commit();
                        }
                    }
                    
                }
	

} // Fin del controlador denuncias.