<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Pablo Ruiz Diaz
 * @package ecopyahu
 * @subpackage models
 *
 */
class usuarios_m extends CI_Model{
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
	 * Recupera la lista de usuarios del sistema.
	 * @param integer $p_limit
	 * @param integer $p_offset
	 * @param object $v_sort
	 * @return array[object]
	 */
	public function get_lista_usuarios($p_limit=25,$p_offset=0,$v_sort=null){
		$this->db->select("sql_calc_found_rows usuario_id, usuario_nombre, usuario_apellido, usuario_user, usuario_email, usuario_estado", false);
		if($v_sort != null){
			foreach($v_sort as $sort){
				$this->db->order_by($sort->property,$sort->direction);
			}
		}
		$this->db->where('usuario_estado <>', 'borrado');
		return $this->db->get('usuarios u', $p_limit, $p_offset);
	} // Fin de la funcion publica get_lista_usuarios.
	
	/**
	 * Recupera registro del usuario, si no encuentra el registro retorna NULL
	 * @param integer $usuario_id
	 * @return object row
	 */
	public function get_usuario($usuario_id){
		$this->db->where('usuario_estado <>','borrado');
		$this->db->where('usuario_id',$usuario_id);
		$query = $this->db->get('usuarios');
		
		if($query->num_rows() > 0){
			return $query->row();
		}
		return null;		
	}
	
	/**
	 * Recupera la cantidad de filas (reales si se uso sql_calc_found_rows) de la ultima consulta que se haya ejecutado
	 * @return integer
	 */
	public function get_cantidad_resultados(){
		return $this->db->query('select FOUND_ROWS() as found_rows')->row()->found_rows;
	}
	
	/**
	 * Guarda un usuario.
	 * @param array $p_data
	 * @return boolean
	 */
	public function guardar_usuario($p_data){
		$r = false;
		if(isset($p_data['confirma_pass'])){
			unset($p_data['confirma_pass']);
		}
		$perfiles = $p_data['perfiles'];
		unset($p_data['perfiles']);
		if(!empty($p_data['usuario_pass'])){
			$p_data['usuario_pass'] = md5($p_data['usuario_pass']);
		}elseif($p_data['usuario_id'] != null){
			//solo en caso de que sea una actualizacion
			unset($p_data['usuario_pass']);
		}
		if($p_data['usuario_id']==null){
			//insert
			unset($p_data['usuario_id']);
			$r = $this->db->insert('usuarios',$p_data);
			$usuario_id = $this->db->insert_id();
			
		}else{
			//update
			$this->db->where('usuario_id',$p_data['usuario_id']);
			$usuario_id = $p_data['usuario_id'];
			unset($p_data['usuario_id']);
			
			$r = $this->db->update('usuarios',$p_data);
			
			//borramos los perfiles que tenga asignado
			$this->db->where('usuario_id',$usuario_id);
			$this->db->delete('perfiles_usuarios');			
		}
		
		if(is_array($perfiles) && count($perfiles)>0){
			foreach($perfiles as $perfil){
				$this->db->insert('perfiles_usuarios',array('perfil_id'=>$perfil,'usuario_id'=>$usuario_id));
			}
		
		}
		
		return $r;
		
	} // Fin de la funcion publica guardar_usuario.
	
	public function borrar_usuario($usuario_id){
		$this->db->where('usuario_id',$usuario_id);
		$this->db->set('usuario_estado','borrado');
		return $this->db->update('usuarios');
		/* Comento esto, porque el estado nomas tiene que cambiar a borrado
		 * $this->db->where('usuario_id',$usuario_id);
		return $this->db->delete('usuarios');*/
	}
	
	public function get_error(){
		return $this->db->_error_message();
	}
	
	public function comprobar_usuario($columna,$valor){
		$this->db->where($columna,$valor);
		return $this->db->get('usuarios');
	}
	
	public function get_proyectos_usuario($usuario_id){
		$this->db->join('proyectos p','p.proyecto_id = pf.proyecto_id');
		$this->db->where('pf.usuario_id',$usuario_id);
		$this->db->where('proyecto_usuario_estado <>','borrado');
		return $this->db->get('proyectos_usuarios pf');
	}
	
		
	/**
	 * Funcion que cambia el password de un usuario, solo si su estado es activo.
	 * @param string $password_anterior
	 * @param string $password_nuevo
	 * @return boolean
	 */
	public function cambiar_password($password_anterior,$password_nuevo,$usuario_id){
		$this->db->where('usuario_id',$usuario_id);
		$this->db->where('usuario_pass',md5($password_anterior));
		$this->db->set('usuario_pass',md5($password_nuevo));
		$resultado =  $this->db->update('usuarios');
		return $resultado && $this->db->affected_rows() > 0;
		
	} // Fin de la funcion publica cambiar_password.
	

} // Fin del model usuarios_m.