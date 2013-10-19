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
         	<h3 class="tit1">Activaci&oacute;n de cuenta</h3>
         	 <p style="color:red;"><?php echo $v_mensaje;?></p>
			<form action="<?php echo  base_url() ?>usuarios/activar_cuenta" method="post" id="formulario_activar_cuenta">
				<!-- <input type="text" name='email' value=''> -->
				<div id="email">
				</div>
				<?php echo form_error('email','<p style="color:red;">','</p>'); ?>
				<div id="codigo_activacion">
				</div>
				<?php echo form_error('codigo_activacion','<p style="color:red;">','</p>'); ?>
				<br>
				<div>
					<label></label><input type="button" class="input_submit" value="Activar" onclick="enviarFormulario()">
				</div> 
			</form>
		</div>
	</div>
</div>

	<script type='text/javascript'>
		// Varibles.
		var v_email;
		var v_codigo_activacion; 
	
		Ext.onReady(function(){
			v_email = Ext.create('Ext.form.field.Text',{
			 	renderTo: 'email',
			 	name: 'email',
			 	vtype: 'email',
			 	value: '<?php echo $v_email;?>',
			 	allowBlank: false,
			 	fieldLabel: 'Email',
			 	validateOnChange: false,
		 		validateOnBlur: false
			});
		 	v_codigo_activacion = Ext.create('Ext.form.field.Text',{
			 	renderTo: 'codigo_activacion',
			 	name: 'codigo_activacion',
			 	inputType : 'text',
			 	fieldLabel: 'C&oacute;digo',
			 	maxLength: 6,
			 	enforceMaxLength: true,
			 	allowBlank: false,
			 	validateOnChange: false,
		 		validateOnBlur: false
			});
		});
		
		function enviarFormulario(){
			if(v_email.isValid() && v_codigo_activacion.isValid()){
				Ext.getDom("formulario_activar_cuenta").submit();		
			}
		}
	</script>
<?php $this->load->view('comunes/pie')?>	
</body>
</html>