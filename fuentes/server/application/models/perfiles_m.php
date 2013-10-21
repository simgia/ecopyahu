<?php
/**
 * 
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 * @subpackage models
 *
 */
class perfiles_m extends CI_Model{
	/**
	 * @var CI_DB_active_record
	 */
	//private $db;
	/**
	 * Constructor de la clase.
	 * - Se carga la base de datos.
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Recupera la lista de perfiles del sistema.
	 * @param integer $p_limit
	 * @param integer $p_offset
	 * @param object $v_sort
	 * @return array[object]
	 */
	public function get_lista_perfiles($p_limit=25,$p_offset=0,$v_sort=null){
		
		if($v_sort!=null){
			foreach($v_sort as $sort){
				$this->db->order_by($sort->property,$sort->order);
			}
		}
		$this->db->where('perfil_estado <>','borrado');
		return $this->db->get('perfiles',$p_limit,$p_offset);		
	}
	
	/**
	 * Recupera la cantidad de filas (reales si se uso sql_calc_found_rows) de la ultima consulta que se haya ejecutado
	 * @return integer
	 */
	public function get_cantidad_resultados(){
		return $this->db->query('select FOUND_ROWS() as found_rows')->row()->found_rows;
	}
	
	public function get_perfiles_usuario($usuario_id){
		$this->db->where('usuario_id',$usuario_id);
		return $this->db->get('perfiles_usuarios');
	}
	
	public function guardar($id,$nombre,$estado='activo'){
		if($id==null){
			//insercion
			$data = array('perfil_nombre'=>$nombre,'perfil_estado'=>$estado);
			$r = $this->db->insert('perfiles',$data);
		}else{
			//actualizacion
			$this->db->where('perfil_id',$id);
			$data = array();
			$data['perfil_nombre'] = $nombre;
			$data['perfil_estado'] = $estado;
			$r = $this->db->update('perfiles',$data);
		}
		return $r;	
	}
	
	public function get_perfil($perfil_id){
		 $this->db->where('perfil_id',$perfil_id);
		return $this->db->get('perfiles');
	}
	
	
	public function borrar($perfil_id){
		$this->db->where('perfil_id',$perfil_id);
		return $this->db->update('perfiles',array('perfil_estado'=>'borrado'));
	}
	
	public function toggle($perfil_id){
		$perfil = $this->get_perfil($perfil_id);
		if($perfil->num_rows()>0){
			if($perfil->row()->perfil_estado == 'activo'){
				$nvo_estado = 'inactivo';
			}else{
				$nvo_estado = 'activo';
			}
			$this->db->set('perfil_estado',$nvo_estado);
			$this->db->where('perfil_id',$perfil_id);
			return $this->db->update('perfiles');
		}else{
			return false;
		}
	}
}