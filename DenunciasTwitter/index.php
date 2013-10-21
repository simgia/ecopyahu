<?php

	require_once 'TwitterAPIExchange.php';
	require_once 'FormatFecha.php';
	require_once 'EcoTwitterComplaint.php';

	$complaints = new TwitterComplaint( 'ContaminacionSonora' );

	$datos = $complaints->getData();

	echo "<pre>";
	print_r( $datos );
	echo "</pre>";

?>