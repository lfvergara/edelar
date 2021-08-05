<?php
require_once "modules/sitio/view.php";


class SitioController {

	function __construct() {
		$this->view = new SitioView();
	}

    function home() {
    	$select = "mp.mantenimientopreventivo_id AS MANPREID, CONCAT('<b>(', mp.numero_eucop, ')</b> ', mp.motivo) AS MOTIVO, CONCAT('El ', mp.fecha_inicio, ', Desde ', SUBSTRING(mp.hora_inicio, 1, 5), ' Hasta las ', SUBSTRING(mp.hora_fin, 1, 5)) AS FECHA, DATEDIFF(mp.fecha_inicio, CURDATE()) AS DIAS_RESTANTES, IF(mp.fecha_inicio = CURDATE() AND mp.hora_fin > CURTIME() AND mp.hora_inicio < CURTIME(), 'EN EJECUCIÓN', 'PENDIENTE') AS ESTADO, CASE WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) = 0 THEN 'danger' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) <= 3 THEN 'warning' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) >= 5 AND DATEDIFF(mp.fecha_inicio, CURDATE()) <= 10 THEN 'success' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) > 10 THEN 'info' END AS MANTENIMIENTO_CLASS, mu.sector AS SECTOR, mu.calles AS CALLES, mu.mantenimientoubicacion_id AS MANUBID, date_format(mp.fecha_inicio, '%d.%m.%Y') AS FECFOR, SUBSTRING(mp.hora_inicio, 1, 5) AS HORINI";
    	$from = "mantenimientopreventivo mp INNER JOIN mantenimientoubicacion mu ON mp.mantenimientoubicacion = mu.mantenimientoubicacion_id";
    	$where = "mp.fecha_inicio > CURDATE() OR (mp.fecha_inicio = CURDATE() AND mp.hora_fin >= CURTIME()) ORDER BY DIAS_RESTANTES ASC, mp.hora_inicio ASC";
    	$mantenimiento_collection = CollectorCondition()->get('MantenimientoPreventivo', $where, 4, $from, $select);

    	if (is_array($mantenimiento_collection) AND !empty($mantenimiento_collection)) {
    		foreach ($mantenimiento_collection as $clave=>$valor) {
    			$mantenimientoubicacion_id = $valor['MANUBID'];
    			$select = "d.denominacion";
    			$from = "departamento d INNER JOIN departamentomantenimientoubicacion dmu ON d.departamento_id = dmu.compositor";
    			$where = "dmu.compuesto = {$mantenimientoubicacion_id}";
    			$departamento_collection = CollectorCondition()->get('Departamento', $where, 4, $from, $select);
    			$tmp_array = array();
    			foreach ($departamento_collection as $departamento) $tmp_array[] = $departamento['denominacion'];
    			$departamentos = implode(' - ', $tmp_array);
    			$mantenimiento_collection[$clave]['DEPARTAMENTOS'] = $departamentos;
    		}
    	}
    			
        $this->view->home($mantenimiento_collection);
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

		$gui = $this->view->imprimir_factura_ajax($obj_deuda, $factura_id, $suministro);
		$mipdf = new DOMPDF();
        $mipdf->set_paper("A4", "portrait");
        $mipdf->load_html($gui);
        $mipdf->render();
        $mipdf->output();
        $mipdf->stream('CuponPagoEDELAR.pdf');
        exit;
	}
	/* MENU = DEUDA ********************************************************/

	/* MENU = MANTENIMIENTOS PREVENTIVOS ***********************************/
	function ver_mantenimiento($arg) {
		$mantenimientopreventivo_id = $arg;

		$select = "mp.motivo AS motivo, CONCAT('El ', date_format(mp.fecha_inicio, '%d.%m.%Y'), ', Desde ', SUBSTRING(mp.hora_inicio, 1, 5), ' Hasta las ', SUBSTRING(mp.hora_fin, 1, 5)) AS fecha, DATEDIFF(mp.fecha_inicio, CURDATE()) AS dias_restantes, IF(mp.fecha_inicio = CURDATE() AND mp.hora_fin > CURTIME() AND mp.hora_inicio < CURTIME(), 'EN EJECUCIÓN', 'PENDIENTE') AS estado, CASE WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) = 0 THEN 'danger' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) <= 3 THEN 'warning' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) >= 5 AND DATEDIFF(mp.fecha_inicio, CURDATE()) <= 10 THEN 'success' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) > 10 THEN 'info' END AS class, mu.sector AS sector, mu.calles AS calles, mu.mantenimientoubicacion_id AS manubid, date_format(mp.fecha_inicio, '%d.%m.%Y') AS fecfor, SUBSTRING(mp.hora_inicio, 1, 5) AS horini";
    	$from = "mantenimientopreventivo mp INNER JOIN mantenimientoubicacion mu ON mp.mantenimientoubicacion = mu.mantenimientoubicacion_id";
    	$where = "mp.mantenimientopreventivo_id = {$mantenimientopreventivo_id}";
    	$mantenimiento_collection = CollectorCondition()->get('MantenimientoPreventivo', $where, 4, $from, $select);
    	$obj_mantenimiento = $mantenimiento_collection[0];

		$mantenimientoubicacion_id = $obj_mantenimiento['manubid'];
		$select = "d.denominacion";
		$from = "departamento d INNER JOIN departamentomantenimientoubicacion dmu ON d.departamento_id = dmu.compositor";
		$where = "dmu.compuesto = {$mantenimientoubicacion_id}";
		$departamento_collection = CollectorCondition()->get('Departamento', $where, 4, $from, $select);
		$tmp_array = array();
		foreach ($departamento_collection as $departamento) $tmp_array[] = $departamento['denominacion'];
		$departamentos = implode(' - ', $tmp_array);
		$obj_mantenimiento['departamentos'] = $departamentos;
    	
    	$select = "mp.mantenimientopreventivo_id AS MANPREID, CONCAT('<b>(', mp.numero_eucop, ')</b> ', mp.motivo) AS MOTIVO, CONCAT('El ', mp.fecha_inicio, ', Desde ', SUBSTRING(mp.hora_inicio, 1, 5), ' Hasta las ', SUBSTRING(mp.hora_fin, 1, 5)) AS FECHA, DATEDIFF(mp.fecha_inicio, CURDATE()) AS DIAS_RESTANTES, IF(mp.fecha_inicio = CURDATE() AND mp.hora_fin > CURTIME() AND mp.hora_inicio < CURTIME(), 'EN EJECUCIÓN', 'PENDIENTE') AS ESTADO, CASE WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) = 0 THEN 'danger' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) <= 3 THEN 'warning' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) >= 5 AND DATEDIFF(mp.fecha_inicio, CURDATE()) <= 10 THEN 'success' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) > 10 THEN 'info' END AS MANTENIMIENTO_CLASS, mu.sector AS SECTOR, mu.calles AS CALLES, mu.mantenimientoubicacion_id AS MANUBID, date_format(mp.fecha_inicio, '%d.%m.%Y') AS FECFOR, SUBSTRING(mp.hora_inicio, 1, 5) AS HORINI";
    	$from = "mantenimientopreventivo mp INNER JOIN mantenimientoubicacion mu ON mp.mantenimientoubicacion = mu.mantenimientoubicacion_id";
    	$where = "mp.fecha_inicio > CURDATE() OR (mp.fecha_inicio = CURDATE() AND mp.hora_fin >= CURTIME()) ORDER BY DIAS_RESTANTES ASC, mp.hora_inicio ASC";
    	$mantenimiento_collection = CollectorCondition()->get('MantenimientoPreventivo', $where, 4, $from, $select);

    	if (is_array($mantenimiento_collection) AND !empty($mantenimiento_collection)) {
    		foreach ($mantenimiento_collection as $clave=>$valor) {
    			$mantenimientoubicacion_id = $valor['MANUBID'];
    			$select = "d.denominacion";
    			$from = "departamento d INNER JOIN departamentomantenimientoubicacion dmu ON d.departamento_id = dmu.compositor";
    			$where = "dmu.compuesto = {$mantenimientoubicacion_id}";
    			$departamento_collection = CollectorCondition()->get('Departamento', $where, 4, $from, $select);
    			$tmp_array = array();
    			foreach ($departamento_collection as $departamento) $tmp_array[] = $departamento['denominacion'];
    			$departamentos = implode(' - ', $tmp_array);
    			$mantenimiento_collection[$clave]['DEPARTAMENTOS'] = $departamentos;
    		}
    	}

    	$this->view->ver_mantenimiento($mantenimiento_collection, $obj_mantenimiento);
	}
	/* MENU = MANTENIMIENTOS PREVENTIVOS ***********************************/

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
