<?php
//error_reporting(0);
/**
* SITIO WEB EDELAR SA
*
* FrontCrontoller de la Aplicación.
* Rutea las peticiones del cliente teniendo en cuenta la estructura
* /MODULO/RECURSO/ARGUMENTO
*
* @package    EDELAR WEB
* @version    2.0b
**/
header('Content-Type: text/html; charset=utf8');
require_once 'settings.php';
require_once 'core/database.php';
require_once 'core/collector.php';
require_once 'core/collector_condition.php';
require_once 'core/view.php';
require_once 'core/standardobject.php';
require_once 'core/sessions.php';
require_once 'core/sessions.cliente.php';
require_once 'core/helpers/files.php';
require_once 'core/helpers/appfiles.php';
require_once "core/helpers/configuracionmenu.php";
require_once 'core/helpers/GoogChart.class.php';
require_once 'core/helpers/emailHelper.php';
require_once "tools/excelreport.php";


$peticion = $_SERVER['REQUEST_URI'];
if (SO_UNIX == true) {
        @list($app, $modulo, $recurso, $argumento) = explode('/', $peticion);
} else {
        @list($null, $app, $modulo, $recurso, $argumento) = explode('/', $peticion);
}

if(empty($modulo)) { $modulo = DEFAULT_MODULE; }
if(empty($recurso)) { $recurso = DEFAULT_ACTION; }
if(!file_exists("modules/{$modulo}/controller.php")) {
    $modulo = DEFAULT_MODULE;
}

$archivo = "modules/{$modulo}/controller.php";
require_once $archivo;
$controller_name = ucwords($modulo) . 'Controller';
$controller = new $controller_name;

$recurso = (method_exists($controller, $recurso)) ? $recurso : DEFAULT_ACTION;
$controller->$recurso($argumento);
?>