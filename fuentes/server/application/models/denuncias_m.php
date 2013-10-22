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
     * Guarda una denuncia. 
     * @param Array $datos
     * @return boolean|int
     */
    public function insertar_denuncia($p_datos){
        if($this->db->insert('denuncias', $p_datos)){
            return $this->db->insert_id();
        }
        return false;
    }
} // Fin del model denuncias_m.