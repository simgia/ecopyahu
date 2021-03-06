<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 *
 * @author josego
 * @package ecopyahu
 *         
 */
class denuncias_movil extends SMG_Controller {
	/**
	 *
	 * @var denuncias_m
	 */
	var $denuncias;
        
        private $local_img_path = LOCAL_IMG_PATH;
	
	/**
	 * Constructor donde levanta las librerias:
	 * - form_validation: sirve para validar el formulario de registro de usuario.
	 * - model usuario.
	 */
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'cookie' );
		$this->load->model ( 'usuarios_m', 'usuarios' );
		$this->load->model ( 'denuncias_m', 'denuncias' );
		$this->load->model ( 'multimedias_m', 'multimedias' );
                $this->load->library ( 'twitter' );
	}
	
	/**
	 * Metodo que se ejecuta por defecto en el controlador.
	 * 
	 * @return void
	 */
	public function index() {
	}
	
	/**
	 * Metodo publico que inserta una denuncia a la base de datos.
	 */
	public function insertar_denuncia() {
		// $this->output->enable_profiler(true);
		/*
		 * $x = $this->validar_permiso($this->nombre_modulo, self::CREAR, true); if(!$x){ $this->denegar_acceso(); }
		 */
		$v_latitud = $this->input->get ( 'latitud', true );
		$v_longitud = $this->input->get ( 'longitud', true );
		$v_descripcion = $this->input->get ( 'descripcion', true );
		$v_categoria = $this->input->get ( 'categoria', true );
		$v_fuente = $this->input->get ( 'fuente', true );
		
		$v_data = array ();
		$data = array ();
		$data ['denuncia_lat'] = $v_latitud;
		$data ['denuncia_lon'] = $v_longitud;
		$data ['denuncia_desc'] = $v_descripcion;
		$data ['denuncia_fecha'] = date ( 'Y-m-d H:i:s' );
		$data ['categoria_id'] = $v_categoria;
		$data ['denuncia_fuente'] = $v_fuente;
		
		$this->denuncias->db->trans_begin ();
		
		// Se recupera el id de la denuncia recien creada.
		$v_denuncia_id = $this->denuncias->insertar_denuncia ( $data );
		
		// Verifica si todo se guardo correctamente en la base de datos.
		if ($this->denuncias->db->trans_status () === true) {
			$v_data ['denuncia_id'] = $v_denuncia_id;
			$v_data ['resultado'] = true;
			$v_data ['mensaje'] = 'Se ha enviado correctamente la denuncia';
			$this->denuncias->db->trans_commit ();
		} else {
			$v_data ['resultado'] = false;
			$v_data ['mensaje'] = 'No se pudo enviar la denuncia';
			$this->denuncias->db->trans_rollback ();
		}
		$v_data ['success'] = true;
		
		$v_callback = $_GET ['callback'];
		if (isset ( $v_callback )) {
			header ( 'Content-Type: text/javascript; charset=utf-8' );
			$v_result = $v_callback . '(' . json_encode ( $v_data ) . ');';
		} else {
			// send json encoded response
			header ( 'Content-Type: application/x-json; charset=utf-8' );
			$v_result = json_encode ( $v_data );
		}
		echo $v_result;
	}
	
    /**
     * Metodo publico que devuelve todas las categorias.
     */
    public function getCategorias() {
    	$this->input->get ( 'limit', true );
	$this->input->get ( 'offset', true );
	
	$v_categorias = $this->denuncias->get_categorias ();
	$v_data ['cantidad_total'] = 0;
	$v_data ['resultado'] = false;
		
	if ($v_categorias->num_rows () > 0) {
            $v_data ['cantidad_total'] = $this->denuncias->get_cantidad_resultados ();
            $v_data ['resultado'] = true;
            $v_data ['data'] = $v_categorias->result ();
	}
	$v_data ['success'] = true;
		
	$v_callback = $_GET ['callback'];
	if ($v_callback) {
            header ( 'Content-Type: text/javascript; charset=utf-8' );
	    echo $v_callback . '(' . json_encode ( $v_data ) . ');';
	}else{
	     header ( 'Content-Type: application/x-json' );
	     echo json_encode ( $v_data );
	}
    }
	
    /**
     *
     * @method subirMultimedia
     * Metodo que sube el archivo multimedia. Tambien guarda en la base de
     * datos lo que se subio.
     */
    public function subirMultimedia() {
	$v_data [] = array ();
		
	$config ['upload_path'] = $this->local_img_path;
	$config ['allowed_types'] = 'gif|jpg|png';
	$config ['max_size'] = '1000';
	// $config['max_width'] = '1024';
	// $config['max_height'] = '768';
		
	$this->load->library ( 'upload', $config );
		
	foreach ( $_FILES as $key => $value ) {
            if(! $this->upload->do_upload ( $key )){
                $v_data ['errores'] [] = $this->upload->display_errors ();
            }else{
                 $upload_data = $this->upload->data ();
			
                 // Denuncia id.
                 $v_denuncia_id = $this->input->post ( 'denuncia_id', true );
                 
		 $data = array (
                    'multimedia_file_name' => $upload_data ['file_name'],
                    'multimedia_tipo' => 'img',
                    'denuncia_id' => $v_denuncia_id
		);
		$this->multimedias->guardar_multimedia ( $data );
                $denuncia = $this->denuncias->get_denuncia($this->input->post ( 'denuncia_id', true ) );
                           
                // La ruta completa de la imagen subida.
                $v_imagen_subida = base_url().$this->local_img_path.$upload_data ['file_name'];
                
                // Si la imagen contiene datos GPS en la imagen, entonces va a extraer
                // y actualizar la denuncia.
                $v_exif = exif_read_data($v_imagen_subida);
                $v_longitud = getGps($v_exif["GPSLongitude"], $v_exif['GPSLongitudeRef']);
                $v_latitud = getGps($v_exif["GPSLatitude"], $v_exif['GPSLatitudeRef']);
                
                if($v_longitud != 0 && $v_latitud != 0){
                    $v_data = array(
                        'denuncia_lon' => $v_longitud,
                        'denuncia_lat' => $v_latitud
                    );
                    $this->denuncias->actualizar_denuncia($v_denuncia_id, $v_data);
                }
                
                // Para twittear las denuncia.
                $this->twitter->sendTweetMedia(substr($denuncia->row()->denuncia_desc, 0, 130), $v_imagen_subida);
		$v_data ['exito'] = true;
            }
        }
        echo json_encode ( $v_data );
    }
} // Fin del controlador denuncias_movil.