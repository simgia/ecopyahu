<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author pablo ruiz diaz
 * @author juan bauer
 * @package ecopyahu
 * @subpackage models
 */
class Seguridad_M extends CI_Model {
	/**
	 * Modelo para manejo de seguridad.
	 * @author Juan Bauer
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Metodo para iniciar sesion del usuario.
	 * @param string nombre de usuario
	 * @param string password de acceso
	 * @return bool
	 */
	public function login($p_user,$p_password){		
		$this->db->select('usuario_user, usuario_id, usuario_nombre, usuario_apellido, usuario_email');
		$this->db->where('usuario_user', $p_user);
		$this->db->where('usuario_pass', $p_password);
		$this->db->where('usuario_estado', E_ACTIVO);
		
		$v_consulta = $this->db->get('usuarios');
		
		if($v_consulta->num_rows()==1){ // Existe
			$v_usuario = $v_consulta->row();
			$this->session->set_userdata(LOGGED,true);
			$this->session->set_userdata('usuario', $v_usuario);
			$this->session->set_userdata(SESSION_LIVE,time());
			//recuperamos toda la informacion de sesion actual del usuario (datos de navegador y origen mas que nada)
			$all_userdata = $this->session->all_userdata();
			//actualizamos a inactivo  toda sesion del usuario que este abierta
			$this->db->where('usuario_id',$v_usuario->usuario_id);
			$this->db->where('session_id <>',$all_userdata['session_id']);
			$this->db->update('sesiones',array('estado'=>'inactivo'));
			
			$set  = array('session_id'=>$all_userdata['session_id'],
					'dir_ip'=>$all_userdata['ip_address'],
					'user_agent'=>$all_userdata['user_agent'],
					'usuario_id'=>$v_usuario->usuario_id,
					'estado'=>'activo',
					'ingreso'=>time()
			);
			//y guardamos ese dato dentro la tabla de sesiones del sistema
			$this->db->insert('sesiones',$set);
			
			return true;
		}else{
			return false;
		}
	}// Fin del metodo login.
	
	/**
	 * Metodo para cerrar sesion del usuario.
	 * @return void
	 */
	public function logout(){
		$all_userdata = $this->session->all_userdata();
		//se actualiza a inactivo la sesion
		$this->db->where('session_id',$all_userdata['session_id']);
		$this->db->set('estado','inactivo');
		$this->db->update('sesiones');
		//se destruye todo dato de sesion y se reemplaza el objeto que se guarda en sesion
		$this->session->unset_userdata(LOGGED,false);
		$this->session->set_userdata('usuario','lala');
		$this->session->sess_destroy();		
		
	}// Fin del metodo logout.
	

	/**
	 * Metodo para mantener con vida la sesion.
	 * @return void
	 */
	public function keepAlive(){
		//log_message(DEBUG_LEVEL_INFO,' * haciendo keep alive');
		$time = time();
		$this->session->set_userdata(SESSION_LIVE,$time);
		$this->session->set_userdata('last_activity',$time);
	}// Fin del metodo keepAlive.
	
	/**
	 * Retorna los datos del usuario necesarios para el extjs.
	 * @param string $p_user_usuario
	 * @return CI_DB_mysql_result
	 */
	public function getDatosusuario(){
		return $this->session->userdata('usuario');
	}// Fin del metodo getDatosusuario.
	
	public function esAdmin(){
				$modulos = $this->getPerfilesUsuario();
			
			$esAdmin=false;
			//busqueda de perfil Administrador
			foreach($modulos->result() as $modulo){
				if($modulo->perfil_nombre=='Administrador'){
					$esAdmin=true;
					break;					
				}
			}
			
        return $esAdmin;
	}
	
	/**
	 * Comprueba que el usuario este logueado y su tiempo de session no ha expirado aun.
	 * @author Pablo
	 * @return boolean
	 */
	public function logged(){
		$session_live =  $this->session->userdata(SESSION_LIVE);
		// Comprobamos que esta seteado el valor en session.
		if($session_live !== FALSE){
			// Verificar que la ultima actividad con el sistema aun no haya caducado.
			if($this->config->item(SESSION_LIVE)< (time() - $session_live) || $this->is_killed_session() ){
				$this->seguridad->logout();
#				$v_sesion_activa = false;
				return false;
			}else{
				return true;
			}
		}else{
			return false;	
		}
	}// Fin del metodo logged.
	
	/**
	 * Comprueba que la sesion no ha sido finalizada por otros metodos que no
	 * sean su expiracion normal. Por ej, que el administrador haya matado su
	 * sesion o que haya iniciado sesion en otra ubicacion
	 * @return boolean
	 */
	public function is_killed_session(){
		$all_userdata = $this->session->all_userdata();
		
		// si algunas de estas variables no se encuentran se considera muerta
		if(!isset($all_userdata['session_id'],$all_userdata['ip_address'],$all_userdata['user_agent'],$all_userdata['last_activity'])){
			return true;
		}
		$session_usuario = $this->session->userdata('usuario');
		if($session_usuario === false){
			return true;
		}
		$this->db->where('session_id',$all_userdata['session_id']);
		$this->db->where('dir_ip',$all_userdata['ip_address']);
		$this->db->where('user_agent',$all_userdata['user_agent']);
		$this->db->where('usuario_id',$session_usuario->usuario_id );
		
		$query = $this->db->get('sesiones');
		//si en nuestra tabla de sesiones no se encuentra entonces esta muerta
		if($query->num_rows() <= 0){
			return true;
		}else{
			$registro = $query->row();
			//si se encuentra pero la sesion esta inactiva esta muerta
			if($registro->estado == 'inactivo'){
				return true;
			}else{
				//solo si todo lo demas se incumplio se puede deducir que esta
				//aun activo
				return false;
			}
		}
	}

	/**
	 * Recupera la lista de modulos disponibles del usuario
	 * @param $usuario_id [opcional] Si no se especifica revisa el valor de sesion
	 * @param $estado [default 'activo] si se setea a false incluye todos los estados
	 * @author Pablo
	 * @return CI_DB_mysql_result
	 */
	public function getModulosUsuario($usuario_id=null,$estado='activo'){
	
		if($usuario_id == null){
			$usuario_id = $this->session->userdata('usuario')->usuario_id;
		}
		$this->db->select('m.modulo_id,m.modulo_descripcion,m.modulo_controlador,m.modulo_nombre_corto,m.modulo_icono,m.modulo_principal,a.accion_nombre');
		$this->db->from('modulos m');
		$this->db->join('modulos_acciones ma','ma.modulo_id = m.modulo_id');
		$this->db->join('acciones a','a.accion_id = ma.accion_id');
		$this->db->join('permisos p','ma.modulo_id = p.modulo_id and ma.accion_id = p.accion_id');
		$this->db->join('perfiles pf','pf.perfil_id = p.perfil_id');
		$this->db->join('perfiles_usuarios pu','pf.perfil_id = pu.perfil_id');
		$this->db->where('pu.usuario_id',$usuario_id);
		if($estado !== false){
			$this->db->where('pf.perfil_estado',$estado);
		}
		$this->db->group_by('m.modulo_id');
		return $this->db->get();
		
		
		
	}
	
	/*public function getModulosAccion($usuario_id=null,$estado='activo'){
		if($usuario_id == null){
			$usuario_id = $this->session->userdata('usuario')->usuario_id;
		}
		$this->db->select('m.modulo_nombre_corto,a.accion_nombre');
		$this->db->from('modulos m');
		$this->db->join('modulos_acciones ma','ma.modulo_id = m.modulo_id');
		$this->db->join('acciones a','a.accion_id = ma.accion_id');
		$this->db->join('permisos p','ma.modulo_id = p.modulo_id and ma.accion_id = p.accion_id');
		$this->db->join('perfiles pf','pf.perfil_id = p.perfil_id');
		$this->db->join('perfiles_usuarios pu','pf.perfil_id = pu.perfil_id');
		$this->db->where('pu.usuario_id',$usuario_id);
		if($estado !== false){
			$this->db->where('pf.perfil_estado',$estado);
		}
		
		return $this->db->get();
	}*/
	
	/**
	 * Recupera la lista de Perfiles del usuario
	 * @param $usuario_id [opcional] Si no se especifica revisa el valor de sesion
	 * @author Pablo
	 * @return CI_DB_mysql_result
	 */
	public function getPerfilesUsuario($usuario_id=null){
		if($usuario_id == null){
			$usuario_id = $this->session->userdata('usuario')->usuario_id;
		}
		$this->db->select('perfil_nombre,pf.perfil_id,pf.usuario_id');
		$this->db->join('perfiles_usuarios pf','pf.perfil_id = p.perfil_id');
		$this->db->where('pf.usuario_id',$usuario_id);
		$this->db->from('perfiles p');
		return $this->db->get();
	}

	/**
	 * verifica si el usuario tiene el perfil solicitado
	 * @param $usuario_id 
	 * @param $perfil_id 
	 * @author jbauer
	 * @return boolean
	 */
	public function verificarPerfilUsuario($usuario_id,$perfil_id){
		$this->db->join('perfiles_usuarios pf','pf.perfil_id = p.perfil_id and p.perfil_id');
		$this->db->where('pf.usuario_id',$usuario_id);
		$this->db->where('p.perfil_nombre',$perfil_id);
		$this->db->from('perfiles p');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * crea una notificacion
	 * @param (CI_DB_mysql_result|Array) $query
	 * @return int/bool
	 */
	public function crearNotificacion($id_usuario_remitente, $id_usuario_destinatario, $mensaje, $fecha_hora, $evento = null){
		$v_data['notificacion_usuario_id_remitente']  = $id_usuario_remitente;
		$v_data['notificacion_usuario_id_destinatario']  = $id_usuario_destinatario;
		$v_data['notificacion_mensaje']  = $mensaje;
		$v_data['notificacion_fecha_hora_envio']  = $fecha_hora;
		$v_data['notificacion_estado']  = 'nuevo';
                                $v_data['notificacion_evento']  = $evento;
		
		$v_result = $this->db->insert('notificaciones', $v_data);
		
		if($v_result){
			return $this->db->insert_id();
		}
		return false;
	}	
	
	/**
	 * Consulta a la tabla notificaciones
	 * @param int $usuario_id
	 * @param (string|array) $estado
	 * @return CI_DB_mysql_result
	 */
	public function recuperarNotificaciones($usuario_id=null,$estado='nuevo'){
		$columnas = 'r.usuario_nombre rem_usuario_nombre, '.
					'r.usuario_apellido as rem_usuario_apellido, '.
					'r.usuario_user as rem_user, '.
					'd.usuario_nombre des_usuario_nombre, '.
					'd.usuario_apellido as des_usuario_apellido, '.
					'd.usuario_user as des_user, n.*'
					;
		
		$this->db->select($columnas,false);
		if($usuario_id == null){
			$usuario_id = $this->session->userdata('usuario')->usuario_id;
		}
		
		if(is_array($estado)){
			$this->db->where_in('notificacion_estado',$estado);
		}else{
			$this->db->where('notificacion_estado',$estado);
		}
		$this->db->join('usuarios r','r.usuario_id=n.notificacion_usuario_id_remitente');
		$this->db->join('usuarios d','d.usuario_id=n.notificacion_usuario_id_destinatario');
		$this->db->where('notificacion_usuario_id_destinatario',$usuario_id);
		return $this->db->get('notificaciones n');
	}
	
	/**
	 * Marca como leida las notificaciones que reciba
	 * @param (CI_DB_mysql_result|Array) $query
	 * @return bool
	 */
	public function marcarNotificacionLeida($query){
		if(is_object($query)){
		if($query->num_rows()>0){
			foreach($query->result() as $registro){
				$this->db->where('notificacion_id',$registro->notificacion_id);
				$this->db->update('notificaciones',array('notificacion_estado'=>'leido'));
			}
		}
		
		}elseif(is_array($query)){
			foreach($query as $id){
				$this->db->where('notificacion_id',$id);
				$this->db->update('notificaciones',array('notificacion_estado'=>'leido'));
			}
			
		}
		return true;
	}
    /**
	 * Recupera los datos de fecha/hora del sistema 
	 * @return string
	 */
    public function getFechaHora(){
        $query = $this->db->query("SELECT date_format(now(),'%d-%m-%Y %H:%i') as fechahora");
        $row = $query->row();
        return $row->fechahora;;
    }
    
    public function crear($p_data){
    	$r = false;
    		
    	$key = $this->getNuevoCodigoActivacion();
    
    	
    
    	$p_data['usuario_pass'] =md5($p_data['usuario_pass']);
    	$p_data['usuario_estado'] ='inactivo';
    	$p_data['usuario_cod_act'] =$key;
    	$p_data['usuario_fecha_reg']   = date('Y-m-d H:i:s');
    	unset($p_data['usuario_id']);
    	$r = $this->db->insert('usuarios',$p_data);
    	return $r;
    
    }
    
    private function getNuevoCodigoActivacion(){
    	$key = "";
    	$caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$length = 10;
    	$max = strlen($caracteres) - 1;
    	for ($i=0;$i<$length;$i++) {
    		$key .= substr($caracteres, rand(0, $max), 1);
    	}
    	return $key;
    }
    
    public function aprobar($p_data){
    
    	$this->db->select('usuario_user, usuario_id, usuario_nombre, usuario_apellido, usuario_email');
    	$this->db->where('usuario_cod_act', $p_data['codigo_activacion']);
    	$this->db->where('usuario_estado', 'inactivo');
    
    	$v_consulta = $this->db->get('usuarios');
    
    	if($v_consulta->num_rows()==1){ // Existe
    			
    		$this->db->select('usuario_user, usuario_id, usuario_nombre, usuario_apellido, usuario_email');
    		$this->db->where('usuario_cod_act', $p_data['codigo_activacion']);
    		$this->db->where('HOUR(TIMEDIFF(usuario_fecha_reg, now())) <=',72);
    		$v_hora = $this->db->get('usuarios');
    		if($v_hora->num_rows()==1){
    			$this->db->update('usuarios',array('usuario_estado'=>'activo','usuario_cod_act'=>''));
    			return 1;
    		}else{
    			$key = $this->getNuevoCodigoActivacion();
    			$usuario = $v_consulta->row();    			
    			$this->db->where('usuario_id',$usuario->usuario_id);
    			$this->db->set('usuario_fecha_reg',date('Y-m-d H:i:s'));
    			$this->db->set('usuario_cod_act',$key);
    			$this->db->update('usuarios');    			
    			$mensaje = 'Para activar el acceso de click al sgte enlace '.base_url().'seguridad/aprobar?codigo='.$key;
    			$asunto = 'Codigo de re-activacion';    			
    			$this->enviarMailCodigoActivacion($usuario->usuario_email, $mensaje,$asunto);    			
    			
    			return 2;
    		}
    	}else{
    		return 0;
    	}
    }
	
}
/* End of file cliente.php */