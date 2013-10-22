<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author 
 * @package ecopyahu
 *
 */
class redesSociales extends CI_Controller{
                
                private $tw_query = '#ecopyahu';
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
	}
        
                public function index(){
                    
                }
	
	
                public function denunciasByTwitter(){
                    $this->load->library('',  $this->tw_settings);
                    
                }
	

} // Fin del controlador denuncias.