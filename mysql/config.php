<?php

ini_set('session.gc_maxlifetime', 3600);

session_set_cookie_params(3600);

//set_time_limit(2800);

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

date_default_timezone_set('America/Sao_Paulo');


/**
 * 	Define Ambiente da Aplicação
 */
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/**
 * @ Váriaveis  globais
 */
define('PATH_APP', dirname( __FILE__ ));

$domain = ( $_SERVER['HTTP_HOST'] == 'localhost' ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_HOST'] . '/bladderlife' );
define('HOME_URI', (APPLICATION_ENV == 'production' ? 'http://' . $domain . '/home' : 'http://' . $domain ));

/**
 * @ Conexão com Banco de Dados
 *
 */
define('HOSTNAME', 	(APPLICATION_ENV == 'production' ? '12.201.166.55' : '12.201.166.56') );
define('DB_SERVICE', 	(APPLICATION_ENV == 'production' ? 'ORA' : 'ORAT') );

define('BLF_USER', 		'root');
define('BLF_PASSWORD', 	(APPLICATION_ENV == 'production' ? 'password' : 'password-test'));

define('DB_CHARSET', 	'UTF8');
define('DB_PORT', 		'portnumber');

/**
 * Dados para efetuar Log In SAP
 */
define('SAP_USER', '');
define('SAP_PWD',  '');

define('DEBUG', (APPLICATION_ENV == 'production' ? false : true));

/**
 * @ Inicializa as depedências da aplicação
 */
require_once 'DAO.php';


?>