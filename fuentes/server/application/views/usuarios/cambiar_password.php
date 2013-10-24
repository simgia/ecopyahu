<?php 
/**
 * @author josego
 * @package dmp
 * La vista cambiar_password donde el usuario puede cambiar su password.
 */
$this->load->view('comunes/cabecera_ext');
	#$mensaje_pwd_anterior = "";
	// Si viene $mensaje_pwd_anterior sin valor es porque no es un error de verificacion de password anterior incorrecto.
	if(!$mensaje_pwd_anterior){
		$mensaje_pwd_anterior = "";
	}
?>
<body id="pag_login" class='color_body'>
<?php $this->load->view('comunes/menu');?>
<!--CUERPO-->
	<script type='text/javascript'>
		// Variables
		var v_pwd_anterior;									// Password anterior.
		var v_pwd_nuevo;									// Password nuevo
		var v_pwd_nuevo_confirmacion;						// Password nuevo confirmacion.

		var keyPressHandler = function(p_field, e){
			if(e.keyCode==e.ENTER) {
				if(p_field.id=='password_anterior')
					Ext.getCmp('password_anterior').focus(false, true);
				else
					enviarFormulario();
			}
		};
		
		Ext.onReady(function(){
			v_pwd_anterior = Ext.create('Ext.form.field.Text',{
				id: 'password_anterior',
			 	renderTo:'password_anterior',
			 	name: 'password_anterior',
			 	inputType : 'password',
			 	fieldLabel: 'Actual',
			 	labelAlign: 'top',
			 	allowBlank: false,
			 	validateOnChange: false,
			 	validateOnBlur: false,
			 	listeners: {
	                render: function(p_field){			// Se utiliza para localizar el foco en el textbox del usuario.
	                    p_field.focus(false, true);
	                }
	            }
			});
			v_pwd_nuevo = Ext.create('Ext.form.field.Text',{
				id: 'password_nuevo',
			 	renderTo:'password_nuevo',
			 	name: 'password_nuevo',
			 	inputType : 'password',
			 	fieldLabel: 'Nueva',
			 	labelAlign: 'top',
			 	allowBlank: false,
			 	validateOnChange: false,
			 	validateOnBlur: false
			});
			v_pwd_nuevo_confirmacion = Ext.create('Ext.form.field.Text',{
			 	renderTo:'passconf_nuevo',
			 	name: 'passconf_nuevo',
			 	inputType : 'password',
			 	fieldLabel: 'Confirmar',
			 	labelAlign: 'top',
			 	allowBlank: false,
			 	validateOnChange: false,
			 	validateOnBlur: false,
			 	validator: function() {
	                var v_pass_nuevo = v_pwd_nuevo.getValue();
	                var v_pass_nuevo_confirmacion = v_pwd_nuevo_confirmacion.getValue();

	                // Compara si el password nuevo y el passowrd nuevo confirmacion coinciden.
	                if (v_pass_nuevo == v_pass_nuevo_confirmacion){
	                	return true;
	                }else{
	                	return "El password nuevo no concuerda!";
	                }        
	            },
	            enableKeyEvents: true,
			 	listeners: {
	                /*render: function(p_field){			// Se utiliza para localizar el foco en el textbox del usuario.
	                    p_field.focus(false, true);
	                    p_field.inputEl.dom.autocomplete = 'on';
	                },*/
	                keypress: keyPressHandler			// Maneja el ENTER
	            }
			});
		});
		
		function enviarFormulario(){
			if(v_pwd_anterior.isValid()&&v_pwd_nuevo.isValid()&&v_pwd_nuevo_confirmacion.isValid()){
				Ext.getDom("formulario_cambiar_pass").submit();
			}
		}
	</script>
	<div id="cuerpo" class="wrap clearfix alink">
		<div class="left alink">
    		<div id="acceso" class= "alink">
    		 	<h3 class="tit1">Cambiar Contraseña</h3>
				<form action="<?php echo  base_url() ?>usuarios/cambiar_password" method="post" id="formulario_cambiar_pass">
					<div id="password_anterior">
					</div>
					<?php if($mensaje_pwd_anterior != '') echo "<p style='color:red';>$mensaje_pwd_anterior</p>" ?>
					<div id="password_nuevo">
					</div>
					<?php echo form_error('password_nuevo','<p style="color:red;">','</p>'); ?>
					<div id="passconf_nuevo">
					</div>
					<?php echo form_error('passconf_nuevo','<p style="color:red;">','</p>'); ?>
					<br>
					<div>
					<label></label><input type="button" value="Cambiar" class="input_submit" onclick="enviarFormulario()">
					</div> 
				</form>
			</div>
		</div>
		<div class="right">
    	<h3>Cambiar su contraseña es f&aacute;cil.</h3>
      	<ul>
            <li>Ingrese la contraseña que estaba usando en el campo "Actual".</li>
        	<li>Ingrese la contraseña que quiere utilizar a partir de ahora en el campo "Nueva".</li>
        	<li>Ingrese su nueva contraseña en el campo "Confirmar" para evitar errores.</li>
      	</ul>
    </div><!--right-->
	</div>
<?php $this->load->view('comunes/pie')?>	
</body>
</html>