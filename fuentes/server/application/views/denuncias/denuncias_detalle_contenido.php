<h4><?php if(isset($p_textos["tipo_denuncia"])) echo $p_textos["tipo_denuncia"];?></h4>
<?php
    // Definicion de impresion.
    $v_impresion = array();
    if(isset($p_textos["fecha_registro"])) $v_impresion[] = $p_textos["fecha_registro"];
    if(isset($p_textos["denuncia_desc"])) $v_impresion[] = $p_textos["denuncia_desc"];
    if(isset($p_textos["categoria_nombre"])) $v_impresion[] = $p_textos["categoria_nombre"];
    if(isset($p_textos["denuncia_fuente"])) $v_impresion[] = $p_textos["denuncia_fuente"];
    //if(isset($p_textos["denuncia_estado"])) $v_impresion[] = $p_textos["denuncia_estado"];
    
    $v_contador = 1;
    $v_elementos_columna = 8;
    $v_elementos = '';
    $v_columnas = 0;
    echo "<p>";
    foreach($v_impresion as $v_elemento) {
        if(trim($v_elemento) == ""){
            continue;
        }
        
        if($v_elemento == "mapa") {
            $v_url = "http://maps.googleapis.com/maps/api/staticmap?center=";
            $v_url .= $p_textos["latitud_longitud_str"];
            $v_url .= "&zoom=".$p_textos["zoom_mapa"];
            $v_url .= "&size=220x200&markers=color:red|color:red|".$p_textos["latitud_longitud_str"];
            $v_url .= "&sensor=false&maptype=".$p_textos["tipo_mapa"];

            $v_elementos .= '</ul>';
            $v_elementos .= '<p><img width="220" src="'.$v_url.'"><p>';
            $v_elementos .= '<ul>'; //Abre nuevamente para los siguientes elementos

            //Por ser el mapa no se imprime mas nada
            $v_contador=0;
        
        }else{
            $v_elementos .= '<li>'.$v_elemento.'</li>';
        }
        
        //Corte
        if ($v_contador % $v_elementos_columna == 0) {
            $v_columnas++;
            echo '<div class="colX4"><ul>';
            echo $v_elementos;
            echo '</ul></div>';
            $v_elementos = '';
        }
        $v_contador++;
    }
    
    // Si hay elementos encolados.
    if($v_elementos!='') {
        echo '<div class="colX4"><ul>';
        echo $v_elementos;
        echo '</ul></div>';
    }
    
    // Imagenes.
    if(isset($p_nombre_imagenes) && count($p_nombre_imagenes) > 0){
        $v_contador=1;
        $v_elementos_columna=2;
        echo '<div class="colX4">';
        echo "<p>Se adjunta la im&aacute;gen:</p>";
        foreach($p_nombre_imagenes as $v_nombre) {
            $v_size = "/150/150";
            if(!isset($vista_detalle)){
                $v_url = "/resources/user_upload/$v_nombre";
                $v_link = "";
            }else{
                 $v_url = "/resources/user_upload/". $v_nombre["multimedia_file_name"];
                 //$v_link = "<a href='javascript:window.open(\"$v_url\")'>Ver Original</a>";
                 //$v_link = "<a href='$v_url' target = '_blank'>Ver Original</a>";
            }
            //echo "<div class='imagenes-denunciante'><img src='$v_url"."$v_size' width='150'>".$v_link."</div>";
            //echo "<div class='imagenes-denunciante'><img src='$v_url"."' width='150'>".$v_link."</div>";
            echo "<div class='imagenes-denunciante'><img src='$v_url"."' width='150'></div>";
            
            if (($v_contador % $v_elementos_columna == 0)){
                $v_columnas++;
                echo '</p></div><div class="colX4"><p>';
            }
            if($v_columnas >= 3)
                $v_elementos_columna = 1;
                
            $v_contador++;
        }
        echo "</div>";
    }
    echo "</p>";
?>
