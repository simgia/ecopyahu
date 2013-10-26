<?php 
$this->load->view('comunes/cabecera');
?>
<head>
</head>
<body>

    <div id="content-denunciar">
        <?php $this->load->view('comunes/menu',array("origen"=>"ayuda"))?>
            <h2>&#191;C&oacute;mo denunciar?</h2><br><br>
            <h3>Utilizando Twitter <i class="fa fa-twitter"></i></h3>
            <p>Para mayor facilidad ecoPYahu se integra con Twitter, utiliz&aacute;ndolo como medio para realizar las denuncias con el simple uso del hashtag #ecopyahu. El sistema constantemente monitorea la red social en busca de twits, al encontrar uno nuevo con el hastag mencionado lo agrega a la base de datos de ecoPyahu y ya se encuentra publicado. Al mismo tiempo se retwittea en la cuenta @ecopyahu para todos sus seguidores.
            <code> Enviar un tweet con el <i>hashtag</i> <b>#ecopyahu</b> </code>
            </p>

            <h3>Utilizando app ecoPYahu</h3><br><br>
            
        <?php $this->load->view('comunes/pie')?>
    </div>
</body>