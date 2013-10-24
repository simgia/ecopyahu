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
			    'oauth_access_token' => "28724574-l1Bx9yBZgAFTgU9SydMtqWBZX1pLkTiAVXkP38xGx",
			    'oauth_access_token_secret' => "0Rh9zpPnq8GSTYA236iB3vxoCRbIXbBDBNsZssDBGNc7v",
			    'consumer_key' => "v7l3Q70QUpTGS4n59HkDg",
			    'consumer_secret' => "jIUNPFebD1daEK0jGHqviCcT4iNRNRDiSPx3A5kI"
			);
                private $tw_url_search = 'https://api.twitter.com/1.1/search/tweets.json';
                private $tw_url_rt = 'https://api.twitter.com/1.1/statuses/retweet/:id.json';
                 private $tw_url_post = 'https://api.twitter.com/1.1/statuses/update.json';
                
                
                private $tw_img_path = 'media/imagen/twitter/';
                private $tw_video_path = 'media/video/twitter/';
   	
                /**
	 * Constructor donde levanta las librerias:
	 */
	public function __construct(){
                    parent::__construct();
                    $this->load->model('redes_sociales_m','redes_sociales');
                    $this->load->model('denuncias_m','denuncias');
                    
                    $this->load->library('TwitterAPIExchange',  $this->tw_settings);
	}
        
                public function index(){
                    
                }
	
	
                public function denunciarByTwitter(){
                    
                    
                    
                    $ultimo_tweet = $this->redes_sociales->get_ultimo_tweet();
                    
                    $this->tw_query.="&since_id=$ultimo_tweet";
                            
                    $tweets = json_decode($this->twitterapiexchange->setGetfield( $this->tw_query )->buildOauth( $this->tw_url_search, 'GET')->performRequest());
                    
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
                                        //elimina los archivos copiados
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
                
                public function retweet($tweet_id){
                    $postfields = array(
                        'id' => $tweet_id
                    );
                    $this->tw_url_rt = str_replace(':id', $tweet_id, $this->tw_url_rt);
                    $json = $this->twitterapiexchange->setPostfields($postfields)->buildOauth($this->tw_url_rt, 'POST')->performRequest();
                    $retweetdata=json_decode($json, true);
                    print_r($retweetdata);
                }
                
                public function twittear($texto){
                     $json = $this->twitterapiexchange->setPostfields(array("status" => $texto ))->buildOauth($this->tw_url_post, 'POST')->performRequest();
                     print_r(json_decode($json, true));
                }
                
               public function menciones(){
                    $tweets = json_decode($this->twitterapiexchange->buildOauth( 'https://api.twitter.com/1.1/statuses/mentions_timeline.json', 'GET')->performRequest());
                   print_r($tweets);
                    
                }
	

} // Fin del controlador denuncias.