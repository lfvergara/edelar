<?php
class Captcha{
    public function Mostrar(){
        session_start();
        $longitud = 6;
        $caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $captcha = "";
        for($i = 0; $i < $longitud; $i++)
        {
            $captcha .= $caracteres[rand(0, strlen($caracteres)-1)];
        }
        $_SESSION['captcha_codigo'] = strtolower($captcha);
        $fuente_tamano = 40;
        $alto = 97;
        $ancho = 260;
        $fuente = $this->getfuente();
        $img = imagecreatefromjpeg(dirname(__FILE__)."/bg.jpg");
        $blanco = ImageColorAllocate($img, 255, 255, 255);
        $x = $fuente_tamano;
        $y = ($alto / 2)+12;
        imagettftext($img, $fuente_tamano, 0, $x, $y, $blanco, $fuente, $captcha);
        header("Content-type: image/jpeg");
        imagejpeg($img);
        imagedestroy($img);
    }

    private function getfuente(){
        $fuente = '';
        $numero = rand(0,5);
        $fuente = dirname(__FILE__)."/fuentes/".$numero.".ttf";
        return $fuente;
    }
    
    public static function Validar($valor){
        session_start();
        if(!isset($_SESSION['captcha_codigo'])) return false;
        return $_SESSION['captcha_codigo'] == $valor;
    }

    public static function destruir(){
        session_destroy();
    }

}