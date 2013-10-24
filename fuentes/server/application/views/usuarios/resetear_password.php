
<?php 
/**
 * @author josego
 * Activar cuenta de usuario.
 * */
$this->load->view('comunes/cabecera_ext');
?>
<body id='pag_login' class='color_body'>
<?php $this->load->view('comunes/menu');?>
<!--CUERPO-->
<div id="cuerpo" class="wrap clearfix">
	 <div class="left">
        <div id="acceso">
         	<h3 class="tit1">Resetear Contraseña</h3>
         	<p>Su nueva contraseña se le enviara a su cuenta de email.</p>
         	<p style="color:red;"><?php echo $v_mensaje;?></p>
         	<?php echo form_error('email','<p style="color:red;">','</p>'); ?>
			<form action="<?php echo  base_url() ?>usuarios/resetear_password" method="post" id="formulario_resetear_password">
				<div id="email">
				</div>
				<br>
				<div>
					<label></label><input type="button" class="input_submit" value="Enviar" onclick="enviarFormulario()">
				</div> 
			</form>
		</div>
	</div>
</div>

<?php $v_email = set_value('email');?>
	<script type='text/javascript'>
		// Varibles.
		var v_email;

		var keyPressHandler = function(p_field, e){
			if(e.keyCode==e.ENTER) {
				enviarFormulario();		
			}
		};
	
		Ext.onReady(function(){
			v_email = Ext.create('Ext.form.field.Text',{
			 	renderTo: 'email',
			 	name: 'email',
			 	vtype: 'email',
			 	value: '<?php echo $v_email; ?>',
			 	allowBlank: false,
			 	fieldLabel: 'Email',
			 	validateOnChange: false,
		 		validateOnBlur: false,
		 		enableKeyEvents: true,
		 		listeners: {
	                keypress: keyPressHandler			// Maneja el ENTER
	            }
			});
		});
		
		function enviarFormulario(){
			if(v_email.isValid()){
				Ext.getDom("formulario_resetear_password").submit();		
			}
		}
	</script>
<?php $this->load->view('comunes/pie')?>	
</body>
</html>