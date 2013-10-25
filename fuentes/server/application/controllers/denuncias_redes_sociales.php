<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 *
 * @author jbauer
 * @author valentin
 * @package ecopyahu
 *         
 */
class denuncias_redes_sociales extends CI_Controller {
	private $tw_img_path = TW_IMG_PATH;
	private $tw_video_path = TW_VIDEO_PATH;
	
	/**
	 * Constructor donde levanta las librerias:
	 */
	public function __construct() {
		parent::__construct ();
		$this->load->model ( 'denuncias_m', 'denuncias' );
		$this->load->model ( 'multimedias_m', 'multimedias' );
		
		// $this->load->library('TwitterAPIExchange', $this->tw_settings);
		$this->load->library ( 'twitter' );
	}
	public function index() {
	}
	public function insertarDenunciasByTwitter() {
		if ($_SERVER ['SERVER_ADDR'] == '127.0.0.1') {
			$ultimo_tweet = $this->denuncias->get_ultima_denuncia_ext ( 'twitter' );
			
			// $this->tw_query.="&since_id=$ultimo_tweet";
			
			// $tweets = json_decode($this->twitterapiexchange->setGetfield( $this->tw_query )->buildOauth( $this->tw_url_search, 'GET')->performRequest());
			
			$tweets = $this->twitter->getTweets ( $ultimo_tweet );
			
			// ordenar los mas viejos primero
			$statuses = array_reverse ( $tweets->statuses );
			// print_r($statuses);
			
			foreach ( $statuses as $tweet ) {
				$error = false;
				// inicia transaccion
				$this->db->trans_begin ();
				// inserta la denuncia y recupera el id
				$denuncia_id = $this->denuncias->insertar_denuncia_ext ( $tweet );
				// verifica si tiene medios multimedia
				if (isset ( $tweet->entities->media )) {
					foreach ( $tweet->entities->media as $media ) {
						// print_r($media);
						// recuperar el nombre del archivo
						$file_name = end ( explode ( '/', $media->media_url ) );
						// verificar el tipo para el path y el tipo en la bd
						if ($media->type == 'photo') {
							$path = $this->tw_img_path;
							$tipo = 'img';
						} else {
							$path = $this->tw_video_path;
							$tipo = 'video';
						}
						// copiar el archivo a un medio local
						if (copy ( $media->media_url, $path . $file_name )) {
							// insertar la referencia del multimedia a la denuncia
							$this->multimedias->guardar_multimedia ( array (
									'denuncia_id' => $denuncia_id,
									'multimedia_file_name' => $file_name,
									'multimedia_tipo' => $tipo 
							) );
						} else {
							$error = true;
							// se recupera la lista ya insertada para eliminar el archivo del servidor
							$multimedias = $this->multimedias->get_multimedias ( $denuncia_id );
							foreach ( $multimedias as $multimedia ) {
								if ($multimedia->multimedia_tipo == 'img') {
									$path = $this->tw_img_path;
								} else {
									$path = $this->tw_video_path;
								}
								// elimina los archivos copiados
								unlink ( $path . $multimedia->multimedia_file_name );
							}
							break;
						}
					}
				}
				// si ocurrio un error se para la copia y se guardo el ultimo prosesado correctamente
				if ($this->db->trans_status () === FALSE || $error) {
					$this->db->trans_rollback ();
					break;
				} else {
					$this->db->trans_commit ();
					$this->twitter->retweet ( $tweet->id ); // rt de las denuncias
				}
			}
		}
	}
}
} // Fin del controlador denuncias.