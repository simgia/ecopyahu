<?php 
/**
 * @author jbauer @bauerpy
 * */
?>
<div  style="max-width:700px; margin: 20px auto; padding:20px; background-color:#ffffff">
    <p> <?php echo $datos_denuncia->denuncia_desc;  ?></p>
    <br>
    
<?php foreach($imagenes as $imagen){   ?>
    <img src="<?php echo base_url(); echo TW_IMG_PATH.$imagen->multimedia_file_name;?>"  />
        
<?php }?>

</div>
</html>
</div>
    