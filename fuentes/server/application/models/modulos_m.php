<?php
/**
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 * @subpackage models
 */
class modulos_m extends CI_Model{
	
	/**
	 * Constructor de la clase.
	 * - Se carga la base de datos.
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Recupera la lista de modulos del sistema.
	 * @param integer $p_limit
	 * @param integer $p_offset
	 * @param object $v_sort
	 * @return array[object]
	 */
	public function get_lista_modulos($p_limit=25,$p_offset=0,$v_sort=null){
		
		if($v_sort!=null){
			foreach($v_sort as $sort){
				$this->db->order_by($sort->property,$sort->order);
			}
		}
		return $this->db->get('modulos',$p_limit,$p_offset);		
	}
	
	/**
	 * Recupera la cantidad de filas (reales si se uso sql_calc_found_rows) de la ultima consulta que se haya ejecutado
	 * @return int
	 */
	public function get_cantidad_resultados(){
		return $this->db->query('select FOUND_ROWS() as found_rows')->row()->found_rows;
	}
	
	/**
	 * Recupera los permisos para el perfil enviado
	 * @param int $perfil_id
	 * @return CI_DB_mysql_result
	 */
	public function get_modulos_perfil($perfil_id){
		$this->db->select('m.*,p.perfil_id,case when p.perfil_id is null then 0 else 2 end as permiso_nivel',false);
		$this->db->from('modulos m');
		$this->db->join('modulos_acciones ma','ma.modulo_id = m.modulo_id','left');
		$this->db->join('acciones a','a.accion_id = ma.accion_id','left');
		$this->db->join('permisos p','p.accion_id = ma.accion_id and p.modulo_id = ma.modulo_id and p.perfil_id = '.$perfil_id,'left');
		$this->db->group_by('m.modulo_id');
		return $this->db->get();
	}
	/**
	 * 	 
	 */
	public function set_permisos_modulo($modulo_id,$perfil_id,$acciones=array()){
		$this->db->where('perfil_id',$perfil_id);
		$this->db->where('modulo_id',$modulo_id);
		$this->db->delete('permisos');
		if(count($acciones)>0){
			foreach($acciones as $accion){
				$tmp_data = array('perfil_id'=>$perfil_id,'modulo_id'=>$modulo_id,'accion_id'=>$accion);
				$this->db->insert('permisos',$tmp_data);
				
			}
			return true;
			
		}else{
			return true;
		}
	}
	/**
	 * Recupera las acciones que tiene seteado un modulo para un perfil determinado
	 * @param int $perfil_id
	 * @param int $modulo_id
	 * @return  CI_DB_mysql_result
	 */
	public function get_acciones_modulos($perfil_id,$modulo_id){
		$this->db->select(' a.*,p.perfil_id,p.modulo_id, case when perfil_id is null then false else true end as permiso',false);
		$this->db->from('acciones a');
		$this->db->join('modulos_acciones ma',"ma.accion_id = a.accion_id and ma.modulo_id = $modulo_id ",'inner');
		$this->db->join('permisos p',"p.accion_id = ma.accion_id and p.modulo_id  = ma.modulo_id and p.perfil_id =$perfil_id",'left');
		
		return $this->db->get();
	}
} // Fin del model clientes_m.