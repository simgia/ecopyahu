<?php 
/**
 * @author jbauer
 * baja cuenta de usuario.
 * */
$this->load->view('comunes/cabecera_ext');
?>
<body id='pag_login' class='color_body'>
<?php $this->load->view('comunes/menu');?>
<!--CUERPO-->
<div id="cuerpo" class="wrap clearfix">
	 <div class="left">
        <div id="acceso">
         	<h3 class="tit1">Baja de la cuenta</h3>
			<form action="<?php echo  base_url() ?>usuarios/baja_cuenta" method="post" id="formulario_baja_cuenta">
				<?php if($mensaje!="") echo "<p style='color:red;'>$mensaje</p>";?>
				<?php echo form_error('password','<p style="color:red;">','</p>'); ?>
				<div id="password">
				</div>
				<br>
				<div>
					<label></label><input type="button" class="input_submit" value="Dar de Baja" onclick="enviarFormulario()">
				</div> 
			</form>
		</div>
	</div>
</div>

	<script type='text/javascript'>
		// Varibles.
		var v_password; 
	
		Ext.onReady(function(){
			v_password = Ext.create('Ext.form.field.Text',{
			 	renderTo: 'password',
			 	name: 'password',
			 	inputType : 'password',
			 	fieldLabel: 'Contrase&ntilde;a',
			 	allowBlank: false,
			 	validateOnChange: false,
		 		validateOnBlur: false
			});
		});
		
		function enviarFormulario(){
			if(v_password.isValid()){
				Ext.getDom("formulario_baja_cuenta").submit();		
			}
		}
	</script>
<?php $this->load->view('comunes/pie')?>	
</body>
</html>