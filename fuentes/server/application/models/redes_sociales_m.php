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
        
                public function get_ultimo_tweet(){
                    $this->db->select('max(denuncia_ext_id) as denuncia_ext_id',false);
                    $this->db->where('denuncia_fuente','twitter');
                    $r = $this->db->get('denuncias');
                    if( $r->row()->denuncia_ext_id!=null)
                        return $r->row()->denuncia_ext_id;
                    else
                        return 0;
                }
	
	
} // Fin del model clientes_m.