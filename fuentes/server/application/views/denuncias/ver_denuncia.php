<?php 
/**
 * @author jbauer @bauerpy
 * */
?>
<div  style="max-width:700px; margin: 20px auto; padding:20px; background-color:#ffffff">
    <p> <?php echo $datos_denuncia->denuncia_desc;  ?></p>
    <br>
    
<?php foreach($imagenes as $imagen){   ?>
    <img src="<?php 
        echo base_url(); 
        
        switch($datos_denuncia->denuncia_fuente){
            case 'twitter':
                echo TW_IMG_PATH.$imagen->multimedia_file_name;
                break;
            case 'web':
            case 'movil':
                echo LOCAL_IMG_PATH.$imagen->multimedia_file_name;
                break;       
        }
        ?>" style='max-width: 640px;' alt ="<?php echo $datos_denuncia->denuncia_desc; ?>"/>
        
<?php }?>

</div>
</html>
</div>
    