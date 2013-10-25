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
     * Recupera la lista de denuncias del sistema.
     * @param integer $p_limit
     * @param integer $p_offset
     * @param object $v_sort
     * @return array[object]
     */
    public function get_lista_denuncias($p_limit = 100, $p_offset = 0, $p_sort = null){
        $this->db->select("sql_calc_found_rows d.*", false);
	if($p_sort != null){
            foreach($p_sort as $sort){
                $this->db->order_by($sort->property,$sort->direction);
            }
	}
        //$this->db->order_by("denuncia_id", "asc");
	$this->db->where('denuncia_estado', 'activo');
	//return $this->db->get('denuncias d', $p_limit, $p_offset);
        return $this->db->get('denuncias d');
    } // Fin de la funcion publica get_lista_denuncias.
    
    /**
     * 
     * @param int $p_denuncia
     * @return type
     */
    public function get_denuncia($p_denuncia_id){
        $this->db->select("sql_calc_found_rows d.*", false);
        $this->db->where('denuncia_id', $p_denuncia);
	$this->db->where('denuncia_estado', 'activo');
        return $this->db->get('denuncias d');
    } // Fin de la funcion publica get_denuncia.    
    
    /**
     * lista de denuncias que va ser consumido por el webservice
     */
    public function get_denuncias_ws($cantidad,$offset,$orden){
           $this->db->select('sql_calc_found_rows d.denuncia_id, d.denuncia_desc, d.denuncia_fecha, d.denuncia_lat, d.denuncia_lon, d.denuncia_fuente, d.denuncia_ext_id, c.categoria_nombre',false);
           $this->db->order_by('denuncia_fecha', $orden);
           $this->db->join('categorias c','c.categoria_id = d.categoria_id','left');
           return $this->db->get('denuncias d',$cantidad, $offset);
    }
    
     /*
    * recupera el id del ultimo tweet que se cargo para recuperar solo los posteiores de twitter
    * 
    */
   public function get_ultima_denuncia_ext($fuente){
       $this->db->select('max(denuncia_ext_id) as denuncia_ext_id',false);
       $this->db->where('denuncia_fuente',$fuente);
       $r = $this->db->get('denuncias');
       if( $r->row()->denuncia_ext_id!=null)
           return $r->row()->denuncia_ext_id;
       else
           return 0;
   }
   
   public function insertar_denuncia_ext($tweet){  
                    $lat = null;
                    $lon = null;
                    if(isset($tweet->coordinates)){
                        $lat = $tweet->coordinates->coordinates[1];
                        $lon = $tweet->coordinates->coordinates[0];
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
    
    /**
    * Recupera la cantidad de filas (reales si se uso sql_calc_found_rows) de la ultima consulta que se haya ejecutado
    * @return integer
    */
   public function get_cantidad_resultados(){
           return $this->db->query('select FOUND_ROWS() as found_rows')->row()->found_rows;
   }
} // Fin del model denuncias_m.