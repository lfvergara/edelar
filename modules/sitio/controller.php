<?php
use Dompdf\Dompdf;
require_once "modules/sitio/view.php";
require_once "modules/areainteres/model.php";
require_once "modules/provincia/model.php";
require_once "modules/unicom/model.php";
require_once "modules/curriculum/model.php";
require_once "modules/tarjetacredito/model.php";
require_once "modules/tramite/model.php";
require_once "modules/gestioncomercial/model.php";
require_once "modules/gestioncomercialhistorico/model.php";
require_once "modules/detalletarjetadebito/model.php";
require_once "modules/detalleadhesiondebito/model.php";
require_once "modules/detalleadhesionfacturadigital/model.php";
require_once "modules/detallecambiovencimientojubilado/model.php";
require_once "modules/detallebajavoluntaria/model.php";
require_once "modules/detallenuevosuministroreconexion/model.php";
require_once "modules/oficina/model.php";
require_once "modules/turnopendiente/model.php";
require_once "modules/configuracionturnero/model.php";
require_once "modules/rangoturnero/model.php";
require_once "modules/configuracionturnerodni/model.php";


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
		require_once "tools/getDatosV10.php";
		$metodo = filter_input(INPUT_POST, 'metodo');
		$valor = filter_input(INPUT_POST, 'valor');
		
		$deuda = new getDatosV10();
		$deuda_collection = $deuda->getDeudaFunction($metodo, $valor);
		$this->view->ver_deuda($deuda_collection, $metodo);
	}

	function consultar_factura_ajax($arg) {
		require_once "tools/getDatosV10.php";
		$ids = explode("@", $arg);
		$suministro = $ids[0];
		$factura_id = $ids[1];
		
		$deuda = new getDatosV10();
		$deuda_collection = $deuda->getDeudaFunction('nis', $suministro);
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
		require_once 'common/libs/ndompdf/autoload.inc.php';
		require_once "tools/getDatosV10.php";
		$ids = explode('@', $arg);
		$suministro = $ids[0];
		$factura_id = $ids[1];
		
		$deuda = new getDatosV10();
		$deuda_collection = $deuda->getDeudaFunction('nis', $suministro);
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

	function info() {
		print phpinfo();
	}

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
	function turnero() {
		$unicom_collection = Collector()->get('Unicom');
		$tramite_collection = Collector()->get('Tramite');
		$this->view->turnero($unicom_collection, $tramite_collection);
	}

	function tramites_grandes_clientes() {
		$this->view->tramites_grandes_clientes();
	}

	function tramites_hogares_comercios() {
		$this->view->tramites_hogares_comercios();
	}

	function grandes_clientes() {
		$this->view->grandes_clientes();
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

		eval("class OV_GestionHistorico {};");
		$gestionhistorico = New OV_GestionHistorico();
		$gestionhistorico->fecha = date('Y-m-d');
		$gestionhistorico->hora = date('h:i:s');
		$gestionhistorico->ov_estadogestion = 1;

		

		switch ($tipo_gestion) {
			case 1:
				$tmp_dgcm = New DetalleAdhesionFacturaDigital();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				//$tmp_dgcm->termino_condiciones = filter_input(INPUT_POST, 'terminos_condiciones');
				$tmp_dgcm->termino_condiciones = 0;
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

				// PARA WS
				eval("class OV_TipoGestion {};");
				$ovtipogestion = new OV_TipoGestion();
				$ovtipogestion->ov_tipogestion_id = 1;
				$ovtipogestion->denominacion = "Adhesión Factura Digital";
				$ovtipogestion->cantidadarchivo = 2;
				$ovtipogestion->codigo = "AFD";

				eval("class OV_Gestion {};");
				$gestion = New OV_Gestion();
				$gestion->suministro = $nis;
				$gestion->fecha = date('Y-m-d');
				$gestion->dni = $dni;
				$gestion->nombre = $nombre;
				$gestion->apellido = $apellido;
				$gestion->correoelectronico = $correo;
				$gestion->telefono = $telefono;
				$gestion->ov_tipogestion = $ovtipogestion;
				$gestion->ov_gestionhistorico_collection = array();
				$gestion->ov_gestionhistorico_collection[] = $gestionhistorico;
				$gestion->archivos_collection = array();

				eval("class OV_DetalleGestionAdhesion {};");
				$tipogestion = new OV_DetalleGestionAdhesion();
				$tipogestion->numero_tramite = $gestioncomercial_id;
				$tipogestion->termino_condiciones = 0;
				$tipogestion->fecha_termino_condiciones = date('Y-m-d');
				$tipogestion->ip = $_SERVER['REMOTE_ADDR'];
				$tipogestion->so = '';
				$tipogestion->tipo = filter_input(INPUT_POST, 'tipo');
				$tipogestion->detalle = 'Gestión comercial online: Adhesión a la Factura Digital.';
				$tipogestion->ov_gestion = $gestion;
				// PARA WS

				$url = 'adhesion_facturadigital';
				break;
			case 3:
				$tmp_dgcm = New DetalleNuevoSuministroReconexion();
				$tmp_dgcm->numero_tramite = $gestioncomercial_id;
				$tmp_dgcm->nis = $nis;
				$tmp_dgcm->termino_condiciones = 0;
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
				
				// PARA WS
				eval("class OV_TipoGestion {};");
				$ovtipogestion = new OV_TipoGestion();
				$ovtipogestion->ov_tipogestion_id = 3;
				$ovtipogestion->denominacion = "Nuevo Suministro - Reconexion";
				$ovtipogestion->cantidadarchivo = 6;
				$ovtipogestion->codigo = "NUS";

				eval("class OV_Gestion {};");
				$gestion = New OV_Gestion();
				$gestion->ov_gestion_id = 0;
				$gestion->suministro = $nis;
				$gestion->fecha = date('Y-m-d');
				$gestion->dni = $dni;
				$gestion->nombre = $nombre;
				$gestion->apellido = $apellido;
				$gestion->correoelectronico = $correo;
				$gestion->telefono = $telefono;
				$gestion->ov_tipogestion = $ovtipogestion;
				$gestion->ov_gestionhistorico_collection = array();
				$gestion->ov_gestionhistorico_collection[] = $gestionhistorico;
				$gestion->archivos_collection = array();

				eval("class OV_DetalleGestionNuevoSuministro {};");
				$tipogestion = New OV_DetalleGestionNuevoSuministro();
				$tipogestion->numero_tramite = $gestioncomercial_id;
				$tipogestion->nis_vecino = $nis;
				$tipogestion->termino_condiciones = 0;
				$tipogestion->fecha_termino_condiciones = date('Y-m-d');
				$tipogestion->ip = $_SERVER['REMOTE_ADDR'];				
				$tipogestion->so = '';
				$tipogestion->tipo = filter_input(INPUT_POST, 'tipo');
				$tipogestion->tipo_titularidad = filter_input(INPUT_POST, 'tipo_titularidad');
				$tipogestion->tipo_persona = 'FÍSICA'; //FIX ME FÍSICA / JURÍDICA
				$tipogestion->detalle = 'Gestión comercial online: Nuevo Suministro - Reconexión.'; //FIX ME FÍSICA / JURÍDICA
				$tipogestion->ov_gestion = $gestion;
				// PARA WS

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

				// PARA WS
				eval("class OV_TipoGestion {};");
				$ovtipogestion = new OV_TipoGestion();
				$ovtipogestion->ov_tipogestion_id = 5;
				$ovtipogestion->denominacion = "Baja Voluntaria";
				$ovtipogestion->cantidadarchivo = 4;
				$ovtipogestion->codigo = "ABV";

				eval("class OV_Gestion {};");
				$gestion = New OV_Gestion();
				$gestion->suministro = $nis;
				$gestion->fecha = date('Y-m-d');
				$gestion->dni = $dni;
				$gestion->nombre = $nombre;
				$gestion->apellido = $apellido;
				$gestion->correoelectronico = $correo;
				$gestion->telefono = $telefono;
				$gestion->ov_tipogestion = $ovtipogestion;
				$gestion->ov_gestionhistorico_collection = array();
				$gestion->ov_gestionhistorico_collection[] = $gestionhistorico;
				$gestion->archivos_collection = array();

				eval("class OV_DetalleBajaVoluntaria {};");
				$tipogestion = New OV_DetalleBajaVoluntaria();
				$tipogestion->numero_tramite = $gestioncomercial_id;
				$tipogestion->termino_condiciones = 0;
				$tipogestion->fecha_termino_condiciones = date('Y-m-d');
				$tipogestion->ip = $_SERVER['REMOTE_ADDR'];
				$tipogestion->so = '';
				$tipogestion->tipo_propietario = filter_input(INPUT_POST, 'tipo_propietario');
				$tipogestion->detalle = 'Gestión comercial online: Baja Voluntaria.';
				$tipogestion->ov_gestion = $gestion;
				// PARA WS

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

				// PARA WS
				eval("class OV_TipoGestion {};");
				$ovtipogestion = new OV_TipoGestion();
				$ovtipogestion->ov_tipogestion_id = 6;
				$ovtipogestion->denominacion = "Cambio Vencimiento Jubilados";
				$ovtipogestion->cantidadarchivo = 4;
				$ovtipogestion->codigo = "CVJ";

				eval("class OV_Gestion {};");
				$gestion = New OV_Gestion();
				$gestion->ov_gestion_id = 0;
				$gestion->suministro = $nis;
				$gestion->fecha = date('Y-m-d');
				$gestion->dni = $dni;
				$gestion->nombre = $nombre;
				$gestion->apellido = $apellido;
				$gestion->correoelectronico = $correo;
				$gestion->telefono = $telefono;
				$gestion->ov_tipogestion = $ovtipogestion;
				$gestion->ov_gestionhistorico_collection = array();
				$gestion->ov_gestionhistorico_collection[] = $gestionhistorico;
				$gestion->archivos_collection = array();

				eval("class OV_DetalleGestionCambioVencimientoJubilado {};");
				$tipogestion = New OV_DetalleGestionCambioVencimientoJubilado();
				$tipogestion->numero_tramite = $gestioncomercial_id;
				$tipogestion->dia_vencimiento = filter_input(INPUT_POST, 'fecha');
				$tipogestion->termino_condiciones = 0;
				$tipogestion->fecha_termino_condiciones = date('Y-m-d');
				$tipogestion->ip = $_SERVER['REMOTE_ADDR'];
				$tipogestion->so = '';
				$tipogestion->detalle = 'Gestión comercial online: Cambio Vencimiento Jubilados.';
				$tipogestion->ov_gestion = $gestion;
				// PARA WS

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

				// PARA WS
				eval("class OV_TipoGestion {};");
				$ovtipogestion = new OV_TipoGestion();
				$ovtipogestion->ov_tipogestion_id = 7;
				$ovtipogestion->denominacion = "Adhesion Debito Automatico";
				$ovtipogestion->cantidadarchivo = 4;
				$ovtipogestion->codigo = "ADA";

				eval("class OV_Gestion {};");
				$gestion = New OV_Gestion();
				$gestion->suministro = $nis;
				$gestion->fecha = date('Y-m-d');
				$gestion->dni = $dni;
				$gestion->nombre = $nombre;
				$gestion->apellido = $apellido;
				$gestion->correoelectronico = $correo;
				$gestion->telefono = $telefono;
				$gestion->ov_tipogestion = $ovtipogestion;
				$gestion->ov_gestionhistorico_collection = array();
				$gestion->ov_gestionhistorico_collection[] = $gestionhistorico;
				$gestion->archivos_collection = array();

				$dt_tarjetacredito = filter_input(INPUT_POST, 'dt_tarjetacredito');
				$dt_tarjetacredito = (is_null($dt_tarjetacredito) OR empty($dt_tarjetacredito) OR $dt_tarjetacredito == '') ? 0 : $dt_tarjetacredito;
				
				$tcm = new TarjetaCredito();
				$tcm->tarjetacredito_id = filter_input(INPUT_POST, 'dt_tarjetacredito');
				$tcm->get();

				eval("class OV_Tarjeta {};");
				$tarjetacredito = new OV_Tarjeta();
				$tarjetacredito->ov_tarjeta_id = $dt_tarjetacredito;
				$tarjetacredito->denominacion = $tcm->denominacion;
				
				eval("class OV_DetalleTarjetaDebito {};");
				$tarjeta = New OV_DetalleTarjetaDebito();
				$tarjeta->ov_detalletarjetadebito_id =  0;
				$tarjeta->denominacion =  filter_input(INPUT_POST, 'db_institucion_financiera');
				$tarjeta->denominacion_titular =  filter_input(INPUT_POST, 'titular');
				$tarjeta->num_cbu =  filter_input(INPUT_POST, 'cbu');
				$tarjeta->num_tarjeta = filter_input(INPUT_POST, 'dt_numero_tarjeta');
				$fecha_vencimiento = filter_input(INPUT_POST, 'dt_vencimiento_tarjeta');
				$fecha_vencimiento = (is_null($fecha_vencimiento)) ? date('Y-m-d') : $fecha_vencimiento . "-01";
				$tarjeta->fecha_vencimiento =  $fecha_vencimiento;
				$tarjeta->ov_tarjeta =  $tarjetacredito;

				eval("class OV_DetalleAdhesionDebito {};");
				$tipogestion = New OV_DetalleAdhesionDebito();
				$tipogestion->numero_tramite = $gestioncomercial_id;
				$tipogestion->metodo_envio = 0;
				$tipogestion->termino_condiciones = 0;
				$tipogestion->fecha_termino_condiciones = date('Y-m-d');
				$tipogestion->ip = $_SERVER['REMOTE_ADDR'];
				$tipogestion->so = '';
				$tipogestion->detalle = 'Gestión comercial online: Adhesión Débito Automático. Débito Bancario.';
				$tipogestion->ov_gestion = $gestion;
				$tipogestion->ov_detalletarjetadebito = $tarjeta;

				//$ovdgam->numero_tramite = $gestioncomercial_id;
				//$ovdgam->termino_condiciones = filter_input(INPUT_POST, 'termino_condiciones');
				//$ovdgam->fecha_termino_condiciones = date('Y-m-d h:i:s');
				//$ovdgam->ip = $_SERVER['REMOTE_ADDR'];
				//$ovdgam->so = $_SERVER['HTTP_USER_AGENT'];
				//$ovdgam->ov_gestion = $gestion;
				//$ovdgam->ov_detalletarjetadebito = $tarjeta;

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
  	  		$tipogestion->ov_gestion->archivos_collection = $tmp_array;
	 	}

	 	$argumento = json_encode($tipogestion);
	 	//print_r($argumento);exit;
	 	if (in_array($tipo_gestion, $array_gestionescomerciales_online)) {
	 		//$resultado = sincroniza_geco_tramite($argumento);	 		
		 	//$resultado = sincroniza_geco_tramite_desa($argumento);
		 	require_once "tools/postGestionGeCo.php";

			$ws = new postGestionGeCo();
			$rst_cliente = $ws->postGestionFunction($argumento);

		 	header("Location: " . URL_APP . "/sitio/{$url}/okTramite");
	 	} else {
		 	header("Location: " . URL_APP . "/sitio/{$url}/errorTramite");
	 	}

	}
	/* GESTIONES COMERCIALES ***********************************************/

	

	/* TURNERO**************************************************************/
	function verificar_dni($arg){
		$fecha = date('Y-m-d');
		$documento = $arg;
		$select = "tp.turnopendiente_id AS ID, tp.documento AS DOCUMENTO, tp.numero AS NUMERO, tp.fecha_hasta AS FECHA_HASTA, tp.hora_solicitud AS HORA_SOLICITUD, tp.estado AS ESTADO, of.denominacion AS OFICINA, t.denominacion AS GESTION, (SELECT COUNT(*) FROM turnopendiente WHERE documento = '{$documento}' AND fecha_hasta >= '{$fecha}' AND estado = 'solicitado') AS CANTIDAD";
		$from = "turnopendiente tp INNER JOIN oficina of ON tp.oficina = of.oficina_id INNER JOIN tramite t ON tp.tramite = t.tramite_id";
		$where = "tp.documento = '{$documento}' AND tp.fecha_hasta >= '{$fecha}' AND tp.estado = 'solicitado'";
		$turnopendiente_collection = CollectorCondition()->get('TurnoPendiente', $where, 4, $from, $select);

		if (is_array($turnopendiente_collection)) {
			$this->view->turnos_documento($turnopendiente_collection);
		} else {
			$turnopendiente_collection = 0;
			print_r($turnopendiente_collection);
		}
	}

	function gestion_requisitos($arg) {
		$tm = new Tramite();
		$tm->tramite_id = $arg;
		$tm->get();
		$this->view->gestion_requisitos($tm);
	}

	function horas_disponibles($arg){
		$var = explode('@',$arg);
		$unicom_id = $var[1];
		$terminacion = substr($var[0], -1);

		$select = "ctd.dia AS dia";
		$from = "configuracionturnerodni ctd";
		$where = "ctd.terminacion LIKE '%{$terminacion}%'";
		$configuracionturnerodni_collection = CollectorCondition()->get('ConfiguracionTurneroDni', $where, 4,$from, $select);

		$select = "rt.fecha_desde AS FECHA_DESDE, rt.fecha_hasta AS FECHA_HASTA";
		$from = "rangoturnero rt";
		$where = "rt.estado = 1";
		$rangoturnero_collection = CollectorCondition()->get('RangoTurnero', $where, 4,$from, $select);

		if (is_array($rangoturnero_collection)) {
			$array_dia = array();
			foreach ($rangoturnero_collection as $key => $value) {
				$fecha_desde = $value["FECHA_DESDE"];
				$fecha_hasta = $value["FECHA_HASTA"];
				$fechaaamostar = $fecha_desde;
				$array = array();
				$bandera = 0;
				while(strtotime($fecha_hasta) >= strtotime($fecha_desde)) {
					if(strtotime($fecha_hasta) != strtotime($fechaaamostar)) {
						$array[$bandera]['fecha'] = $fechaaamostar;
						$array[$bandera]['dia'] = date("N", strtotime($fechaaamostar));
						$fechaaamostar = date("Y-m-d", strtotime($fechaaamostar . " + 1 day"));
						$bandera++;
					} else {
						$array[$bandera]['fecha'] = $fechaaamostar;
						$array[$bandera]['dia'] = date("N", strtotime($fechaaamostar));
						break;
					}
				}
			 	$array_dia[$key] = $array;
			}

			$temp_array = array();
			foreach($array_dia as $key => $val) {
				if (!in_array($val, $temp_array)) $temp_array[$key] = $val;
			}

			$array= array_reduce($temp_array, 'array_merge', array());

			/*ELIMINA FECHAS DUPLICADAS*/
			$temp_array = array();
	    	$i = 0;
	    	$key_array = array();
   			$key  = 'fecha';
		    foreach($array as $val) {
		        if (!in_array($val[$key], $key_array)) {
		            $key_array[$i] = $val[$key];
		            $temp_array[$i] = $val;
		        }
		        $i++;
		    }
			
			$array = $temp_array;
			/*ELIMINA FECHAS DUPLICADAS*/

			/*ELIMINA TERMINCIONES DE DNI*/
			$dif = array_diff(array_column($array,'dia'), array_column($configuracionturnerodni_collection,'dia'));
			foreach ($dif as $key => $dia_dif) unset($array[$key]);
			/*ELIMINA TERMINCIONES DE DNI*/

			/*ELIMINA DIAS VENCIDOS*/
			foreach ($array as $key => $dia) {
				if(strtotime(date("d-m-Y")) > strtotime($dia["fecha"])) unset($array[$key]);
			}
			/*ELIMINA DIAS VENCIDOS*/

		
			if (empty($array)) {
				print_r(0);
			} else {
				$this->view->horas_disponibles($array);
			}
		} else {
			 print_r(0);
		}
	}

	function dias_disponibles($arg) {
		$var = explode('@',$arg);
		$fecha = $var[0];
		$unicom = $var[1];

		/*BUSCO CONFIGURACION POR UNICOM Y FECHA*/
		$select = "rt.fecha_desde AS FECHA_DESDE, rt.fecha_hasta AS FECHA_HASTA, ct.cantidad_gestores AS CANTIDAD, of.oficina_id AS OFICINA, of.denominacion AS DENOMINACION, of.direccion AS DIRECCION";
		$from = "rangoturnero rt INNER JOIN configuracionturnero ct ON rt.rangoturnero_id = ct.rangoturnero INNER JOIN configuracionturnerooficina cto ON ct.configuracionturnero_id = cto.compositor INNER JOIN oficina of ON cto.compuesto = of.oficina_id";
		$where = "'{$fecha}' BETWEEN rt.fecha_desde AND rt.fecha_hasta AND rt.estado = 1 AND of.unicom = {$unicom} AND of.turnero_online = 1";
		$configuracion_unicom_collection = CollectorCondition()->get('RangoTurnero', $where, 4,$from, $select);
		/*BUSCO CONFIGURACION POR UNICOM Y FECHA*/
		//print_r($unicom);exit;
		if (is_array($configuracion_unicom_collection)) {
			/*BUSCO CONFIGURACION DE HORARIOS OFICINAS*/
			$select = "of.hora_desde AS HORA_DESDE, of.hora_hasta AS HORA_HASTA, of.oficina_id AS OFICINA";
			$from = "oficina of";
			$where = "of.unicom = {$unicom}";
			$configuracion_horario_collection = CollectorCondition()->get('Oficina', $where, 4,$from, $select);
			/*BUSCO CONFIGURACION DE HORARIOS OFICINAS*/

			/*CREANDO SECUENCIA DE HORARIO*/
			foreach($configuracion_unicom_collection as $clave=>$valor){
				if (is_array($configuracion_horario_collection)) {
					$clave = array_search($valor['OFICINA'], array_column($configuracion_horario_collection, 'OFICINA'));
					$configuracion_unicom_collection[$clave]['HORA_DESDE'] = $configuracion_horario_collection[$clave]['HORA_DESDE'];
					$configuracion_unicom_collection[$clave]['HORA_HASTA'] = $configuracion_horario_collection[$clave]['HORA_HASTA'];
				} else {
					$configuracion_unicom_collection[$clave]['HORA_DESDE'] = '07:00:00';
					$configuracion_unicom_collection[$clave]['HORA_HASTA'] = '13:00:00';
				}
			}

			foreach ($configuracion_unicom_collection as $clave=>$valor) {
				$hora_desde	= $valor['HORA_DESDE'];
				$hora_hasta	= $valor['HORA_HASTA'];

				$horaaamostar = $hora_desde;
				$array_hora = array();
				$bandera = 0;
				while(strtotime($hora_hasta) >= strtotime($hora_desde)) {
					if(strtotime($hora_hasta) != strtotime($horaaamostar)) {
						$array_hora[$bandera] = $horaaamostar;
						$horaaamostar = date("H:i:s", strtotime($horaaamostar . " + 30 minute"));
						$bandera++;
					} else {
						$array_hora[$bandera] = $horaaamostar;
						break;
					}
				}
				$configuracion_unicom_collection[$clave]["TURNOS"] = $array_hora;
			}
			/*CREANDO SECUENCIA DE HORARIO*/

			/*TO_TURNOSPENDIENTES*/
			$select = "COUNT(tp.hora_solicitud) AS CANTIDAD, tp.hora_solicitud AS HORA_SOLICITUD, tp.oficina AS OFICINA";
			$from = "turnopendiente tp INNER JOIN oficina of ON tp.oficina = of.oficina_id";
			$where = "of.unicom  = {$unicom} AND tp.fecha_hasta = '{$fecha}' AND tp.estado = 'solicitado'";
			$groupby = "tp.hora_solicitud, tp.oficina";
			$turnopendiente_collection = CollectorCondition()->get('TurnoPendiente', $where, 4,$from, $select, $groupby);
			/*TO_TURNOSPENDIENTES*/

			/*ELIMINO HORARIO NO DISPONIBLE*/
			if (is_array($turnopendiente_collection)) {
				foreach ($turnopendiente_collection as $key => $value) {
					$cantidad = $value['CANTIDAD'];
					$hora_solicitud = $value['HORA_SOLICITUD'];

					$clave = array_search($value['OFICINA'], array_column($configuracion_unicom_collection, 'OFICINA'));
					if ($configuracion_unicom_collection[$clave]['CANTIDAD'] == $cantidad) {
						$key_turno = array_search($hora_solicitud, $configuracion_unicom_collection[$clave]['TURNOS']);
						unset($configuracion_unicom_collection[$clave]['TURNOS'][$key_turno]);
					}
				}
			}
			/*ELIMINO HORARIO NO DISPONIBLE*/

			/*ELIMINO HORARIO VENCIDO*/
			date_default_timezone_set('America/Argentina/La_Rioja');
			$hora_actual = date('H:i:s');
			$dia_actual = date('Y-m-d');
			if ($dia_actual == $fecha) {

				foreach ($configuracion_unicom_collection as $key => $configuracion) {
					foreach ($configuracion['TURNOS'] as $keys => $values) {
						if ($values <= $hora_actual) unset($configuracion['TURNOS'][$keys]);
					}
					$configuracion_unicom_collection[$key]['TURNOS'] = $configuracion['TURNOS'];
				}
			}
			/*ELIMINO HORARIO VENCIDO*/

			/*VALIDAMOS QUE EXISTAN TURNOS*/
			$longitud = sizeof($configuracion_unicom_collection);
			if ($longitud == 1) {
				if (empty($configuracion_unicom_collection[0]['TURNOS'])) {
					$this->view->dias_no_disponibles();
				} else {
					$this->view->dias_disponibles($configuracion_unicom_collection);
				}
			} else {
				foreach ($configuracion_unicom_collection as $key => $value) {
					if (empty($value['TURNOS'])) unset($configuracion_unicom_collection[$key]);
				}

				if (empty($configuracion_unicom_collection)) {
					$this->view->dias_no_disponibles();
				} else {
					$this->view->dias_disponibles($configuracion_unicom_collection);
				}
			}
			/*VALIDAMOS QUE EXISTAN TURNOS*/
		} else {
			$this->view->dias_no_disponibles();
		}
	}

	function guardar_turno() {
		$fecha = filter_input(INPUT_POST, 'fecha_turno');
	  	$gestion_id = filter_input(INPUT_POST, 'gestion');
	  	$documento = filter_input(INPUT_POST, 'documento');
	  	$turno = filter_input(INPUT_POST, 'hora_turno');
	  	$var = explode('@', $turno);
	  	$hora = $var[0];
	  	$oficina_id = $var[1];
		$telefono = filter_input(INPUT_POST, 'telefono');
		$correoelectronico = filter_input(INPUT_POST, 'correoelectronico');
		if (is_null($fecha) OR empty($fecha) OR $fecha == 0) {
			$mensaje ="Seleccione una Fecha Disponible. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		} elseif (is_null($turno) OR empty($turno) OR $turno == 0) {
			$mensaje ="Seleccione un Horario Disponible. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		} elseif (is_null($gestion_id) OR empty($gestion_id) OR $gestion_id == 0) {
			$mensaje ="Seleccione una Gestión Disponible. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		} elseif (is_null($documento) OR empty($documento) OR $documento == 0) {
			$mensaje ="Ingrese un Documento. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		} elseif (is_null($telefono) OR empty($telefono) OR $telefono == 0) {
			$mensaje ="Ingrese un Teléfono. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		} else {
			if (!empty($correoelectronico)) {
				$api_key = "sg2xL6QmK2HMC0dD6e0NObaVN";
				$j = json_decode(file_get_contents("https://api.millionverifier.com/api/v3/?api=$api_key&email=$correoelectronico"));
				switch($j->resultcode) {
					case 1:
					  	$confirmacion = 1;
						break;
					default:
						$mensaje ="Correo Electrónico Inválido. Vuelva a intentarlo. Gracias";
						$this->mensaje_turno($mensaje);
						break;
				}
			} else {
				$confirmacion = 0;
			}

			$select = "ct.cantidad_gestores AS CANTIDAD";
			$from = "rangoturnero rt INNER JOIN configuracionturnero ct ON rt.rangoturnero_id = ct.rangoturnero INNER JOIN configuracionturnerooficina cto ON ct.configuracionturnero_id = cto.compositor INNER JOIN oficina o ON cto.compuesto = o.oficina_id";
			$where = "rt.estado = 1 and cto.compuesto = {$oficina_id}";
			$cantidad_gestores = CollectorCondition()->get('RangoTurnero', $where, 4,$from, $select);
			$cantidad_gestores = (is_array($cantidad_gestores) AND !empty($cantidad_gestores)) ? $cantidad_gestores[0]['CANTIDAD'] : 0;

			$telefono = str_replace(' ', '', $telefono);
			$telefono = preg_replace('/[^0-9,.]+/i', '', $telefono);

			/*GUARDA EN TURNERO*/
			$turno = array('documento' => $documento,
						   'fecha' => $fecha,
						   'hora_solicitud' => $hora,
						   'oficina_id' => $oficina_id,
						   'gestion_id' => $gestion_id,
						   'telefono' => $telefono,
						   'correoelectronico' => $correoelectronico,
						   'turnopendiente_id' => 0,
						   'cantidad_gestores' => $cantidad_gestores);

			$argumento = json_encode($turno);
			$resultado = sincroniza_geco_turno_desa($argumento);
			/*GUARDA EN TURNERO*/

			/*GUARDA EN WEB*/
			$turno = json_decode($resultado);
			switch ($turno) {
				case (is_object($turno)):
					  	/*CREAMOS TOKEN*/
					  	$numero = $turno->numero;
					  	$turnero_id = $turno->turnopendiente_id;
					  	$oficina = $turno->oficina;
						$tipogestioncomercial = $turno->tipogestion;
						$fecha_token = date("Y-m-d H:i:s");

						$tpm = new TurnoPendiente();
						$tpm->numero = $turno->numero;
						$tpm->documento = $documento;
						$tpm->fecha_hasta = $turno->fecha;
						$tpm->hora_solicitud = $turno->hora_solicitud;
						$tpm->telefono = $telefono;
						$tpm->correoelectronico = $correoelectronico;
						$tpm->estado = 'solicitado';
						$tpm->token_fecha = $token_fecha;
						$tpm->token = $token;
						$tpm->turnero_id = $turno->turnopendiente_id;
						$tpm->oficina = $turno->oficina;
						$tpm->tipogestioncomercial = $turno->tipogestion;
						$tpm->save();
						$turnopendiente_id = $tpm->turnopendiente_id;
						$token = "{$turnopendiente_id}-{$turnero_id}-{$numero}-{$oficina}-{$tipogestioncomercial}";
						break;
				case "TURNO_NO_DISPONIBLE":
						$mensaje ="Turno no disponible. Vuelva a intentarlo. Gracias";
						$this->mensaje_turno($mensaje);
						break;
				default:
						$mensaje = "Error al solicitar turno. Vuelva a intentarlo. Gracias";
						$this->mensaje_turno($mensaje);
						break;
			}
			/*GUARDA EN WEB*/

			switch($confirmacion) {
				case 0:
					/*MANDARIA SMS*/
					/* FIX INTEGRAR API PARA EL ENVÍO DE SMS */

					$mensaje = "Se ha enviado un mensaje al teléfono indicado en la solicitud. Por favor confirme el , de lo contrario el mismo será cancelado. Gracias";
					$this->mensaje_turno($mensaje);
					
					break;
				case 1:
					require_once 'core/helpers/emailHelper.php';
					$emailHelper = new EmailHelper();
					$emailHelper->envia_turnoweb_confirmacion($correoelectronico, $token);
					
					$mensaje = "Se ha enviado un mensaje al correo electrónico indicado en la solicitud. Por favor confirme el , de lo contrario el mismo será cancelado. Gracias";
					$this->mensaje_turno($mensaje);
					break;
				default:
					$mensaje = "Error al solicitar turno. Vuelva a intentarlo. Gracias";
					$this->mensaje_turno($mensaje);
					break;
			}

			header("Location: " . URL_APP . $direccion_confirmacion);
		}
	}

	function guardar_turno_editado(){
		$turnopendiente_id = filter_input(INPUT_POST, 'turnopendiente_id');
		$fecha = filter_input(INPUT_POST, 'fecha_turno');
		$gestion_id = filter_input(INPUT_POST, 'gestion');
		$documento = filter_input(INPUT_POST, 'documento');
		$turno = filter_input(INPUT_POST, 'hora_turno');
		$var = explode('@',$turno);
		$hora = $var[0];
		$oficina_id = $var[1];
		$telefono = filter_input(INPUT_POST, 'telefono');
		$correoelectronico = filter_input(INPUT_POST, 'correoelectronico');

		if (is_null($fecha) OR empty($fecha) OR $fecha == 0) {
			$mensaje ="Seleccione una Fecha Disponible. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		}elseif (is_null($turno) OR empty($turno) OR $turno == 0) {
			$mensaje ="Seleccione un Horario Disponible. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		}elseif (is_null($gestion_id) OR empty($gestion_id) OR $gestion_id == 0) {
			$mensaje ="Seleccione una Gestión Disponible. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		}elseif (is_null($documento) OR empty($documento) OR $documento == 0) {
			$mensaje ="Ingrese un Documento. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		}elseif (is_null($telefono) OR empty($telefono) OR $telefono == 0) {
			$mensaje ="Ingrese un Telefono. Vuelva a intentarlo. Gracias";
			$this->mensaje_turno($mensaje);
		} else {
			if (!empty($correoelectronico)) {
				$api_key = "sg2xL6QmK2HMC0dD6e0NObaVN";
				$j = json_decode(file_get_contents("https://api.millionverifier.com/api/v3/?api=$api_key&email=$correoelectronico"));
				switch($j->resultcode) {
					case 1:
					  	$confirmacion = 1;
						break;
					default:
						$mensaje ="Correo Electrónico Inválido. Vuelva a intentarlo. Gracias";
						$this->mensaje_turno($mensaje);
						break;
				}
			} else {
				$confirmacion = 0;
			}


			$select = "ct.cantidad_gestores AS CANTIDAD";
			$from = "rangoturnero rt INNER JOIN configuracionturnero ct ON rt.rangoturnero_id = ct.rangoturnero INNER JOIN configuracionturnerooficina cto ON ct.configuracionturnero_id = cto.compositor INNER JOIN oficina o ON cto.compuesto = o.oficina_id";
			$where = "rt.estado = 1 and cto.compuesto = {$oficina_id}";
			$cantidad_gestores = CollectorCondition()->get('RangoTurnero', $where, 4,$from, $select);
			$cantidad_gestores = (is_array($cantidad_gestores) AND !empty($cantidad_gestores)) ? $cantidad_gestores[0]['CANTIDAD'] : 0;

			$tpm = new TurnoPendiente();
			$tpm->turnopendiente_id = $turnopendiente_id;
			$tpm->get();

			$telefono =str_replace(' ', '', $telefono);
			$telefono = preg_replace('/[^0-9,.]+/i', '', $telefono);

			/*GUARDA EN TURNERO*/
			$turno = array('documento'=>$documento,
						   'fecha'=>$fecha,
						   'hora_solicitud'=>$hora,
						   'oficina_id'=>$oficina_id,
						   'gestion_id'=>$gestion_id,
						   'telefono'=>$telefono,
						   'correoelectronico'=>$correoelectronico,
						   'turnopendiente_id'=>$tpm->turnopendiente_id,
						   'cantidad_gestores'=>$cantidad_gestores);

			$argumento = json_encode($turno);
			$resultado = sincroniza_geco_turno_modificar_desa($argumento);
			/*GUARDA EN TURNERO*/

			/*GUARDA EN WEB*/
			$turno = json_decode($resultado);
			switch ($turno) {
				case (is_object($turno)):

						$tpm->numero = $turno->numero;
						$tpm->fecha_hasta = $turno->fecha;
						$tpm->hora_solicitud = $turno->hora_solicitud;
						$tpm->to_oficina =  $turno->oficina;
						$tpm->to_tipogestion =  $turno->tipogestion;
						$tpm->telefono = $turno->telefono;
						$tpm->correoelectronico = $turno->correoelectronico;
						$tpm->save();

						header("Location: " . URL_APP . "/sitio/comprobante_turno/{$documento}@{$turnopendiente_id}");
						break;
				case "TURNO_NO_DISPONIBLE":
						$mensaje ="Turno no disponible. Vuelva a intentarlo. Gracias";
						$this->mensaje_turno($mensaje);
						break;
				default:
						$mensaje = "Error al solicitar turno. Vuelva a intentarlo. Gracias";
						$this->mensaje_turno($mensaje);
						break;
			}
			/*GUARDA EN WEB*/

			switch($confirmacion) {
				case 0:
					/*MANDARIA SMS*/
					/* FIX INTEGRAR API PARA EL ENVÍO DE SMS */

					$mensaje = "Se ha enviado un mensaje al teléfono indicado en la solicitud. Por favor confirme el , de lo contrario el mismo será cancelado. Gracias";
					$this->mensaje_turno($mensaje);
					
					break;
				case 1:
					require_once 'core/helpers/emailHelper.php';
					$emailHelper = new EmailHelper();
					$emailHelper->envia_turnoweb_confirmacion($correoelectronico, $token);
					
					$mensaje = "Se ha enviado un mensaje al correo electrónico indicado en la solicitud. Por favor confirme el , de lo contrario el mismo será cancelado. Gracias";
					$this->mensaje_turno($mensaje);
					break;
				default:
					$mensaje = "Error al solicitar turno. Vuelva a intentarlo. Gracias";
					$this->mensaje_turno($mensaje);
					break;
			}
		}

		header("Location: " . URL_APP . $direccion_confirmacion);
	}

	function cancelar_turno($arg) {
		$var = explode('@',$arg);
		$documento = $var[0];
		$turnopendiente_id = $var[1];

		if ($turnopendiente_id !=0) {
			$tpm = new TurnoPendiente();
			$tpm->turnopendiente_id = $turnopendiente_id;
			$tpm->get();

			if ($tpm->documento == $documento) {
				/*GUARDA EN TURNERO*/
				$turno = array('fecha'=>$tpm->fecha_hasta,
							   'hora_solicitud'=>$tpm->hora_solicitud,
							   'oficina_id'=>$tpm->oficina,
							   'gestion_id'=>$tpm->tramite,
							   'telefono'=>$tpm->telefono,
							   'correoelectronico'=>$tpm->correoelectronico,
							   'turnopendiente_id'=>$tpm->turnopendiente_id,
							   'cantidad_gestores'=>0);

				$argumento = json_encode($turno);
				$resultado = sincroniza_geco_turno_cancelar_desa($argumento);
				/*GUARDA EN TURNERO*/

				/*GUARDA EN WEB*/
				$resultado = json_decode($resultado);
				switch ($resultado) {
					case ("OK"):
							$tpm->estado = 'cancelado';
							$tpm->save();

							$this->view->cancelar_turno();
	 						break;
					case "TURNO_NO_DISPONIBLE":
							$mensaje ="Turno no disponible. Vuelva a intentarlo. Gracias";
							$this->mensaje_turno($mensaje);
							break;
					default:
							$mensaje = "Error al cancelar turno. Vuelva a intentarlo. Gracias";
							$this->mensaje_turno($mensaje);
							break;
				}
				/*GUARDA EN WEB*/
			} else {
				header("Location: " . URL_APP . "/sitio");
			}
		} else {
			header("Location: " . URL_APP . "/sitio");
		}
	}

	function editar_turno($arg) {
		$var = explode('@',$arg);
		$documento = $var[0];
		$turnopendiente_id = $var[1];

		$tpm = new TurnoPendiente();
		$tpm->turnopendiente_id = $turnopendiente_id;
		$tpm->get();
		$unicom_collection = Collector()->get('Unicom');
		$tramite_collection = Collector()->get('Tramite');

		if ($tpm->documento == $documento) {
			$this->view->editar_turno($tpm, $unicom_collection, $tramite_collection);
		} else {
			header("Location: " . URL_APP . "/sitio");
		}
	}

	function imprimir_turno($arg) {
		$ids = explode('@', $arg);
		$documento = $ids[0];
		$turnopendiente_id = $ids[1];

		$select = "tp.turnopendiente_id AS ID, tp.numero AS NUMERO,	tp.documento AS DOCUMENTO, tp.fecha_hasta AS FECHA, tp.hora_solicitud AS HORA, o.denominacion AS OFICINA, t.denominacion AS GESTION, o.direccion AS DIRECCION, t.tramite_id AS TIPO";
		$from = "turnopendiente tp INNER JOIN oficina o ON tp.oficina = o.oficina_id INNER JOIN tramite t ON tp.tramite = t.tramite_id";
		$where = "tp.documento = {$documento} AND tp.turnopendiente_id = {$turnopendiente_id}";
		$turnopendiente_collection = CollectorCondition()->get('TO_TurnoPendiente', $where, 4,$from, $select);

		$tramite_id = $turnopendiente_collection[0]['TIPO'];

		$tm = new Tramite();
		$tm->tramite_id = $tramite_id;
		$tm->get();

		$gui = $this->view->imprimir_turno($turnopendiente_collection, $tm);
		$mipdf = new DOMPDF();
		$mipdf->set_paper("A4", "portrait");
		$mipdf->load_html($gui);
		$mipdf->render();
		$mipdf->output();
		$mipdf->stream('CuponTurnoEDELAR.pdf');
		exit;
	}

	function horas_disponibles_edit($arg){
		$var = explode('@',$arg);
		$unicom_id = $var[1];
		$terminacion = substr($var[0], -1);
		$dia_solicitud = $var[2];

		$select = "ctd.dia AS dia";
		$from = "configuracionturnerodni ctd";
		$where = "ctd.terminacion LIKE '%{$terminacion}%'";
		$configuracionturnerodni_collection = CollectorCondition()->get('ConfiguracionTurneroDni', $where, 4,$from, $select);

		$select = "rt.fecha_desde AS FECHA_DESDE, rt.fecha_hasta AS FECHA_HASTA";
		$from = "rangoturnero rt";
		$where = "rt.estado = 1";
		$rangoturnero_collection = CollectorCondition()->get('RangoTurnero', $where, 4,$from, $select);

		if (is_array($rangoturnero_collection)) {
			$array_dia = array();
			foreach ($rangoturnero_collection as $key => $value) {
				$fecha_desde = $value["FECHA_DESDE"];
				$fecha_hasta = $value["FECHA_HASTA"];
				$fechaaamostar = $fecha_desde;
				$array = array();
				$bandera = 0;
				while(strtotime($fecha_hasta) >= strtotime($fecha_desde)) {
					if(strtotime($fecha_hasta) != strtotime($fechaaamostar)) {
						$array[$bandera]['fecha'] = $fechaaamostar;
						$array[$bandera]['dia'] = date("N", strtotime($fechaaamostar));
						$fechaaamostar = date("Y-m-d", strtotime($fechaaamostar . " + 1 day"));
						$bandera++;
					} else {
						$array[$bandera]['fecha'] = $fechaaamostar;
						$array[$bandera]['dia'] = date("N", strtotime($fechaaamostar));
						break;
					}
				}
				$array_dia[$key] = $array;
			}

			$temp_array = array();
			foreach($array_dia as $key => $val) {
				if (!in_array($val, $temp_array)) $temp_array[$key] = $val;
			}

			$array= array_reduce($temp_array, 'array_merge', array());

			/*ELIMINA TERMINCIONES DE DNI*/
			$dif = array_diff(array_column($array,'dia'), array_column($to_configuracion_dni_collection,'dia'));
			foreach ($dif as $key => $dia_dif) unset($array[$key]);
			/*ELIMINA TERMINCIONES DE DNI*/

			/*ELIMINA DIAS VENCIDOS*/
			foreach ($array as $key => $dia) {
				if(strtotime(date("d-m-Y")) > strtotime($dia["fecha"])) unset($array[$key]);
			}
			/*ELIMINA DIAS VENCIDOS*/

			if (empty($array)) {
				print_r(0);
			} else {
				$this->view->horas_disponibles_edit($array,$dia_solicitud);
			}
		} else {
			 print_r(0);
		}
	}

	function dias_disponibles_edit($arg){
		$var = explode('@',$arg);
		$fecha = $var[0];
		$unicom = $var[1];
		$horario = $var[2];
		$oficina = $var[3];
		$turnopendiente_id = $var[4];

		$tpm = new TurnoPendiente();
		$tpm->turnopendiente_id = $turnopendiente_id;
		$tpm->get();
		/*BUSCO CONFIGURACION POR UNICOM Y FECHA*/
		$select = "rt.fecha_desde AS FECHA_DESDE, rt.fecha_hasta AS FECHA_HASTA, ct.cantidad_gestores AS CANTIDAD, of.oficina_id AS OFICINA, of.denominacion AS DENOMINACION, of.direccion AS DIRECCION";
		$from = "rangoturnero rt INNER JOIN configuracionturnero ct ON rt.rangoturnero_id = ct.rangoturnero INNER JOIN configuracionturnerooficina cto ON ct.configuracionturnero_id = cto.compositor INNER JOIN oficina of ON cto.compuesto = of.oficina_id";
		$where = "'{$fecha}' BETWEEN rt.fecha_desde AND rt.fecha_hasta AND rt.estado = 1 AND of.unicom = {$unicom} AND of.turnero_online = 1";
		$configuracion_unicom_collection = CollectorCondition()->get('RangoTurnero', $where, 4,$from, $select);
		/*BUSCO CONFIGURACION POR UNICOM Y FECHA*/

		if (is_array($configuracion_unicom_collection)) {
			/*BUSCO CONFIGURACION DE HORARIOS OFICINAS*/
			$select = "of.hora_desde AS HORA_DESDE, of.hora_hasta AS HORA_HASTA, of.oficina_id AS OFICINA";
			$from = "oficina of";
			$where = "of.unicom = {$unicom}";
	 		$configuracion_horario_collection = CollectorCondition()->get('Oficina', $where, 4,$from, $select);
			/*BUSCO CONFIGURACION DE HORARIOS OFICINAS*/

			/*CREANDO SECUENCIA DE HORARIO*/
			foreach($configuracion_unicom_collection as $key => $value){
				if (is_array($configuracion_horario_collection)) {
					$clave = array_search($value['OFICINA'], array_column($configuracion_horario_collection, 'OFICINA'));
					$configuracion_unicom_collection[$key]['HORA_DESDE'] = $configuracion_horario_collection[$clave]['HORA_DESDE'];
					$configuracion_unicom_collection[$key]['HORA_HASTA'] = $configuracion_horario_collection[$clave]['HORA_HASTA'];
				}else {
					$configuracion_unicom_collection[$key]['HORA_DESDE'] = '07:00:00';
					$configuracion_unicom_collection[$key]['HORA_HASTA'] = '13:00:00';
				}
			}

			foreach ($configuracion_unicom_collection as $key => $value) {
				$hora_desde	= $value['HORA_DESDE'];
				$hora_hasta	= $value['HORA_HASTA'];

				$horaaamostar = $hora_desde;
				$array_hora = array();
				$bandera = 0;
				while(strtotime($hora_hasta) >= strtotime($hora_desde))
				{
					if(strtotime($hora_hasta) != strtotime($horaaamostar))
					{
						$array_hora[$bandera] = $horaaamostar;
						$horaaamostar = date("H:i:s", strtotime($horaaamostar . " + 30 minute"));
						$bandera++;
					} else {
						$array_hora[$bandera] = $horaaamostar;break;
					}
				}
				$configuracion_unicom_collection[$key]["TURNOS"] = $array_hora;
			}
			/*CREANDO SECUENCIA DE HORARIO*/

			/*TO_TURNOSPENDIENTES*/
			$select = "COUNT(tp.hora_solicitud) AS CANTIDAD, tp.hora_solicitud AS HORA_SOLICITUD, tp.oficina AS OFICINA";
			$from = "turnopendiente tp INNER JOIN oficina of ON tp.oficina = of.oficina_id";
			$where = "of.unicom  = {$unicom} AND tp.fecha_hasta = '{$fecha}' AND tp.estado = 'solicitado'";
			$groupby = "tp.hora_solicitud, tp.oficina";
			$turnopendiente_collection = CollectorCondition()->get('TurnoPendiente', $where, 4,$from, $select, $groupby);
			/*TO_TURNOSPENDIENTES*/

			/*ELIMINO HORARIO NO DISPONIBLE*/
			if (is_array($turnopendiente_collection)) {
				foreach ($turnopendiente_collection as $key => $value) {
					$cantidad = $value['CANTIDAD'];
					$hora_solicitud = $value['HORA_SOLICITUD'];

					$clave = array_search($value['OFICINA'], array_column($configuracion_unicom_collection, 'OFICINA'));
					if ($configuracion_unicom_collection[$clave]['CANTIDAD'] == $cantidad) {
						$key_turno = array_search($hora_solicitud, $configuracion_unicom_collection[$clave]['TURNOS']);
						unset($configuracion_unicom_collection[$clave]['TURNOS'][$key_turno]);
					}
				}
 			}
			/*ELIMINO HORARIO NO DISPONIBLE*/

			/*ELIMINO HORARIO VENCIDO*/
			date_default_timezone_set('America/Argentina/La_Rioja');
			$hora_actual = date('H:i:s');
			$dia_actual = date('Y-m-d');
			if ($dia_actual == $fecha) {

				foreach ($configuracion_unicom_collection as $key => $configuracion) {
					foreach ($configuracion['TURNOS'] as $keys => $values) {
						if ($values <= $hora_actual) {
							unset($configuracion['TURNOS'][$keys]);
						}
					}
					$configuracion_unicom_collection[$key]['TURNOS'] = $configuracion['TURNOS'];
				}
			}
			/*ELIMINO HORARIO VENCIDO*/

			/*VALIDAMOS QUE EXISTAN TURNOS*/
			$longitud = sizeof($configuracion_unicom_collection);
			if ($longitud == 1) {
				if (empty($configuracion_unicom_collection[0]['TURNOS'])) {
					$this->view->dias_no_disponibles();
				}else {
					$this->view->dias_disponibles_edit($configuracion_unicom_collection,$horario,$oficina,$fecha,$tpm->fecha_hasta);
				}
			} else {
				foreach ($configuracion_unicom_collection as $key => $value) {
					if (empty($value['TURNOS'])) {
						 unset($configuracion_unicom_collection[$key]);
					}
				}

				if (empty($configuracion_unicom_collection)) {
					$this->view->dias_no_disponibles();
				}else {
					$this->view->dias_disponibles_edit($configuracion_unicom_collection,$horario,$oficina,$fecha,$tpm->fecha_hasta);
				}
			}
			/*VALIDAMOS QUE EXISTAN TURNOS*/
 		} else {
			$this->view->dias_no_disponibles();
 		}
	}

	function mensaje_turno($arg){
		$this->view->mensaje_turno($arg);
	}
	/* TURNERO**************************************************************/
	
	/* OFICINA VIRTUAL******************************************************/
	function ofivirtual() {
		require_once "tools/getDatosV10.php";
		
		//SIN DEUDA
		$documento = 12393896;
		//CON DEUDA
		$documento = 12393897;

		$ws = new getDatosV10();
		$rst_cliente = $ws->getClienteFunction('dni', $documento);

		//FIX ME: COMPLETAR CON WS TRAER CLIENTE Y SUMINISTROS
		/*
		$ws = new getDatosV10();
		$rst_suministros = $ws->getSuministrosFunction('dni', $documento);
		*/

		//$this->view->ofivirtual($rst_cliente, $rst_suministros);
		$this->view->ofivirtual($rst_cliente);		
	}
	/* OFICINA VIRTUAL******************************************************/

	/* OFICINA VIRTUAL: VER SUMINISTRO**************************************/
	function ofivirtual_suministro() {
		require_once "tools/getDatosV10.php";
		require_once "core/helpers/facturaHelper.php";
		//SIN DEUDA
		$suministro = 5171545;
		//CON DEUDA
		$suministro = 5126854;

		//CON DEUDA
		$documento = 12393897;

		$metodo = 'nis';
		$valor = $suministro;		
		
		$ws = new getDatosV10();
		$rst_deuda = $ws->getDeudaFunction($metodo, $valor);

		$ws = new getDatosV10();
		$rst_cliente = $ws->getClienteFunction('dni', $documento);

		//FIX ME: COMPLETAR CON WS TRAER SUMINISTRO
		/*
		$ws = new getDatosV10();
		$suministro = $ws->getSuministroFunction('nis', $suministro);
		*/
		$suministro_direccion = '';
		$suministro_facturadigital = 'ADHERIDO';
		$suministro_flag_facturadigital = 'ADHERIDO';
		$obj_suministro = array('{suministro-nis}'=>$suministro, 
								'{suministro-direccion}'=>$suministro_direccion, 
								'{suministro-facturadigital}'=>$suministro_facturadigital,
								'{suministro-flag_facturadigital}'=>$suministro_flag_facturadigital);

		$impreso_collection=readService("http://provider:123456@200.91.37.167:9190/FacturaProvider/query?nis={$suministro}");
		$this->view->ofivirtual_suministro($obj_suministro, $rst_cliente, $rst_deuda, $metodo, $impreso_collection);
	}

	function ofivirtual_deuda() {
		require_once "tools/getDatosV10.php";
		//SIN DEUDA
		$documento = 12393896;
		//CON DEUDA
		$documento = 12393897;
		
		$ws = new getDatosV10();
		$rst_cliente = $ws->getClienteFunction('dni', $documento);

		$deuda = new getDatosV10();
		$rst_deuda = $deuda->getDeudaFunction('dni', $documento);
		$this->view->ofivirtual_deuda($rst_deuda, $rst_cliente);
	}

	/* OFICINA VIRTUAL: DESCARGAR IMPRESO***********************************/
	function descargar_factura() {
        require_once "core/helpers/facturaFile.php";
    }
	/* OFICINA VIRTUAL******************************************************/

	/* COMMON **************************************************************/
	function ver_archivo(){
		require_once "core/helpers/files.php";
	}

	function ver_archivo_unico(){
		require_once "core/helpers/file.php";
	}
	/* COMMON **************************************************************/
}
?>
