
<?php 
/**
 * @author josego
 * @package dmp
 * La vista editar_usuario donde el usuario puede:
 *  - Registrarse en el sistema, pero su cuenta no se activa. La activacion es manual por medio del mail o sms.
 *  - Editar la informacion del usuario.
 */

    /* Si viene la primera vez del menu editar usuario, entonces tiene que cargar los datos que estan de la base de datos.
     * Caso contrario: Si viene otras veces es porque tiene errores y va a completar automaticamente dependiendo:
     * - Si tiene errores del lado del server. Se pone el campo vacio ("").
     * - Si esta no tiene errores coloca el valor que se escribio anteriormente.
     */
    if($opciones_vista["primera_vez_edicion"] == 'si'){
    	$v_nombre = $v_nombre;
    	$v_apellido = $v_apellido;
    	$v_email = $v_email;
    	$v_celular = $v_celular;	
    }else{
    	$v_nombre = set_value('nombre');
    	$v_apellido = set_value('apellidos');
    	$v_email = set_value('email');
    	$v_celular = set_value('celular');
    }
    $this->load->view('comunes/cabecera_ext');
?>
<script type='text/javascript'>
    // Varibles.
    var v_nombre;
    var v_apellidos;
    var v_password;
    var v_passconf;
    var v_email;
    var v_celular; 
    var v_captcha;
    var v_labelAlign = 'top';
    var v_width = 300;

    var v_scope = this;
    Ext.onReady(function(){
        // Mail
        var v_scope = this;

		v_scope.email_valido = true;
		
        v_email = Ext.create('Ext.form.field.Text',{
            fieldLabel : 'E-mail',
            renderTo: 'email',
            name: 'email',
            vtype: 'email',
            labelSeparator: ': *',
            labelClsExtra : 'input_label',
            allowBlank: false,
            value: '<?php echo $v_email; ?>',
            validateOnChange: false,
            validateOnBlur: false,
            labelAlign: v_labelAlign,
            width: v_width,
            validator: function() {return v_scope.email_valido;},
            listeners: {
                render: function(p_field){
                    p_field.focus(false, true);
                },
                blur: function(p_field){
                	Ext.Ajax.request({
	            	    url: '<?php echo base_url();?>usuarios/verificar_email',
	            	    params: {
	            	        email: p_field.getValue()
	            	    },
	            	    success: function(p_response){
	    					var v_respuesta = Ext.JSON.decode(p_response.responseText);
	    					if(v_respuesta.existe) {
	    						v_scope.email_valido = 'Ya existe una cuenta con el email asociado, por favor utilice otra cuenta de email o pongase en contacto con <?php echo EMAIL_ADMINISTRADOR;?>';
	    					}else{
	    						v_scope.email_valido = true;
		    				}
		    				p_field.isValid();
	    				}
	            	});
                }
            },
            tabIndex: 1
        });
        // Celular.
        v_celular = Ext.create('Ext.form.field.Text',{
            fieldLabel : 'Celular',
            renderTo: 'celular',
            name: 'celular',
            inputType : 'text',
            maxLength: 20,
            enforceMaxLength: true,
            value: '<?php echo $v_celular; ?>',
            regexText: 'Ejemplo: +595981555555 o 0981555555',
	    maskRe:/(\+|[0-9])/,
            regex:/^(\+)?([0-9]+)$/,
            validateOnChange: true,
            validateOnBlur: true,
            labelAlign: v_labelAlign,
            width: v_width,
            tabIndex: 2
        });
        // Nombre.
        v_nombre = Ext.create('Ext.form.field.Text',{
            fieldLabel: 'Nombre',
            renderTo: 'nombre',
            name: 'nombre',
            inputType: 'text',
            //allowBlank: false,
            value: '<?php echo $v_nombre; ?>',
            validateOnChange: false,
            validateOnBlur: false,
            labelAlign: v_labelAlign,
            width: v_width,
            tabIndex: 3
        });
        // Apellidos.
        v_apellidos = Ext.create('Ext.form.field.Text',{
            fieldLabel: 'Apellido',
            renderTo: 'apellidos',
            name: 'apellidos',
            inputType: 'text',
            //allowBlank: false,
            value: '<?php echo $v_apellido; ?>',
            validateOnChange: false,
            validateOnBlur: false,
            labelAlign: v_labelAlign,
            width: v_width,
            tabIndex: 4
        });
    
        if(<?php echo "'".$opciones_vista["vista"]."'"?> == 'registro_usuario'){
            v_password = Ext.create('Ext.form.field.Text',{
                fieldLabel: 'Contrase&ntilde;a',
                labelSeparator: ': *',
                renderTo: 'password',
                name: 'password',
                inputType : 'password',
                allowBlank: false,
                validateOnChange: false,
                validateOnBlur: false,
                labelAlign: v_labelAlign,
                width: v_width,
                tabIndex: 5
            });
            // Password confirmacion.
            v_passconf = Ext.create('Ext.form.field.Text',{
                fieldLabel: 'Repita la Contrase&ntilde;a',
                labelSeparator: ': *',
                renderTo: 'passconf',
                name: 'passconf',
                inputType : 'password',
                allowBlank: false,
                validateOnChange: false,
                validateOnBlur: false,
                validator: function() {
                    var v_pass1 = v_password.getValue();
                    var v_pass2 = v_passconf.getValue();

                    // Compara si los dos pass coinciden.
                    if (v_pass1 == v_pass2){
                       return true;
                    }else{
                       return 'El password  no concuerda!';
                    }
                },
                labelAlign: v_labelAlign,
                width: v_width,
                tabIndex: 5
            });
        	// Captcha.
        	v_captcha = Ext.create('Ext.form.field.Text',{
	            fieldLabel : 'Introduzca el c&oacute;digo de la imagen',
	            labelSeparator: ': *',
	            renderTo: 'txt_captcha',
	            name: 'txt_captcha',
	            inputType : 'text',
	            allowBlank: false,
	            validateOnChange: false,
	            validateOnBlur: false,
	            labelAlign: v_labelAlign,
	            tabIndex: 7
        	});
       	} // Fin del if (Si la vista es registro usuario.)

        /*v_scope.preguntarDarBaja = function(){
            Ext.MessageBox.show({
				title:'Se cambiara de estado',
				msg: '&iquest;Esta seguro que quiere dar de baja su cuenta?',
				buttons: Ext.MessageBox.YESNO,
				fn: function(p_btn){
					if(p_btn=='yes') {
						window.location = usuarios/baja_usuario';
					} 
				},
				//animEl: v_scope,
				icon: Ext.MessageBox.QUESTION
			});
        }*/
       	
    }); // Fin de la funcion Ext.onReady.
    
    function enviarFormulario(){
        // Si el modo es registro usuario.
        if('<?php echo $opciones_vista["vista"]?>' == 'registro_usuario'){
            if(v_nombre.isValid() && v_apellidos.isValid() && v_password.isValid() && v_passconf.isValid() && v_email.isValid() && v_celular.isValid() && v_captcha.isValid()){
                Ext.getDom('formulario_editar_usuario').submit();
            }
        } // Si la vista es editar_usuario.
        else{
        	if(v_nombre.isValid() && v_apellidos.isValid() && v_email.isValid() && v_celular.isValid()){
                Ext.getDom('formulario_editar_usuario').submit();
            }
        }
    } // Fin de la funcion enviarFormulario.
    </script>
<body id='pag_denunciar' class='color_body'>
<?php $this->load->view('comunes/menu');?>
<!--CUERPO-->
<div id="cuerpo" class="wrap clearfix">
    <div style="padding: 30px"></div>
    <div id="cont_tabs">
      <div class="gris3">
       <h3 class="tit1"><?php echo $opciones_vista["titulo"]; ?></h3>
       <form action='<?php echo base_url()."usuarios/".$opciones_vista['vista'];?>' method='post' id='formulario_editar_usuario' >
			<br>
            <div id='form_left_reg' style="margin-bottom: 10px;">
                <div id="email">                    
                </div>
                <?php echo form_error('email','<p style="color:red;">','</p>'); ?>
                <br>
                <div id="nombre">
                </div>
                <?php echo form_error('nombre','<p style="color:red;">','</p>'); ?>
                <?php 
                    // En caso que sea Modo registro_usuario se da la opcion de password y la forma de contacto preferida.
                    if($opciones_vista["vista"] == 'registro_usuario'){
                        echo "<br><div id='password'>";
                        echo "</div>";
                        echo form_error('password','<p style="color:red;">','</p>');
                  		echo "<br>
                			  <label>Forma de Contacto Preferida</label><br>
                			  <select name = 'forma_contacto' tabindex=6>
                    		      <option value = 'email'>E-mail</option>
                                  <option value = 'celular'>Celular</option>
                                  <option value = 'no_contactar'>No contactar</option>
                              </select>"; 
                  		 
                  		echo "<br><br>
                  		<label>Pertenece a una Instituci&oacute;n</label><br>
                  		<select name = 'institucion' tabindex=6>
                  		<option value = ''>Ninguna</option>";
                  		foreach($instituciones as $institucion){
                  			echo "<option value = '".$institucion->INSTITUCION_ID."'>".$institucion->INSTITUCION_NOMBRE."</option>";
                  		}
                  		echo "</select>";
                	}else{
                		echo "<br><br><a href='".base_url()."usuarios/baja_cuenta'>Quiero dar de baja mi cuenta!</a>";
                		//echo "<br><br><a href='".base_url()."/usuarios/resetear_password'>Quiero dar de baja mi cuenta!</a>";
                	}
                ?>
            </div>
            <div id='form_right_reg' style="margin-bottom: 10px;">
                <div id="celular">
                </div>
                 <?php echo form_error('celular','<p style="color:red;">','</p>'); ?>
                <br>
                <div id="apellidos">
                </div>
                <?php echo form_error('apellidos','<p style="color:red;">','</p>'); ?>
                <?php 
                    // En caso que sea Modo regsitro_usuario se da la opcion de password.
                    if($opciones_vista["vista"] == 'registro_usuario'){
                        echo "<br><div id='passconf'>";
                        echo "</div>";
                        echo form_error('passconf','<p style="color:red;">','</p>');
                    }
                ?>
                 <?php 
                    // Si es Registro de Usuario aparece el captcha. 
                    // En caso que sea Modo editar_usuario no aparece el captcha.
                    if($opciones_vista["vista"] == 'registro_usuario'){
                        echo "<br><div id='txt_captcha'>";
                        echo "</div>";
                        echo form_error('txt_captcha','<p style="color:red;">','</p>');
                        echo "<div><img src=".base_url()."usuarios/captcha id = 'img_captcha' name = 'img_captcha'>";
                        echo "<a class=captcha href='#' onclick=\""."document.getElementById('img_captcha').src='".base_url(). "usuarios/captcha". "?'+Math.floor(Math.random()*1000)+ 1;\" id='change-image'>Cambiar Imagen</a></div>";
                        echo "<p style='color:red;'>".$opciones_vista["mensaje_captcha"]."</p>";
                    }
                ?>
                <?php 
	                if($opciones_vista["vista"] == 'registro_usuario'){
	                	echo "<div class='error_captcha'></div>";
	                }
                ?>
                <?php 
                	if($opciones_vista["vista"] == 'editar_usuario'){
                		echo "<input type='hidden' name='editar' id = 'editar' value='si'>";
                	}
                ?>
            </div>
           
            <div id="siguientes" class="clear">
                <?php 
                // Si el modo es registro usuario
                if($opciones_vista["vista"] == 'registro_usuario'){
                    echo "<input type='button' class='input_submit' value='Registrarse' onclick='enviarFormulario()'>";
                }else{
                    echo "<input type='button' class='input_submit' value='Guardar' onclick='enviarFormulario()'>";
                }
                ?>
                <input type="reset"  class="input_submit" value="Limpiar">
            </div>
        </form>
    </div>
    </div>
</div>
<!--CUERPO--> 
<?php $this->load->view('comunes/pie')?>
</body>
</html>