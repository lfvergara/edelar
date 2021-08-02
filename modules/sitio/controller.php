<?php
require_once "modules/sitio/view.php";


//LIBS ENCRIPT
require_once "common/libs/sha256encript/SHA256Encript.php";
require_once "common/libs/sha256encript/AESEncrypter.php";

class SitioController {

	function __construct() {
		$this->view = new SitioView();
	}

    function home() {
        $this->view->home();
	}

	/* MENU = INSTITUCIONAL ************************************************/
	function nosotros() {
		$this->view->nosotros();
	}
	
	function oficinascomerciales() {
		$this->view->oficinascomerciales();
	}

	function rse() {
		$select = "rse.rse_id AS ID, rse.denominacion AS TITULO, rse.epigrafe AS EPIGRAFE, (SELECT a.url FROM archivo a INNER JOIN archivorse ar ON a.archivo_id = ar.compositor WHERE ar.compuesto = rse.rse_id LIMIT 1) AS URL";
		$from = "rse rse";
		$from = "rse.activo = 1 LIMIT 3";
		$rse_collection = CollectorCondition()->get('RSE', NULL, 4, $from, $select);
		
		$this->view->rse($rse_collection);
	}

	function ver_rse($arg) {
		$rse_id = $arg;
		$select = "rse.rse_id AS rse_id, rse.denominacion AS denominacion, rse.epigrafe AS epigrafe, rse.contenido AS contenido, DATE_FORMAT('%d/%m/%Y', rse.fecha) AS fecha, rse.hora AS hora";
		$from = "rse rse";
		$where = "rse.rse_id = {$rse_id}";
		$rse_collection = CollectorCondition()->get('RSE', NULL, 4, $from, $select);
		
		$select = "a.archivo_id AS ID, a.url AS URL";
		$from = "archivo a INNER JOIN archivorse ar ON a.archivo_id = ar.compositor";
		$where = "ar.compuesto = {$rse_id}";
		$archivo_collection = CollectorCondition()->get('Archivo', NULL, 4, $from, $select);

		$select = "v.video_id AS ID, v.url AS URL";
		$from = "video v INNER JOIN videorse vr ON v.video_id = vr.compositor";
		$where = "vr.compuesto = {$rse_id}";
		$video_collection = CollectorCondition()->get('Video', NULL, 4, $from, $select);

		$this->view->ver_rse($rse_collection, $archivo_collection, $video_collection);
	}

	function trabajaedelar() {
		$this->view->trabajaedelar();
	}
	/* MENU = INSTITUCIONAL ************************************************/

	/* MENU = CENTRO DE AYUDA **********************************************/
	function leerfactura() {
		$this->view->leerfactura();
	}

	function preguntasfrecuentes() {
		$this->view->preguntasfrecuentes();
	}

	function seguridad() {
		$this->view->seguridad();
	}

	function usoenergia() {
		$this->view->usoenergia();
	}

	function contacto() {
		$this->view->contacto();
	}
	/* MENU = CENTRO DE AYUDA **********************************************/

	function tramites_hogares_comercios() {
		$this->view->tramites_hogares_comercios();
	}

	/* MENU = DEUDA ********************************************************/
	function ver_deuda() {
		require_once "common/libs/WebService/wsAExterno_get_deuda_desa.php";
		$ws_get_deuda = new wsAgenteExternoGetDeudaHelper();

		$tipo = filter_input(INPUT_POST, 'tipo');
		switch ($tipo) {
			case 1:
				$variable = filter_input(INPUT_POST, 'nis');
				$rst = $ws_get_deuda->suministro($variable);
				break;
			case 2:
				$variable = filter_input(INPUT_POST, 'dni');
				$rst = $ws_get_deuda->documento($variable);
				break;
		}
		print_r($rst);exit;
		$this->view->ver_deuda($variable, $rst, $tipo);
	}
	/* MENU = DEUDA ********************************************************/

	/* PARA PRUEBA DE FORMULARIOS ******************************************/
	function p1_signup_cliente() {
		$this->view->p1_signup_cliente();
	}

	function p2_signup_cliente() {
		if (isset($_SESSION["array_registro"]) AND !empty($_SESSION["array_registro"])) {
			$this->view->p2_signup_cliente();
		} else {
			$this->p1_signup_cliente();
		}
	}

	function p3_signup_cliente() {
		$this->view->p3_signup_cliente();
	}

	function p4_signup_cliente() {
		//FIXME - Desarrollar script de verificación de datos
		$this->view->p3_signup_cliente();
	}
	/* PARA PRUEBA DE FORMULARIOS ******************************************/
}
	/* COMMON **************************************************************/
	function ver_archivo(){
		SessionHandler()->check_session();
		require_once "core/helpers/files.php";
	}
	/* COMMON **************************************************************/
?>
