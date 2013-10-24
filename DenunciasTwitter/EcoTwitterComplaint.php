<?php

	class TwitterComplaint {
		private $tweetsDecode;
		private $settings;
		private $tweets;
		private $getField;
		private $url;
		private $requestMethod;
		private $tweetsClean;

		public function __construct( $categoryTag ) {
			$this->settings = array(
			    'oauth_access_token' => "1029593413-BsySFq1wdMtPC80pfxngUattESM3eBVgp2PUGI4",
			    'oauth_access_token_secret' => "G5obXx1sjywGNXETKIGRnAtSEezT5d7P6YrnujhlBg",
			    'consumer_key' => "JoGeQHHrKFKO60pY2uHA",
			    'consumer_secret' => "S9TRDfjHl0lG136CPgYyL1NCSHlBYt6AmRQrlAnjESo"
			);

			//Recurso del API que queremos consultar
			$this->url = 'https://api.twitter.com/1.1/search/tweets.json';
			$this->getField = '?q=#ecopyahu+%23#'.$categoryTag;
			$this->requestMethod = 'GET';

			$this->tweets = new TwitterAPIExchange( $this->settings );

		}//end __construct

		public function getData() {
			$this->tweetsDecode = json_decode( $this->tweets->setGetfield( $this->getField )
							        ->buildOauth( $this->url, $this->requestMethod )
							        ->performRequest() );
                                                print_r($this->tweetsDecode);                    
			//Obtiene solo los statuses
			$this->tweets = $this->tweetsDecode->statuses;
			
			for( $i = 0; $i < sizeof( $this->tweets ); $i++ ) {
				$tweet = $this->tweets[ $i ];

				$this->tweetsClean[ $i ][ 'id' ] = $tweet->id;
				$this->tweetsClean[ $i ][ 'description' ] = $this->getDescription( $tweet );
				$this->tweetsClean[ $i ][ 'media_url' ] = $this->getMedia( $tweet );
				$this->tweetsClean[ $i ][ 'date' ] = $this->getDate( $tweet );

			}//end for

			return $this->tweetsClean;						
			
		}//end getData


		//****************************************************************************

		private function getDate( &$tweet ) {
			$fecha = $tweet->created_at;
			$fechaFormateada = new FormatFecha( $fecha );

			return $fechaFormateada->getFechaFormated();

		}//end getFecha




		//****************************************************************************



		private function getDescription( &$tweet ) {
			//Obtiene el array de los objetos que poseen los hashtags
			$hashtags = $tweet->entities->hashtags;

			//Obtiene solo el texto de los hastag
			foreach ( $hashtags as $hashtag ) {
				$hashtagsClean[] = $hashtag->text;
			}//end foreach

			//Borra todos los hashtags
			foreach ( $hashtagsClean as $hashtag ) {
				$tweet->text = str_replace( "#".$hashtag, '', $tweet->text );
			}

			//Si hay media borra el link para dejar limpia la descripcion
			if( isset( $tweet->entities->media ) ) {
				$link = $tweet->entities->media[ 0 ]->url;
				$tweet->text = str_replace( $link, '', $tweet->text );

			}//end if

			//Modifica dos espacios continuos por si los hashtag se insertaron en el medio
			$tweet->text = str_replace( '  ', ' ', $tweet->text );

			return trim( $tweet->text );

		}//end getDescription



		//*******************************************************************


		private function getMedia( &$tweet) {
			if( isset( $tweet->entities->media ) ) {
				$media = $tweet->entities->media[ 0 ]->media_url;

			} else {
				$media = null;

			}//end if

			return $media;

		}//end getMedia


	}//end TwitterComplaint

?>