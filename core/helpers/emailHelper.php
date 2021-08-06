<?php
require_once 'common/libs/PHPMailer/class.phpmailer.php';
require_once 'common/libs/domPDF/dompdf_config.inc.php';


class EmailHelper extends View {
	public function envia_contacto($array_dict) {
                $gui = file_get_contents("static/common/contacto.html");
                $fecha_descompuesta = $this->descomponer_fecha();
                $gui = $this->render($array_dict, $gui);
                $gui = $this->render($fecha_descompuesta, $gui);

                $origen = "autogestion@edelar.com.ar";
                $nombre = "EDELAR: Consultas y Contactos";
                $destino = "contactoweb.edelar@emdersa.com.ar";

                $mail = new PHPMailer();
                $mail->From = $origen;
                $mail->FromName = $nombre;
                $mail->AddAddress($destino);
                $mail->AddReplyTo($origen);
                $mail->IsHTML(true);
                $mail->Subject = utf8_decode($nombre);
                $mail->Body = $gui;

                $mail->IsSMTP();
                $mail->Host = "smtp.mandrillapp.com";
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = "grodriguez.edelar@gmail.com";
                $mail->Password = "qgKWZFp15HWJQ1sMa5sd6Q";
                $mail->Send();
	}

        public function envia_curriculum($array_dict) {
                $gui = file_get_contents("static/common/curriculum.html");
                $fecha_descompuesta = $this->descomponer_fecha();
                $gui = $this->render($array_dict, $gui);
                $gui = $this->render($fecha_descompuesta, $gui);

                $origen = "autogestion@edelar.com.ar";
                $nombre = "EDELAR: Servicio de CV";

                $mail = new PHPMailer();
                $mail->From = $origen;
                $mail->FromName = $nombre;
                $mail->AddAddress("jmiranday.edelar@emdersa.com.ar");
		$mail->AddReplyTo($origen);
                $mail->IsHTML(true);
                $mail->Subject = utf8_decode($nombre);
                $mail->Body = $gui;

                $mail->IsSMTP();
                $mail->Host = "smtp.mandrillapp.com";
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = "grodriguez.edelar@gmail.com";
                $mail->Password = "qgKWZFp15HWJQ1sMa5sd6Q";
                $mail->Send();
        }

        public function envia_activacion($correoelectronico, $nueva_contrasena, $clave_activacion) {
                $gui = file_get_contents("static/common/activacion.html");
                $fecha_descompuesta = $this->descomponer_fecha();
                $gui = $this->render($fecha_descompuesta, $gui);
                $gui = str_replace('{nueva_contrasena}', $nueva_contrasena, $gui);
                $gui = str_replace('{clave_activacion}', $clave_activacion, $gui);
                $gui = str_replace('{correoelectronico}', $correoelectronico, $gui);
                $origen = "autogestion@edelar.com.ar";
                $nombre = "EDELAR: Activar cuenta";

                $mail = new PHPMailer();
                $mail->From = $origen;
                $mail->FromName = $nombre;
                $mail->AddAddress($correoelectronico);
                $mail->AddReplyTo($origen);
                $mail->IsHTML(true);
                $mail->Subject = utf8_decode($nombre);
                $mail->Body = $gui;

                $mail->IsSMTP();
                $mail->Host = "smtp.mandrillapp.com";
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = "grodriguez.edelar@gmail.com";
                $mail->Password = "qgKWZFp15HWJQ1sMa5sd6Q";
                $mail->Send();
        }

        public function envia_recupera_contrasena($correoelectronico, $nueva_contrasena) {
                $gui = file_get_contents("static/common/recupera_contrasena.html");
                $fecha_descompuesta = $this->descomponer_fecha();
                $gui = $this->render($fecha_descompuesta, $gui);
                $gui = str_replace('{nueva_contrasena}', $nueva_contrasena, $gui);
                $origen = "autogestion@edelar.com.ar";
                $nombre = "EDELAR: Servicio de CV, Consultas y Contactos";

                $mail = new PHPMailer();
                $mail->From = $origen;
                $mail->FromName = $nombre;
                $mail->AddAddress($correoelectronico);
                $mail->AddReplyTo($origen);
                $mail->IsHTML(true);
                $mail->Subject = utf8_decode($nombre);
                $mail->Body = $gui;

                $mail->IsSMTP();
                $mail->Host = "smtp.mandrillapp.com";
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = "grodriguez.edelar@gmail.com";
                $mail->Password = "qgKWZFp15HWJQ1sMa5sd6Q";
                $mail->Send();
        }
}
?>
