<?php 
$this->load->view('comunes/cabecera');
?>
<head>
</head>
<body>

    <div id="content-denunciar">
        <?php $this->load->view('comunes/menu',array("origen"=>"ayuda"))?>
            <h2>&#191;C&oacute;mo denunciar?</h2><br><br>
            <p><iframe src="http://prezi.com/embed/ehrelc6xrhaf/?bgcolor=ffffff&amp;lock_to_path=0&amp;autoplay=0&amp;autohide_ctrls=0&amp;features=undefined&amp;disabled_features=undefined" width="100%" height="400" frameBorder="0" style="width: 100%"></iframe>
	    </p><br>
            <h3>Utilizando Twitter <i class="fa fa-twitter"></i></h3>
            <p>Para mayor facilidad ecoPYahu se integra con Twitter, utiliz&aacute;ndolo como medio para realizar las denuncias con el simple uso del hashtag #ecopyahu; para poder ubicar el lugar de la denuncia, se recomienda activar la ubicaci&oacute;n antes de postear el tweet. El sistema constantemente monitorea la red social en busca de tweets, al encontrar uno nuevo con el hastag mencionado lo agrega a la base de datos de ecoPYahu y ya se encuentra publicado. Al mismo tiempo se retwittea en la cuenta @ecopyahu para todos sus seguidores.
            <code> Enviar un tweet con el <i>hashtag</i> <b>#ecopyahu</b> </code>
            </p>

            <h3>Utilizando app ecoPYahu</h3>
            <p>Si lo desea puede bajar la app para su plataforma espec&iacute;fica con los enlaces situados m&aacute;s arriba, en el men&uacute; principal. La app ecoPYahu permite tomar fotograf&iacute;as obtener la informaci&oacute;n geogr&aacute;fica desde su dispositivo m&oacute;vil, ingresar datos adicionales y enviar la denuncia. La misma se registrar&aacute; en el servidor y se generar&aacute; un tweet con la misma informaci&oacute;n en la cuenta de Twitter @ecopyahu.
            
            </p>
            
        <?php $this->load->view('comunes/pie')?>
    </div>
</body>
