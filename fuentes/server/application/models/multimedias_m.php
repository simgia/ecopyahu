<?php
/**
 * @author josego
 * @package ecopyahu
 * @subpackage models
 */
class multimedias_m extends CI_Model{
    /**
     * Constructor de la clase.
     */
    public function __construct(){
	parent::__construct();
    }

    /**
     * Metodo publico que devuelve la cantidad de filas de la ultima consulta.
     * @method get_cantidad_filas
     * @return int
     */
    public function get_cantidad_filas(){
        $v_query = $this->db->query('select found_rows() as cant');
	return $v_query->row()->cant;
    }
    
    /**
     * Metodo publico que guarda un archivo multimedia.
     * @method guardar_multimedia
     * @param Array $p_datos
     * @return type
     */
    public function guardar_multimedia($p_datos){
        return $this->db->insert('multimedias', $p_datos);
    }
    
    /**
     * Metodo publico que devuelve un archivo multimedia.
     * @method get_multimedias
     * @param int $p_denuncia_id
     * @return type
     */
    public function get_multimedias($p_denuncia_id){
        $this->db->where('denuncia_id', $p_denuncia_id);
        return $this->db->get('multimedias');
    }
    
     /**
     * Metodo publico que devuelve el path y el nombre del archivo multimedia para una denuncia por medio del ws
     * @method get_multimedias
     * @param int $p_denuncia_id
     * @return type
     */
    public function get_multimedias_ws($p_denuncia_id){
        $this->db->select('concat("'.base_url().TW_IMG_PATH.'", multimedia_file_name) as multimedia_url',false);
        $this->db->where('denuncia_id', $p_denuncia_id);
        $r = $this->db->get('multimedias');
        if($r->num_rows()>0)
            return $r->result();
        else
            return false;   
    }
} // Fin del model denuncias_m.