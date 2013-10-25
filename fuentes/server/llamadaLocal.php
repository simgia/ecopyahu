<?php
/**
 * Gestiona las llamadas al servidor para obtener las actualizaciones
 * de las redes sociales.
 * Ubicacion:Deberia estar en alguna carpeta privada. Accesible al crontab
 */
//$url = "http://ecopyahu.simgia.com/denuncias_redes_sociales/insertarDenunciasByTwitter";
  $url = "http://ecopyahu.simgia.com/denuncias_redes_sociales/test";
echo file_get_contents($url);