<?php
/**
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 * @subpackage models
 */
class denuncias_m extends CI_Model{
    /**
     * Constructor de la clase.
     * - Se carga la base de datos.
     */
    public function __construct(){
	parent::__construct();
    }

    /**
     * Metodo Guarda una denuncia. 
     * @method get_categorias
     * @param Array $datos
     * @return boolean|int
     */
    public function insertar_denuncia($p_datos){
        if($this->db->insert('denuncias', $p_datos)){
            return $this->db->insert_id();
        }
        return false;
    }
    
    /**
     * Metodo publico que devuelve todas las categorias.
     * @method get_categorias
     * @return 
     */
    public function get_categorias(){
	$this->db->select('SQL_CALC_FOUND_ROWS c.*', false);
	return $this->db->get('categorias c');
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
    *
     */
    public function guardar_multimedia($denuncia_id,$file_name,$tipo, $descripcion){
        $datos = array(
            'denuncia_id'=>$denuncia_id,
            'multimedia_file_name'=>$file_name,
            'multimedia_tipo'=>$tipo,
            'multimedia_desc'=>$descripcion,
        );
        return $this->db->insert('multimedias',$datos);
    }
    
    public  function get_multimedias($denuncia_id){
        $this->db->where('denuncia_id',$denuncia_id);
        return $this->db->get('multimedias');
    }
} // Fin del model denuncias_m.