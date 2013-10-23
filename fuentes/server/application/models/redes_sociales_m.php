<?php
/**
 * @author juan bauer @bauerpy
 * @package ecopyahu
 * @subpackage models
 */
class redes_sociales_m extends CI_Model{
	
	/**
	 * Constructor de la clase.
	 * - Se carga la base de datos.
	 */
	public function __construct(){
		parent::__construct();
	}
        
                /*
                 * recupera el id del ultimo tweet que se cargo para recuperar solo los posteiores de twitter
                 * 
                 */
                public function get_ultimo_tweet(){
                    $this->db->select('max(denuncia_ext_id) as denuncia_ext_id',false);
                    $this->db->where('denuncia_fuente','twitter');
                    $r = $this->db->get('denuncias');
                    if( $r->row()->denuncia_ext_id!=null)
                        return $r->row()->denuncia_ext_id;
                    else
                        return 0;
                }
                
                /*
                 * insertar tweet a la base de datos
                 */
                
                public function insertar_tweet($tweet){  
                    $lat = null;
                    $lon = null;
                    if(isset($tweet->coordinates)){
                        $lat = $tweet->coordinates->coordinates[0];
                        $lon = $tweet->coordinates->coordinates[1];
                    }
                    $data = array(
                        'denuncia_desc'=>$tweet->text,
                        'denuncia_fecha'=>date( 'Y-m-d H:i:s', strtotime($tweet->created_at) ),
                        'denuncia_lat'=>$lat,
                        'denuncia_lon'=>$lon,
                        'denuncia_fuente'=>'twitter',
                        'denuncia_ext_id'=>$tweet->id,
                        'denuncia_ext_datos'=>  json_encode($tweet)
                    );
                   $this->db->insert('denuncias', $data);
                    return $this->db->insert_id();
                }
                
} // Fin del model clientes_m.