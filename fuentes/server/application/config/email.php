<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['protocol'] = 'smtp'; //tal vez sendmail
$config['mailpath'] = '/usr/sbin/sendmail';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;
$config['smtp_host'] = 'ssl://smtp.googlemail.com';
$config['smtp_user'] = 'direccion@conacyt.com';
$config['smtp_pass'] = base64_decode('contrasenha encriptada en base64');
$config['smtp_timeout'] = '4';
$config['smtp_port'] = '465';
$config['mailtype'] = 'text';




//$config[''] = '';
