
<?php 
/**
 * @author jbauer @bauerpy
 * @package ecopyahu
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
    $this->load->view('comunes/cabecera');
?>

<body>		
    <div id="content-denunciar">		
            
            <?php $this->load->view('comunes/menu')?>

            <h2>Registro</h2>
            <form>


            </form>

            <br />
            <br />
    </div>
<!--CUERPO-->
<?php $this->load->view('comunes/pie')?>
</body>
</html>


<!-- 

lo que viene abajo es lo que quedo de otro formulario de registro, pero con elementos extjs, queda como ejemplo

--!>
<<!--CUERPO-->
<body>		
    <div id="content-denunciar">		
            
            <?php $this->load->view('comunes/menu')?>

            <h2>Registrarse</h2>
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