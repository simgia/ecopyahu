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
   	
                /**
	 * Constructor donde levanta las librerias:
	 */
	public function __construct(){
                    parent::__construct();
                    $this->load->model('redes_sociales_m','redes_sociales');
	}
        
                public function index(){
                    
                }
	
	
                public function denunciarByTwitter(){
                    
                    $this->load->library('TwitterAPIExchange',  $this->tw_settings);
                    
                    $ultimo_tweet = $this->redes_sociales->get_ultimo_tweet();
                    
                    echo $this->tw_query.="&since_id=$ultimo_tweet";
                    
                    
                            
                    $tweets = json_decode($this->twitterapiexchange->setGetfield( $this->tw_query )->buildOauth( $this->tw_url, 'GET')->performRequest());
                    
                    print_r($tweets);
                    
                }
	

} // Fin del controlador denuncias.