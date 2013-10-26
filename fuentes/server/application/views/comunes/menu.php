<!--HEADER-->
<header>
    <div id="logo">
        <img src="<?php echo  base_url() ?>images/logo.png" alt="">
    </div>
    <nav>
        <ul>
            <li>
                <?php if($origen!="ayuda")
                    echo '<a class="btn" href="ayuda/como">&#191;C&oacute;mo denunciar?</a>';
                else
                    echo '<a class="btn" href="/">Ver denuncias</a>';
                ?>
            </li>
            <li>
                <a class="btn" href="<?php echo base_url()?>descargas/ecoPYahu.apk" title="Descargue la aplicaci&oacute;n para Android"><i class="fa fa-android fa-lg"></i>&nbsp;</a>
            </li>
            <li>
                <a class="btn" href="#" title="Descargue la aplicaci&oacute;n para iOS (En desarrollo...)"><i class="fa fa-apple fa-lg"></i>&nbsp;</a>
            </li>
            <li>
                <a class="btn" href="#" title="Descargue la aplicaci&oacute;n para WindowsPhone (En desarrollo...)"><i class="fa fa-windows fa-lg"></i>&nbsp;</a>
            </li>
        <ul>
    </nav>
</header>
<!--HEADER-->