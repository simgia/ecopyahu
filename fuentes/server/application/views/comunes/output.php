<?php
/**
 * Esta vista se encargara de devolver los valores solicitados a las aplicaciones clientes. Ej json, xml
 * @author Juan Bauer @bauerpy
 */
if(!isset($p_output) || $p_output=="")
    $p_output = array('success'=>true);
echo json_encode($p_output);