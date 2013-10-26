<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class twitter{
    private $ci;
    
    private $tw_query = '?q=#ecopyahu+exclude:retweets+-from:ecopyahu';
    private $tw_settings = array(
			    'oauth_access_token' => "2149537735-sQv0z8hdrFlC8zvTz08kfQlOoZBFODtEqYd8Zzq",
			    'oauth_access_token_secret' => "Tb59MQM6HkLWFxfkaxjVEMJhBgOZ8NJ0RqjQEkdPXIAjF",
			    'consumer_key' => "Vkom6MKNvIZgiJTJ1lZvQ",
			    'consumer_secret' => "lSXvqZJo6yCY9XisXJ6aYKNjCJ1GLMrG4TCGksM"
			);
    private $tw_url_search = 'https://api.twitter.com/1.1/search/tweets.json';
    private $tw_url_rt = 'https://api.twitter.com/1.1/statuses/retweet/:id.json';
    private $tw_url_post = 'https://api.twitter.com/1.1/statuses/update.json';
    private $tw_url_post_media =  'https://api.twitter.com/1.1/statuses/update_with_media.json';
   


    
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->library('TwitterAPIExchange',  $this->tw_settings);
        $this->twitter = $this->ci->twitterapiexchange;
    }
    
    /*
     * recuperar tweets a partir del ultimo recuperado en la anterior llamada
     */
    public function getTweets($ultimo_tweet){
         $this->twitter->postfield = null;//parche para no destruir y volver a instanciar la clase
         $this->tw_query.="&since_id=$ultimo_tweet";
          return json_decode($this->twitter->setGetfield( $this->tw_query )->buildOauth( $this->tw_url_search, 'GET')->performRequest());
    }
    
    /*
     * retwitea el id del tweet que se le pasa como parametro
     */
    
    public function retweet($tweet_id){
                    $this->twitter->getfield = null; //parche para no destruir y volver a instanciar la clase
                    $postfields = array(
                        'id' => $tweet_id
                    );
                    $url = str_replace(':id', $tweet_id, $this->tw_url_rt);
                    $json = $this->twitter->setPostfields($postfields)->buildOauth($url, 'POST')->performRequest();
                    $retweetdata=json_decode($json, true);
                    print_r($retweetdata);
                }
     /*
      * 
      * twittea el texto que 
      */           
    public function sendTweet($texto){
          $this->twitter->getfield = null; //parche para no destruir y volver a instanciar la clase
         $json = $this->twitter->setPostfields(array("status" => $texto ))->buildOauth($this->tw_url_post, 'POST')->performRequest();
        // print_r(json_decode($json, true));
    }

      /*
      * 
      * twittea el texto y media
      */           
    public function sendTweetMedia($texto,$image){
          $this->twitter->getfield = null; //parche para no destruir y volver a instanciar la clase
          $json = $this->twitter->setPostfields(array("status" => $texto,"media[]"=>file_get_contents($image) ))->buildOauth($this->tw_url_post_media, 'POST')->performRequest();
           print_r(json_decode($json, true));
    }
    
    
    public function getMenciones(){
        $this->twitter->postfield = null;//parche para no destruir y volver a instanciar la clase
        $tweets = json_decode($this->twitterapiexchange->buildOauth( 'https://api.twitter.com/1.1/statuses/mentions_timeline.json', 'GET')->performRequest());
       //print_r($tweets);
    }
}
?>
