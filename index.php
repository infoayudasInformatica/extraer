<?php

//si hemos submitido
if(isset($_POST['fecha'])){
    
//    $options = array('http' => array(
//    'method'  => 'GET',
//    ));
//    
//    
//    $html = file_get_contents('http://www.boe.es/boe/dias/2015/09/03/',false, $config);
//    preg_match_all('<li class="dispo">(.*)</li>', $html, $title);
//
//    //ver datos
//    var_dump($title);

    
    //CURL
    $c = curl_init('http://www.boe.es/boe/dias/2015/09/02/');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_HTTPHEADER, array(
    "Accept: */*",
    "Accept-Language: *",
    "Host: d.mismarcadores.com",
    "Referer: http ://d.mismarcadores.com/x/feed/proxy",
    "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:20.0) Gecko/20100101 Firefox/20.0",
    "X-Fsign: SW9D1eZo",
    "X-GeoIP: 1",
    "X-Requested-With: XMLHttpRequest",
    "X-utime: 1"
    ));
    $content = curl_exec($c);
    curl_close($c);

    //echo $content;
    
    //ahora busco lo que hay en 
    preg_match_all('|<li class="dispo">(.*?)</li>|is', $content, $title);
//    preg_match_all('|<li class="dispo"><p>(.*?)</p></li>|is', $content, $title);
    
    //preparo un array con los datos
    $datos = $title[1];
    for ($i = 0; $i < count($datos); $i++) {
        
        echo $datos[$i].'<br/>';
        
        
    }
    
    //var_dump($title);
    //CURL

}else{

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>extraer datos</title>
        
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <script src="https://code.jquery.com/ui/1.10.0/jquery-ui.js"></script> 

        <script language="JavaScript">
        jQuery(function($){
           $.datepicker.regional['es'] = {
              closeText: 'Cerrar',
              prevText: '&#x3c;Ant',
              nextText: 'Sig&#x3e;',
              currentText: 'Hoy',
              monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
              monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
              dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
              dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
              dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
              weekHeader: 'Sm',
              dateFormat: 'dd/mm/yy',
              firstDay: 1,
              isRTL: false,
              showMonthAfterYear: false,
              yearSuffix: ''};
           $.datepicker.setDefaults($.datepicker.regional['es']);
        });

        $(function() {
                $("#fecha").datepicker({
                    changeMonth: true, 
                    changeYear: true, 
                });
        });
        </script>
        
    </head>
    <body>
        <form action="index.php" method="POST" name="form1">
            <label>Elige fecha</label>
            <input type="text" name="fecha" id="fecha" />
            <input type="submit" name="submit" value="OK" />
        </form>
    </body>
</html>
<?php
}
?>