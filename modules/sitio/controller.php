<?php
require_once "modules/sitio/view.php";
require_once "modules/areainteres/model.php";
require_once "modules/provincia/model.php";
require_once "modules/curriculum/model.php";
require_once "modules/tarjetacredito/model.php";
require_once "modules/gestioncomercial/model.php";
require_once "modules/gestioncomercialhistorico/model.php";
require_once "modules/detalletarjetadebito/model.php";
require_once "modules/detalleadhesiondebito/model.php";
require_once "modules/detalleadhesionfacturadigital/model.php";
require_once "modules/detallecambiovencimientojubilado/model.php";
require_once "modules/detallebajavoluntaria/model.php";
require_once "modules/detallenuevosuministroreconexion/model.php";


class SitioController {

	function __construct() {
		$this->view = new SitioView();
	}

    function home() {
    	$select = "b.banner_id AS BANID";
    	$from = "banner b";
    	$where = "b.activo = 1";
    	$banner_collection = CollectorCondition()->get('Banner', $where, 4, $from, $select);

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
    			
		$this->view->home($banner_collection, $mantenimiento_collection);
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

	function trabajaedelar($arg) {
		require_once "core/helpers/recaptcha_lib.php";
		$areainteres_collection = Collector()->get('AreaInteres');
		$provincia_collection = Collector()->get('Provincia');
		$this->view->trabajaedelar($areainteres_collection, $provincia_collection, $arg);
	}

	function guardar_trabajaedelar() {
		require_once "core/helpers/recaptcha_lib.php";
		require_once 'core/helpers/emailHelper.php';

		$recaptcha = filter_input(INPUT_POST, "g-recaptcha-response");
		$secret = "6Lck8w8UAAAAAIBpLWv_HUcU4SHS61WZbaxON3sa";
		$response = null;
		$reCaptcha = new ReCaptcha($secret);
		if ($recaptcha != '') {
		    $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$recaptcha);
		    if ($response != null AND $response->success == true) {

				if($_FILES['archivo']['error'] == 0) {
					$archivo = $_FILES["archivo"]["tmp_name"];
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					$mime = $finfo->file($archivo);
					$formato = explode("/", $mime);
					$mimes_permitidos = array("application/pdf", "application/msword");

					if(in_array($mime, $mimes_permitidos)) {
						$cm = new Curriculum();
						$cm->apellido = filter_input(INPUT_POST, 'apellido');
						$cm->nombre = filter_input(INPUT_POST, 'nombre');
						$cm->localidad = filter_input(INPUT_POST, 'localidad');
					    $cm->direccion = filter_input(INPUT_POST, 'direccion');
					    $cm->correo = filter_input(INPUT_POST, 'correo');
					    $cm->telefono = filter_input(INPUT_POST, 'telefono');
						$cm->estudio = filter_input(INPUT_POST, 'estudio');
						$cm->titulo = filter_input(INPUT_POST, 'titulo');
						$cm->estadocivil = filter_input(INPUT_POST, 'estadocivil');
					    $cm->mensaje = filter_input(INPUT_POST, 'mensaje');
					    $cm->fecha_carga = date('Y-m-d');
					    $cm->areainteres = filter_input(INPUT_POST, 'areainteres');
					    $cm->provincia = filter_input(INPUT_POST, 'provincia');
					    
					    $cm->save();
					    $curriculum_id = $cm->curriculum_id;
						$directorio = URL_PRIVATE . "curriculum/";
						$name = $curriculum_id;
						move_uploaded_file($archivo, "{$directorio}/{$name}");

						$array_dict = array("{denominacion}"=>filter_input(INPUT_POST, "nombre") . ', ' . filter_input(INPUT_POST, "apellido"),
						        			"{localidad}"=>filter_input(INPUT_POST, "localidad"),
									        "{correo}"=>filter_input(INPUT_POST, "correo"),
									        "{telefono}"=>filter_input(INPUT_POST, "telefono"),
						        			"{mensaje}"=>filter_input(INPUT_POST, "mensaje"),
						        			"{url_static}"=>URL_STATIC);

						//$emailHelper = new EmailHelper();
						//$emailHelper->envia_curriculum($array_dict);
						header("Location: " . URL_APP . "/sitio/trabajaedelar/okCorreo");
					} else {
						header("Location: " . URL_APP . "/sitio/trabajaedelar/erFormato");
					}
				} else {
					header("Location: " . URL_APP . "/sitio/trabajaedelar/erArchivo");
				}
	    	} elseif (isset($recaptcha) AND $response->success == false) {
				header("Location: " . URL_APP . "/sitio/trabajaedelar/erCaptcha");
			} else {
				header("Location: " . URL_APP . "/sitio/trabajaedelar/erCaptcha");
			}
		} else {
			header("Location: " . URL_APP . "/sitio/trabajaedelar/erCaptcha");
		}
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

	function contacto($arg) {
		require_once "core/helpers/recaptcha_lib.php";
		$this->view->contacto($arg);
	}

	function enviar_contacto() {
		require_once "core/helpers/recaptcha_lib.php";
		require_once 'core/helpers/emailHelper.php';

		$array_dict = array("{nombre}"=>filter_input(INPUT_POST, "nombre"),
							"{apellido}"=>filter_input(INPUT_POST, "apellido"),
					        "{localidad}"=>filter_input(INPUT_POST, "localidad"),
					        "{direccion}"=>filter_input(INPUT_POST, "direccion"),
					        "{correo}"=>filter_input(INPUT_POST, "correo"),
					        "{telefono}"=>filter_input(INPUT_POST, "telefono"),
					        "{mensaje}"=>filter_input(INPUT_POST, "mensaje"),
					        "{url_static}"=>URL_STATIC);

		$recaptcha = filter_input(INPUT_POST, "g-recaptcha-response");
		$secret = "6Lck8w8UAAAAAIBpLWv_HUcU4SHS61WZbaxON3sa";
		$response = null;
		$reCaptcha = new ReCaptcha($secret);
		if ($recaptcha != '') {
		    $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$recaptcha);
		    if ($response != null AND $response->success == true) {
				//$emailHelper = new EmailHelper();
				//$emailHelper->envia_contacto($array_dict);
				header("Location: " . URL_APP . "/sitio/contacto/okCorreo");
		    }
		} elseif (isset($recaptcha) AND $response->success == false) {
			header("Location: " . URL_APP . "/sitio/contacto/erCaptcha");
		} else {
			header("Location: " . URL_APP . "/sitio/contacto/erCaptcha");
		}
	}
	/* MENU = CENTRO DE AYUDA **********************************************/

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

		$select = "mp.motivo AS motivo, CONCAT('El ', date_format(mp.fecha_inicio, '%d.%m.%Y'), ', Desde ', SUBSTRING(mp.hora_inicio, 1, 5), ' Hasta las ', SUBSTRING(mp.hora_fin, 1, 5)) AS fecha, DATEDIFF(mp.fecha_inicio, CURDATE()) AS dias_restantes, IF(mp.fecha_inicio = CURDATE() AND mp.hora_fin > CURTIME() AND mp.hora_inicio < CURTIME(), 'EN EJECUCIÓN', 'PENDIENTE') AS estado, CASE WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) = 0 THEN 'danger' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) <= 3 THEN 'warning' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) >= 5 AND DATEDIFF(mp.fecha_inicio, CURDATE()) <= 10 THEN 'success' WHEN DATEDIFF(mp.fecha_inicio, CURDATE()) > 10 THEN 'info' END AS class, mu.sector AS sector, mu.calles AS calles, mu.mantenimientoubicacion_id AS manubid, date_format(mp.fecha_inicio, '%d.%m.%Y') AS fecfor, SUBSTRING(mp.hora_inicio, 1, 5) AS horini, mu.latitud, mu.longitud, mu.zoom";
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

		$select = "cmp.coordenadamantenimientopreventivo_id AS ID, cmp.latitud AS LATITUD, cmp.longitud AS LONGITUD, cmp.altitud AS ALTITUD, cmp.etiqueta AS ETIQUETA, cmp.fillcolor AS FILLCOLOR, cmp.strokecolor AS STROKECOLOR, cmp.indice AS INDICE, cmp.mantenimientopreventivo_id AS MANPREID";
		$from = "coordenadamantenimientopreventivo cmp";
		$where = "cmp.mantenimientopreventivo_id = {$mantenimientopreventivo_id}";
		$coordenadamantenimientopreventivo_collection = CollectorCondition()->get('CoordenadaMantenimientoPreventivo', $where, 4, $from, $select);

		if(is_array($coordenadamantenimientopreventivo_collection)){
			$tmp_array = array();
			foreach ($coordenadamantenimientopreventivo_collection as $clave=>$valor) {
				$etiqueta = $valor["ETIQUETA"];
		   		if(!in_array($etiqueta, $tmp_array)) $tmp_array[] = $etiqueta;
			}

			$coordenadas = array();
			foreach ($tmp_array as $claves=>$valores) {
				$array_coordenada = array();
				foreach ($coordenadamantenimientopreventivo_collection as $clave=>$valor) {
					if ($valor["ETIQUETA"] == $valores) {
						$array_temp = array("latitud"=>$valor["LATITUD"],
											"longitud"=>$valor["LONGITUD"],
											"etiqueta"=>$valor["ETIQUETA"],
											"filcolor"=>$valor["FILLCOLOR"],
											"strockcolor"=>$valor["STROKECOLOR"],
											"indice"=>$valor["INDICE"],
											"mantenimientopreventivo_id"=>$valor["MANPREID"]);
						$array_coordenada[] = $array_temp;
					}
				}

				$coordenadas[] = $array_coordenada;
			}
		} else {
			$coordenadas = array();
		}

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

    	$this->view->ver_mantenimiento($mantenimiento_collection, $coordenadas, $obj_mantenimiento);
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

	/* GESTIONES COMERCIALES ***********************************************/
	function tramites_hogares_comercios() {
		$this->view->tramites_hogares_comercios();
	}

	function adhesion_debito($arg) {
		$tarjetacredito_collection = Collector()->get('TarjetaCredito');
		$this->view->adhesion_debito($tarjetacredito_collection, $arg);
	}

	function adhesion_facturadigital($arg) {
		$this->view->adhesion_facturadigital($arg);
	}

	function cambio_vencimiento_jubilados($arg) {
		$this->view->cambio_vencimiento_jubilados($arg);
	}

	function baja_voluntaria($arg) {
		$this->view->baja_voluntaria($arg);
	}

	function nnss_reconexion_propietario($arg) {
		$this->view->nnss_reconexion_propietario($arg);
	}

	function nnss_reconexion_inquilino($arg) {
		$this->view->nnss_reconexion_inquilino($arg);
	}

	function ver_frm_nuevosuministroreconexion_inquilino($arg) {
		$ids = explode('@', $arg);
		$tipo_persona = $ids[0];
		$tipo_gestion = $ids[1];
		$concat = $tipo_persona . $tipo_gestion;
		switch ($concat) {
			case 11:
				$html = 'ver_frm_nuevosuministro_inquilino_fisico';
				break;
			case 12:
				$html = 'ver_frm_reconexion_inquilino_fisico';
				break;
			case 21:
				$html = 'ver_frm_nuevosuministro_inquilino_juridico';
				break;
			case 22:
				$html = 'ver_frm_reconexion_inquilino_juridico';
				break;
		}

		$this->view->ver_frm_nuevosuministroreconexion_inquilino($html);
	}

	function nnss_reconexion_precario($arg) {
		$this->view->nnss_reconexion_precario($arg);
	}

	function guardar_tramite() {
		$array_gestionescomerciales_online = array(1, 3, 4, 5, 6, 7);
		$nombre = filter_input(INPUT_POST, 'nombre');
		$apellido = filter_input(INPUT_POST, 'apellido');
		$nis = filter_input(INPUT_POST, 'nis');
		$telefono = filter_input(INPUT_POST, 'telefono');
		$dni = filter_input(INPUT_POST, 'dni');
		$correo = filter_input(INPUT_POST, 'correo');
		$tipo_gestion = filter_input(INPUT_POST, 'tipo_gestion');

		$gcm = New GestionComercial();
		$gcm->suministro = $nis;
		$gcm->fecha = date('Y-m-d');
		$gcm->dni = $dni;
		$gcm->apellido = $apellido;
		$gcm->nombre = $nombre;
		$gcm->correoelectronico = $correo;
		$gcm->telefono = $telefono;
		$gcm->tipogestioncomercial = $tipo_gestion;
		$gcm->save();
		$gcm->get();
		$gestioncomercial_id = $gcm->gestioncomercial_id;

		$gchm = New GestionComercialHistorico();
		$gchm->fecha = date('Y-m-d');
		$gchm->hora = date('h:i:s');
		$gchm->estadogestioncomercial = 1;
		$gchm->save();
		$gcm->add_gestioncomercialhistorico($gchm);

		$gchgcm = New GestionComercialHistoricoGestionComercial($gcm);
		$gchgcm->save();

		switch ($tipo_gestion) {
			case 1:
				$tmp_dgcm = New DetalleAdhesionFacturaDigital();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				$tmp_dgcm->termino_condiciones = filter_input(INPUT_POST, 'terminos_condiciones');
				$tmp_dgcm->fecha_termino_condiciones = date('Y-m-d h:i:s');
				$tmp_dgcm->ip = $_SERVER['REMOTE_ADDR'];
				$tmp_dgcm->so = $_SERVER['HTTP_USER_AGENT'];
				$tmp_dgcm->tipo = filter_input(INPUT_POST, 'tipo');
				$tmp_dgcm->detalle = 'Gestión comercial online: Adhesión a la Factura Digital.';
				$tmp_dgcm->gestioncomercial = $gestioncomercial_id;
				$tmp_dgcm->save();
				$tmp_dgcm->get();
				$detalleadhesionfacturadigital_id = $tmp_dgcm->detalleadhesionfacturadigital_id;

				$tmp_dgcm = new DetalleAdhesionFacturaDigital();
				$tmp_dgcm->detalleadhesionfacturadigital_id = $detalleadhesionfacturadigital_id;
				$tmp_dgcm->get();

				$url = 'adhesion_facturadigital';
				break;
			case 3:
				$tmp_dgcm = New DetalleNuevoSuministroReconexion();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				$tmp_dgcm->nis = $nis;
				$tmp_dgcm->termino_condiciones = filter_input(INPUT_POST, 'terminos_condiciones');
				$tmp_dgcm->fecha_termino_condiciones = date('Y-m-d h:i:s');
				$tmp_dgcm->ip =	$_SERVER['REMOTE_ADDR'];
				$tmp_dgcm->so = $_SERVER['HTTP_USER_AGENT'];
				$tmp_dgcm->tipo = filter_input(INPUT_POST, 'tipo');
				$tmp_dgcm->tipo_titularidad = filter_input(INPUT_POST, 'tipo_titularidad');
				$tmp_dgcm->tipo_persona = filter_input(INPUT_POST, 'tipo_persona');
				$tmp_dgcm->tiempo_baja = filter_input(INPUT_POST, 'tiempo_baja');
				$tmp_dgcm->detalle = 'Gestión comercial online: Nuevo Suministro - Reconexión.';
				$tmp_dgcm->gestioncomercial = $gestioncomercial_id;
				$tmp_dgcm->save();
				$tmp_dgcm->get();
				$detallenuevosuministroreconexion_id = $tmp_dgcm->detallenuevosuministroreconexion_id;

				$tmp_dgcm = new DetalleNuevoSuministroReconexion();
				$tmp_dgcm->detallenuevosuministroreconexion_id = $detallenuevosuministroreconexion_id;
				$tmp_dgcm->get();
				
				$url = filter_input(INPUT_POST, 'url');
	      		break;
			case 5:
				$tmp_dgcm = New DetalleBajaVoluntaria();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				$tmp_dgcm->termino_condiciones = filter_input(INPUT_POST, 'terminos_condiciones');
				$tmp_dgcm->fecha_termino_condiciones = date('Y-m-d h:i:s');
				$tmp_dgcm->ip =	$_SERVER['REMOTE_ADDR'];
				$tmp_dgcm->so = $_SERVER['HTTP_USER_AGENT'];
				$tmp_dgcm->tipo_propietario = filter_input(INPUT_POST, 'tipo_propietario');
				$tmp_dgcm->detalle = 'Gestión comercial online: Baja Voluntaria.';
				$tmp_dgcm->gestioncomercial = $gestioncomercial_id;
				$tmp_dgcm->save();
				$tmp_dgcm->get();
				$detallebajavoluntaria_id = $tmp_dgcm->detallebajavoluntaria_id;

				$tmp_dgcm = new DetalleBajaVoluntaria();
				$tmp_dgcm->detallebajavoluntaria_id = $detallebajavoluntaria_id;
				$tmp_dgcm->get();

				$url = 'baja_voluntaria';
				break;
			case 6:
				$tmp_dgcm = New DetalleCambioVencimientoJubilado();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				$tmp_dgcm->dia_vencimiento = filter_input(INPUT_POST, 'fecha');
				$tmp_dgcm->termino_condiciones = filter_input(INPUT_POST, 'terminos_condiciones');
				$tmp_dgcm->fecha_termino_condiciones = date('Y-m-d h:i:s');
				$tmp_dgcm->ip = $_SERVER['REMOTE_ADDR'];
				$tmp_dgcm->so = $_SERVER['HTTP_USER_AGENT'];
				$tmp_dgcm->detalle = 'Gestión comercial online: cambio Vencimiento de Jubilados.';
				$tmp_dgcm->gestioncomercial = $gestioncomercial_id;
				$tmp_dgcm->save();
				$tmp_dgcm->get();
				$detallecambiovencimientojubilado_id = $tmp_dgcm->detallecambiovencimientojubilado_id;

				$tmp_dgcm = new DetalleCambioVencimientoJubilado();
				$tmp_dgcm->detallecambiovencimientojubilado_id = $detallecambiovencimientojubilado_id;
				$tmp_dgcm->get();

				$url = 'cambio_vencimiento_jubilados';
				break;
			case 7:
				$dtdm = New DetalleTarjetaDebito();
				$dtdm->institucion_financiera =  filter_input(INPUT_POST, 'db_institucion_financiera');
				$dtdm->titular =  filter_input(INPUT_POST, 'titular');
				$dtdm->cbu =  filter_input(INPUT_POST, 'db_cbu');
				$dtdm->numero_tarjeta = filter_input(INPUT_POST, 'dt_numero_tarjeta');
				$fecha_vencimiento = filter_input(INPUT_POST, 'dt_vencimiento_tarjeta');
				$fecha_vencimiento = (is_null($fecha_vencimiento)) ? date('Y-m-d') : $fecha_vencimiento . "-01";
				$dtdm->fecha_vencimiento =  $fecha_vencimiento;
				$dtdm->tarjetacredito =  filter_input(INPUT_POST, 'dt_tarjetacredito');
				$dtdm->save();
				$dtdm->get();
				$detalletarjetadebito_id = $dtdm->detalletarjetadebito_id;

				$tmp_dgcm = New DetalleAdhesionDebito();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				$tmp_dgcm->metodo_envio = 1;
				$tmp_dgcm->termino_condiciones = filter_input(INPUT_POST, 'terminos_condiciones');
				$tmp_dgcm->fecha_termino_condiciones = date('Y-m-d h:i:s');
				$tmp_dgcm->ip = $_SERVER['REMOTE_ADDR'];
				$tmp_dgcm->so = $_SERVER['HTTP_USER_AGENT'];
				$tmp_dgcm->detalle = 'Gestión comercial online: Adhesión Débito Automático. Débito Bancario.';
				$tmp_dgcm->gestioncomercial = $gestioncomercial_id;
				$tmp_dgcm->detalletarjetadebito = $detalletarjetadebito_id;
				$tmp_dgcm->save();
				$tmp_dgcm->get();
				$detalleadhesiondebito_id = $tmp_dgcm->detalleadhesiondebito_id;

				$tmp_dgcm = new DetalleAdhesionDebito();
				$tmp_dgcm->detalleadhesiondebito_id = $detalleadhesiondebito_id;
				$tmp_dgcm->get();

				$url = 'adhesion_debito';
				break;
			
			default:
				# code...
				break;
		}


		if (!empty($_FILES['archivo'])) {
			$directorio = URL_PRIVATE . "gestioncomercial/{$gestioncomercial_id}";
			$array_archivos = $_FILES['archivo']["tmp_name"];
			$tmp_array = array();
			foreach ($array_archivos as $key=>$archivo) {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$mime = $finfo->file($archivo);
				$formato = explode("/", $mime);
				$mimes_permitidos = array("image/jpg", "image/jpeg", "application/pdf");
			  	$name = $gestioncomercial_id . date("Ymd") . rand();
  				$nombre_archivo = 'archivo_tramite-'.$key.'-'.$gestioncomercial_id;

				if (in_array($mime, $mimes_permitidos)) {
					if(!file_exists($directorio)) {
						mkdir($directorio);
						chmod($directorio, 0777);
						move_uploaded_file($archivo, "{$directorio}/{$name}");
					} else {
						move_uploaded_file($archivo, "{$directorio}/{$name}");
					}

					$am = new Archivo();
					$am->denominacion = $nombre_archivo;
					$am->url = $name;
					$am->fecha_carga = date('Y-m-d');
					$am->formato = $formato[1];
					$am->save();
					$archivo_id = $am->archivo_id;

					$am = new Archivo();
					$am->archivo_id = $archivo_id;
					$am->get();
					$gcm->add_archivo($am);
					$agcm = new ArchivoGestionComercial($gcm);
					$agcm->save();

					if(file_exists("{$directorio}/{$name}")) {
						$archivos = array();
						$im = file_get_contents("{$directorio}/{$name}");
						$data = base64_encode($im);

						$archivos['denominacion'] = $nombre_archivo;
						$archivos['archivo'] = $data;
						$archivos['fecha_carga'] = date('Y-m-d');
						$archivos['formato'] = $formato[1];

						array_push($tmp_array, $archivos);
					}
				}
			}

  	  		$tmp_dgcm->gestioncomercial->archivos_collection = $tmp_array;
	 	}

	 	$argumento = json_encode($tmp_dgcm);
		if (in_array($tipo_gestion, $array_gestionescomerciales_online)) {
	 		//$resultado = sincroniza_geco_tramite($argumento);	 		
	 	} else {
		 	//$resultado = sincroniza_geco_tramite_desa($argumento);
	 	}

	 	header("Location: " . URL_APP . "/sitio/{$url}/okTramite");
	}
	/* GESTIONES COMERCIALES ***********************************************/

	/* COMMON **************************************************************/
	function ver_archivo(){
		SessionHandler()->check_session();
		require_once "core/helpers/files.php";
	}
	/* COMMON **************************************************************/
}
?>
