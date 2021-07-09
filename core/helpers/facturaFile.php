<?php
$nis=$_REQUEST['n'];
$date=$_REQUEST['d'];
$nfact=$_REQUEST['nf'];

header ('Content-Type: application/pdf');
header("Content-disposition: attachment; filename=$nis-$date-$nfact.pdf");
  
$archivohttp = file_get_contents("http://provider:123456@200.91.37.167:9190/FacturaProvider/get?id=".$_REQUEST['f']);
echo ($archivohttp);
?>