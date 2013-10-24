<?php 
$this->load->view('comunes/cabecera');
?>
<!--CUERPO-->
<body>		
    <div id="content-denunciar">		
            
            <?php $this->load->view('comunes/menu')?>

            <h2>Realizar la denuncia</h2>


                    <div id="mapa">
                            <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox=-57.618248462677%2C-25.30999233427183%2C-57.61333465576172%2C-25.30738328763991&amp;layer=mapnik" style="border: 1px solid black"></iframe><br/>
                    </div>




            <br />
            <br />
    </div>
<!--CUERPO-->
<?php $this->load->view('comunes/pie')?>
</body>
</html>