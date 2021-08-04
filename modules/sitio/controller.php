<?php
require_once "modules/sitio/view.php";


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
		$where = "rse.activo = 1 LIMIT 3";
		$rse_collection = CollectorCondition()->get('RSE', $where, 4, $from, $select);
		
		$this->view->rse($rse_collection);
	}

	function ver_rse($arg) {
		$rse_id = $arg;
		$select = "rse.rse_id AS rse_id, rse.denominacion AS denominacion, rse.epigrafe AS epigrafe, rse.contenido AS contenido, DATE_FORMAT(rse.fecha, '%d/%m/%Y') AS fecha, rse.hora AS hora";
		$from = "rse rse";
		$where = "rse.rse_id = {$rse_id}";
		$rse_collection = CollectorCondition()->get('RSE', $where, 4, $from, $select);
		
		$select = "a.archivo_id AS ID, a.url AS URL, a.denominacion AS DENOMINACION";
		$from = "archivo a INNER JOIN archivorse ar ON a.archivo_id = ar.compositor";
		$where = "ar.compuesto = {$rse_id}";
		$archivo_collection = CollectorCondition()->get('Archivo', $where, 4, $from, $select);

		$select = "v.video_id AS ID, v.url AS URL, v.denominacion AS DENOMINACION";
		$from = "video v INNER JOIN videorse vr ON v.video_id = vr.compositor";
		$where = "vr.compuesto = {$rse_id}";
		$video_collection = CollectorCondition()->get('Video', $where, 4, $from, $select);

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
		require_once "tools/getDeuda.php";
		$metodo = filter_input(INPUT_POST, 'metodo');
		$valor = filter_input(INPUT_POST, 'valor');
		
		$deuda_collection = getDeuda()->getDeuda($metodo, $valor);
		$this->view->ver_deuda($deuda_collection, $metodo);
	}

	function consultar_factura_ajax($arg) {
		require_once "tools/getDeuda.php";
		$ids = explode("@", $arg);
		$suministro = $ids[0];
		$factura_id = $ids[1];
		
		$deuda_collection = getDeuda()->getDeuda('nis', $suministro);
		$obj_deuda = null;
		$deuda_collection = json_decode($deuda_collection);
		$deuda_collection = $deuda_collection[0];
		foreach ($deuda_collection as $clave=>$valor) {
			$tmp_factura_id = $valor->id_factura;
			$deuda_collection[$clave]->nis = $valor->suministro->id;
			if ($tmp_factura_id == $factura_id) $obj_deuda = $deuda_collection[$clave];
		}

		$this->view->consultar_factura_ajax($obj_deuda, $factura_id, $suministro);
	}

	function imprimir_factura($arg) {
		require_once "tools/getDeuda.php";
		require_once 'common/libs/domPDF/dompdf_config.inc.php';
	    $ids = explode('@', $arg);
		$suministro_id = $ids[0];
		$factura_id = $ids[1];

		$deuda_collection = getDeuda()->getDeuda('nis', $suministro);
		$obj_deuda = null;
		$deuda_collection = json_decode($deuda_collection);
		$deuda_collection = $deuda_collection[0];
		foreach ($deuda_collection as $clave=>$valor) {
			$tmp_factura_id = $valor->id_factura;
			$deuda_collection[$clave]->nis = $valor->suministro->id;
			if ($tmp_factura_id == $factura_id) $obj_deuda = $deuda_collection[$clave];
		}

		$gui = $this->view->imprimir_factura_ajax($obj_deuda, $factura_id, $suministro_id);
		$mipdf = new DOMPDF();
        $mipdf->set_paper("A4", "portrait");
        $mipdf->load_html($gui);
        $mipdf->render();
        $mipdf->output();
        $mipdf->stream('CuponPagoEDELAR.pdf');
        exit;
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
