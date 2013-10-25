<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/**
 * Variable que guarda el nombre del estado logueado
 * @var string
 */
define('LOGGED', 'logged');
/**
 * Estado activo
 * @var string
*/
define('E_ACTIVO','activo');
/* End of file constants.php */
/**
 * Nombre de la variable de vida de session
 * @var string
*/
define('SESSION_LIVE','session_live');

define('SYSTEM_NAME','Ecopyahu');
define('SYSTEM_SHORT_NAME','Ecopyahu');

define('EMAIL_ADDRESS','ecopyhu@simgia.com');
define('EMAIL_ADDRES_NAME','Ecopyahu');

/*
 * Constantes para multimedias.
 */
define('TW_IMG_PATH','media/imagen/twitter/');
define('TW_VIDEO_PATH','media/video/twitter/');
define('LOCAL_IMG_PATH','media/imagen/local/');



/* End of file constants.php */
/* Location: ./application/config/constants.php */