<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author juan bauer @bauerpy
 * @package ecopyahu
 *
*/
class registro extends SMG_Controller{

	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Metodo por defecto, redirige a listado
	 */
	public function index(){
		
	}
        
                /**
	 * M&uacute;todo con el cual se registra un usuario al sistema.
	 * @param string nombre [via POST]
	 * @param string apellidos [via POST]
	 * @param string email [via POST]
	 * @param string celular [via POST]
	 * @param date fecha_nacimiento [via POST]
	 * @param string password [via POST]
	 * @param string passconf [via POST]
	 * @param string medio_activacion [via POST]
	 * //@return json
	 * @return void
	 */
	public function registro_usuario(){
		$this->validar_permiso(array("anonimo"));
		$opciones_vista["mensaje_captcha"] = "";
		
		$v_nombre = $this->input->post('nombre', true);
		$v_apellidos = $this->input->post('apellidos', true);
		$v_email = $this->input->post('email', true);
		$v_celular = $this->input->post('celular', true);
		$v_fecha_nacimiento = "";
		$v_password = $this->input->post('password', true);
		$v_passconf = $this->input->post('passconf', true);
		$v_estado = "pendiente";
		$v_codigo_activacion = $this->crear_codigo_activacion();
		$v_date = $this->fecha_hora;
		$v_forma_contacto = $this->input->post('forma_contacto', true);
                                $v_cod_captcha = $this->input->post('txt_captcha', true);
                                $v_institucion= $this->input->post('institucion', true);

                                $this->data['instituciones'] = $this->usuarios->get_instituciones();
                                        // Guarda en una variable de session para luego utilizar en el formulario de registro de usuario.
                                        //$this->guardar_session_usuario($v_nombre, $v_apellidos, $v_email, $v_celular);
		
		if ($this->form_validation->run('registro_usuario') == FALSE){
			// Parametros que se envian a la vista editar_usuario.
			$opciones_vista['titulo'] = "Registro de usuario";								// El titulo que se muestra en el navegador.
			$opciones_vista['vista'] = "registro_usuario";									// Modo registro usuario.
			$opciones_vista['primera_vez_edicion'] = "no";									// Si es la primera vez que entra para editar usuario. Este valor al registrar usuario siempre va a ser "no".
			$this->data["opciones_vista"] = $opciones_vista;
			
			// Se dirige a la vista editar_usuario con parametros a la vista editar_usuario.
			$this->load->view('usuarios/editar_usuario', $this->data);
		}else if($v_cod_captcha != $this->ci->session->userdata('codigo_captcha')){
			// Parametros que se envian a la vista editar_usuario.
			$opciones_vista['titulo'] = "Registro de usuario";								// El titulo que se muestra en el navegador.
			$opciones_vista['vista'] = "registro_usuario";									// Modo registro usuario.
			$opciones_vista['mensaje_captcha'] = "C&oacute;digo de imagen incorrecta.";		// Mensaje de error de captcha.
			$opciones_vista['primera_vez_edicion'] = "no";									// Si es la primera vez que entra para editar usuario. Este valor al registrar usuario siempre va a ser "no".
			$this->data["opciones_vista"]=$opciones_vista;
			
			// Se dirige a la vista editar_usuario con parametros a la vista editar_usuario.
			$this->load->view('usuarios/editar_usuario', $this->data);
		}else{
			if($v_institucion!=null)
				$v_cat_usuario = 2;
			else 
				$v_cat_usuario = 1;
			if($this->usuarios->guardar_usuario(true, $v_nombre, $v_apellidos, $v_email, $v_celular, $v_fecha_nacimiento, $v_estado, $v_codigo_activacion, $v_password, $v_date, $v_forma_contacto, $v_institucion, $v_cat_usuario)){
				$v_data = array("success"=>true);
				//echo "Registro Exitoso.<br>"; 
				
				// Si es por mail.
				if($v_institucion==null){
					$v_base_url = base_url(). "usuarios/activar_cuenta/?email=".urlencode($v_email);
					$v_mensaje = "El c&oacute;digo de activaci&oacute;n es: <b>" . $v_codigo_activacion . "</b><br>".
					    "Tenes 24 horas para poder activar tu cuenta.<br><br>" .
						"<a href=".$v_base_url.">Click</a> para activar cuenta";
					
					// Se envia el codigo de activacion por mail al usuario.
					$this->enviar_email($v_email, $v_mensaje);
					
					$this->data['v_titulo']="Registro Exitoso.";
					$this->data['v_mensaje']="Se le ha enviado un email con el c&oacute;digo de activaci&oacute;n de su cuenta.";
					
					if(SMS=='activo' && $v_celular!=''){
						$this->load->library('sms');
						$this->sms->envioSms($v_celular,"Su código de activación para el sistema de denuncias es: $v_codigo_activacion");	
					}				
				}else{
					//email para el administrador
					$v_mensaje = "Su cuenta ha sido creada, un administrador verificara los datos de instituci&oacute;n y se pondr&aacute; en contacto con usted";
					
					// Se envia el codigo de activacion por mail al usuario.
					$this->enviar_email($v_email, $v_mensaje);
					
					//envio de email al administrador					
					$v_mensaje = "Se ha creado una cuenta para una instituci&oacute;n con el email $v_email , favor verificar, activar y contestar al encargado de la misma.";
						
					// Se envia el codigo de activacion por mail al usuario.
					$this->enviar_email(EMAIL_ADMINISTRADOR, $v_mensaje);
					
					$this->data['v_titulo']="Registro Exitoso.";
					$this->data['v_mensaje']="Se le ha enviado un email. Su cuenta ha sido creada, un administrador verificara los datos de instituci&oacute;n y se pondr&aacute; en contacto con usted.";
						
					if(SMS=='activo' && $v_celular!=''){
						$this->load->library('sms');
						$this->sms->envioSms($v_celular,"Su cuenta ha sido creada, un administrador verificara los datos de institución y se pondra en contacto.");
					}
				}
				
				$this->load->view('comunes/informaciones', $this->data);
					
			}else{
				$this->data['v_titulo']="Registro Erroneo.";
				$this->data['v_mensaje']="Ha ocurrido un error al intentar activar su cuenta, por favor intentelo nuevamente.";
				$this->load->view('comunes/informaciones', $this->data);
				//$v_data = array("success"=>false);
				//redirect("seguridad");
				//$this->load->view('seguridad/login', $this->data);
			}
		}
	} // Fin de la funcion publica registro_usuario.
        
        	/**
	 * Metodo que crea la imagen captcha.
	 * @return void
	 */
	public function captcha(){
		/*
		 * Posiciones del array:
		*  - [0] --> cantidad_caracteres
		*  - [1] --> tamanio_fuente
		*  - [2] --> posicion_x
		*  - [3] --> posicion_y
		*
		*/
		$param = array(6, 5, 5, 10);
	
		// Carga la libreria Captcha.
		$this->load->library('captcha', $param);
	
		// Guarda el objeto captcha en el array $data.
		$data['captcha'] = $this->captcha;

                                // Guardar en una variable de session el valor del codigo captcha.
		$this->session->set_userdata("codigo_captcha", $this->captcha->getCaptcha());
		
		// Envia el array $data en la vista captcha.
		$this->load->view('seguridad/captcha', $data);
	} // Fin de la funcion publica captcha.
        
                 /**
	 * Metodo con el cual se puede dar de baja un usuario.
	 * Se envia un mail avisando su baja.
	 * @param string email [via POST]
	 * @return void
	 */
	public function baja_cuenta(){
		$this->validar_permiso(array('administrador','usuario', 'institucion'));
		$v_password = $this->input->post('password', true);
		$this->data["p_menu_actual"] = "usuarios";
		$this->data["mensaje"] = "";
		$v_usuario_email = $this->session->userdata('usuario')->USUARIO_EMAIL;
		if ($this->form_validation->run('baja_cuenta') == FALSE){
			$this->load->view('usuarios/baja_cuenta', $this->data);
		}else{
			if(!$this->usuarios->is_password_anterior_correcto($v_usuario_email, $v_password)){
				$this->data["mensaje"] = "Contraseña incorrecta.";
				$this->load->view('usuarios/baja_cuenta', $this->data);
			}else{
				//$v_data['resultado'] = false;
				//$v_data['mensaje'] = 'Se produjo un error al dar de baja la cuenta.';
				
				$v_resultado = $this->usuarios->cambiar_estado_usuario($v_usuario_email, 'inactivo');
				
				if($v_resultado){
					//$v_data['resultado'] = true;
					//$v_data['mensaje'] = 'Se borro con &eacute;xito el usuario.';
					$v_mensaje = "La cuenta fue dada de baja exitosamente, si la quiere recuperar por favor comunicarse con admin@dmp.gov.py";
					
					$this->enviar_email($this->session->userdata('usuario')->USUARIO_EMAIL, $v_mensaje, 'Baja de cuenta - Ministerio Público');
					
					$this->seguridad->logout();
					
					 $this->verificacion_inicial();
					
					$this->data['v_titulo']="Baja Cuenta";
					$this->data['v_mensaje']="Su cuenta ha sido dada de baja en forma exitosa, se le envío un email a $v_usuario_email.";
					$this->load->view('comunes/informaciones', $this->data);
				}else{
					$this->data['v_titulo']="Error";
					$this->data['v_mensaje']="Ocurrió un error al intentar dar de baja su cuenta, favor intentar nuevamente.";
					$this->load->view('comunes/informaciones', $this->data);
				}
			}
		}
		
	} // Fin de la funcion publica baja_usuario.
	
	/**
	 * Activar cuenta.
	 * @param string $v_email [via POST]
	 * @param string $v_codigo_activacion [via POST]
	 * @return void
	 */
	public function activar_cuenta(){
		$this->data["p_menu_actual"] = "usuarios";
		$v_email = urldecode($this->input->get_post('email', true));		
		$v_codigo_activacion = $this->input->post('codigo_activacion', true);
		$this->data['v_email'] = $v_email;
		$this->data['v_mensaje'] = '';
		if ($this->form_validation->run('activacion_usuario') == FALSE){
			$this->load->view('usuarios/activar_cuenta', $this->data);
		}else{
			// Almacena en la variable $v_milisegundos_transcurridas los milisegundos transcurridos entre el tiempo que se registro y el tiempoque quiso activar su cuenta.
			$v_milisegundos_transcurridas = $this->usuarios->get_milisegundos_trascurridos($v_email);
			
			if(!$this->usuarios->is_cuenta_activa($v_email)){
				// La constante es 86400000 (es equivalente a 1 dia o 24 horas) porque es el tiempo máximo que puede pasar para poder activar la cuenta.
				if((86400000 >= $v_milisegundos_transcurridas) && (-1 != $v_milisegundos_transcurridas) ){
					$usuario_id = $this->usuarios->verificar_codigo_activacion($v_email, $v_codigo_activacion);
					// Si es -1 es porque el usuario con el mail con el codigo de activacion no existe o ya esta activado..
					if($usuario_id != -1){
						if($this->usuarios->cambiar_estado_usuario($v_email, "activo")){
							$this->data['v_titulo']="Activaci&oacute;n Exitosa.";
							$this->data['v_mensaje']="Ya puede iniciar sesi&oacute;n con el email y la contraseña con la que se registro.";
							$this->load->view('comunes/informaciones', $this->data);
						}else{
							$this->data['v_titulo']="Activaci&oacute;n Erronea.";
							$this->data['v_mensaje']="Ocurrio un error al intentar activar la cuenta, por favor intentelo nuevamente. Si el error persiste comuniquese con ".EMAIL_ADMINISTRADOR;
							$this->load->view('comunes/informaciones', $this->data);
						}
					}else{
						$this->data['v_mensaje'] = "C&oacute;digo erroneo, por favor intentelo nuevamente.";
						$this->load->view('usuarios/activar_cuenta', $this->data);
					}
				}else{					
					// Si guarda en la base de datos el nuevo codigo de verificacion, entonces manda un nuevo mail.
					// Caso contrario, no manda mail y avisa al usuario para que se comunique a admin@dmp.gov.py.
					$v_codigo_activacion = $this->crear_codigo_activacion();
					if($this->usuarios->cambiar_codigo_verificacion($v_email, $v_codigo_activacion, 'pendiente')){
						$v_date = $this->fecha_hora;
						
						// Solo si cambia el timestamp a la fecha actual, manda un mail, caso contrario no lo hace.
						if($this->usuarios->cambiar_fecha_registro($v_email, 'pendiente', $v_date)){
							$v_base_url = base_url(). "usuarios/activar_cuenta/?email=".urlencode($v_email);
							$v_mensaje = "El c&oacute;digo de activaci&oacute;n es: <b>" . $v_codigo_activacion . "</b><br>".
					    	"Tenes 24 horas para poder activar su cuenta.<br><br>" .
							"<a href=".$v_base_url.">Click</a> para activar cuenta";
						
							// Se envia el codigo de activacion por mail al usuario.
							$this->enviar_email($v_email, $v_mensaje);
						
							$this->data['v_titulo']="C&oacute;digo Expirado.";
							$this->data['v_mensaje']="Ya pasaron 24 horas para activar tu cuenta. Se te vuelve a enviar otro c&oacute;digo de activaci&oacute;n.";
							$this->load->view('comunes/informaciones', $this->data);
							// Ir a la vista login y esperar que active desde el mail.
							//$this->load->view('seguridad/login', $this->data);
						}else{
							$this->data['v_titulo']="Activaci&oacute;n Erronea.";
							$this->data['v_mensaje']="Ocurrio un error al intentar activar la cuenta, por favor intentelo nuevamente. Si el error persiste comuniquese con ".EMAIL_ADMINISTRADOR;
							$this->load->view('comunes/informaciones', $this->data);
						}
					}else{
						$this->data['v_titulo']="Activaci&oacute;n Erronea.";
						$this->data['v_mensaje']="Ocurrio un error al intentar activar la cuenta, por favor intentelo nuevamente. Si el error persiste comuniquese con ".EMAIL_ADMINISTRADOR;
						$this->load->view('comunes/informaciones', $this->data);
					}
				}
			}else{
				$this->data['v_titulo']="Cuenta Activa.";
				$this->data['v_mensaje']="Su cuenta ya se encuentra activa, por favor diríjase a inicio de sesión para iniciar su cuenta.";
				$this->load->view('comunes/informaciones', $this->data);
			}
		}
	}// Fin de la funcion publica activar_cuenta.
        
                /**
	 * verifica si el email ya existe
	 * @return json
	 */
	public function verificar_email(){
		$v_email = $this->input->post('email', true);
		if($this->seguridad->logged()){
			if(strtoupper($v_email)==strtoupper($this->session->userdata('usuario')->USUARIO_EMAIL))
				$v_email_propio = true;
			else	
				$v_email_propio = false;
		}else{
			$v_email_propio = false;
		}
		$v_existe = false;
		if(($this->usuarios->get_datos_usuario($v_email)!=-1) && !$v_email_propio)
			$v_existe = true;
		
		$this->load->view('comunes/output',array('p_output'=>array('existe'=>$v_existe)));
	}
        
                 /**
	 * Crea el codigo de activacion del usuario.
	 * @return string
	 */
	private function crear_codigo_activacion(){
		// Carga la libreria Generic.
		$this->load->library('generic');
	
		// Se instancia el objeto generic.
		$generic = new generic();
	
		// El parametro 6 es la cantidad de caracteres que contendra el codigo de activacion.
		$codigo_activacion = $generic->generar_codigo(6);
	
		// Retorna el codigo de activacion.
		return $codigo_activacion;
	} // Fin de la funcion privada crear_codigo_activacion.
        
    }
?>
