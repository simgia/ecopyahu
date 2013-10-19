
<?php 
/**
 * @author jbauer
 * Login
 * formulario de login al sistema
 * */
$this->load->view('comunes/cabecera_ext');?>
<body id="pag_login" class='color_body'>
<?php $this->load->view('comunes/menu');?>
<!--CUERPO-->
<div id="cuerpo" class="wrap clearfix">
    <div class="left">
        <div id="acceso">
            <h3 class="tit1">Inicio de sesi&oacute;n</h3>
            <p>Complete el siguiente formulario con sus datos de registro:</p>
            <p style="color:red;"><?php echo $v_mensaje;?></p>
            <form action="<?php echo  base_url() ?>seguridad/login" method="post" id="formulario_login">
                <div id="email">
                </div>
                <?php echo form_error('email','<p style="color:red;">','</p>'); ?>
                <div id="password">
                </div>
                <?php echo form_error('password','<p style="color:red;">','</p>'); ?>
                <br/>
                <div>
                    <a href="<?php echo base_url();?>/usuarios/resetear_password">Olvide mi contraseña!</a>&nbsp;&nbsp;&nbsp;<input type="button" class="input_submit" value="Iniciar sesi&oacute;n" onclick="enviarFormulario()">
                </div> 
            </form>
        </div>
    </div>
    <div class="right">
    	<h3>Reg&iacute;strese y aun permanezca an&oacute;nimo, pero con m&aacute;s beneficios</h3>
      	<ul>
            <li>Sus datos ser&aacute;n tratados en forma confidencial garantizando su anonimato.</li>
        	<li>Agiliza la carga de denuncias ya que hay menos campos obligatorios.</li>
        	<li>Permite dar seguimientos de todas sus denuncias cargadas.</li>
            <li>Tiene mayor detalle de sus denuncias cargadas.</li>
            <li>La Fiscal&iacute;a podr&aacute; contactar con Usted de la manera que Usted elija para ampliar su denuncia.</li>
      	</ul>
        <br/>
        <div>
           <input type="button" class="input_submit align_right" value="Registrarse" onclick="parent.location='<?php echo base_url()?>usuarios/registro_usuario'">
        </div> 
    </div><!--right-->
</div>
<?php $this->load->view('comunes/pie')?>
</body>
</html>
<!--CUERPO--> 
<script type='text/javascript'>
    var v_email;
    var v_pwd; 
    Ext.onReady(function(){
        var v_clean_validate = function(p_field){
            p_field.clearInvalid();
        }
        var keyPressHandler = function(p_field, e){
			if(e.keyCode==e.ENTER) {
				if(p_field.id=='email')
					Ext.getCmp('password').focus(false, true);
				else
					enviarFormulario();
			}
		};
        v_email = Ext.create('Ext.form.field.Text',{
            id: 'email',
            renderTo:'email',
            name: 'email',
            inputId : 'id_email',
            vtype: 'email',
            fieldLabel: 'E-mail',
            width:300,
            value: '<?php echo set_value('email'); ?>',
            allowBlank: false,
            validateOnChange: false,
            validateOnBlur: false,
            enableKeyEvents: true,
            listeners: {
                change: v_clean_validate,
                render: function(p_field){			// Se utiliza para localizar el foco en el textbox del usuario
                    p_field.focus(false, true);
                    p_field.inputEl.dom.autocomplete = 'on';
                },
                keypress: keyPressHandler			// Maneja el ENTER
            }
        });
        v_pwd = Ext.create('Ext.form.field.Text',{
            id:'password',
            renderTo:'password',
            name: 'password',
            inputType : 'password',
            fieldLabel: 'Contrase&ntilde;a',
            width:300,
            allowBlank: false,
            validateOnChange: false,
            validateOnBlur: false,
            enableKeyEvents: true,
            listeners: {
                change: v_clean_validate,
                keypress: keyPressHandler			// Maneja el ENTER
            }
        });
    });
    function enviarFormulario(){
        if(v_email.isValid()&&v_pwd.isValid())
            Ext.getDom("formulario_login").submit();
    }
</script>