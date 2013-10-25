<?php 
/**
 * @author josego
 * */
$p_textos = $this->data["datos_denuncia"];    // Se acorta el nombre
$p_imagenes = $this->data["imagenes"];
?>
<body id="pag_login" class='color_body'>
<div id="cuerpo" class="wrap clearfix">
    <div id="cont_tabs" class="clearfix">
        <div class="gris2 clearfix">
            <h3 class="tit1">Detalle de Denuncia: <b><?php echo $this->data["denuncia_id"];?></b></h3>
            <?php $this->load->view('denuncias/denuncias_detalle_contenido', array("p_textos" => $p_textos, "p_nombre_imagenes" => $p_imagenes, "vista_detalle" => true));?>
        </div>
	</div>
</div>
</body>
</html>