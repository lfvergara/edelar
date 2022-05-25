<?php
class Impreso {
    var $id;
    var $nis;
    var $date;
    var $number;

    function Impreso($aa) {
        foreach ($aa as $k=>$v)
            $this->$k = $aa[$k];
    }
}
        
function readService($urlService) {
    //$archivohttp = @file_get_contents($urlService);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $urlService,
        CURLOPT_USERAGENT => 'Edelar Service'
    ));

    $archivohttp = curl_exec($curl);
    curl_close($curl);
    $parser = xml_parser_create('UTF-8');
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    $result=xml_parse_into_struct($parser, $archivohttp, $values, $tags);
    xml_parser_free($parser);
    if($result==1) {
        foreach ($tags as $key=>$val) {
            if ($key == "factura") {
                $factura= $val;
                for ($i=0; $i < count($factura); $i+=2) {
                    $offset = $factura[$i] + 1;
                    $len = $factura[$i + 1] - $offset;
                    $tdb[] = parseArchivo(array_slice($values, $offset, $len));
                }
            } else {
                continue;
            }
        }

        return $tdb;
    }
}

function parseArchivo($mvalues) {
    for ($i=0; $i < count($mvalues); $i++) {
        $mol[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
    }
 
    return new Archivo($mol);
}
?>