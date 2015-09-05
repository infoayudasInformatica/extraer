<?php
require_once './tools/simple_html_dom.php';


//FORRALLA
function extraer2($fecha){
    $fechaE = explode('/',$fecha);
    //I Disposiciones Generales
    $urlBOE = 'http://www.boe.es/boe/dias/'.$fechaE[2].'/'.$fechaE[1].'/'.$fechaE[0].'/index.php?s=1';
    $urlBOE = './wwwboe/boe.php';

    $html = file_get_html($urlBOE);

    //busco numero de BOE
    $tit = $html->find('div.linkSumario2 p');
    $titulo = $tit->plaintext;
    
    //ahora busco lo que hay en las distintas leyes aprobadas
    $row = '';
    $i=0;
    foreach ($html->find('li.dispo p') as $value) {
        $row[$i]['titulo'] = $value->plaintext;
        $i++;
    }
    $i=0;
    foreach ($html->find('li.dispo div ul li.puntoPDF a') as $value) {
        $urlPDF = "www.boe.es" . $value->href;
        $row[$i]['urlPDF'] = $urlPDF;
        
        $file  = $urlPDF;

        $nombreExplode = explode('/',$file);
        $nombre = $nombreExplode[count($nombreExplode)-1];
        $row[$i]['PDF'] = $nombre;
        $i++;
    }
    
    
    $resultado['leyes'] = $row;  
//    var_dump($resultado);die;
    return $resultado;
}


//funcion que extrae los datos
function extraer($fecha){
    
    $fechaE = explode('/',$fecha);
    //I Disposiciones Generales
    $urlBOE = 'http://www.boe.es/boe/dias/'.$fechaE[2].'/'.$fechaE[1].'/'.$fechaE[0].'/index.php?s=1';
    
    $c = curl_init($urlBOE);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($c);
    curl_close($c);


    //busco numero de BOE
    preg_match_all("#</abbr>(.*?)</h2>#U", $content, $sumario);
    
    $datosFinales = '';
    $datosFinales['fecha'] = $fecha;
    $datosFinales['NumeroBOE'] = $sumario[1][0];
    
    //ahora busco lo que hay en las distintas leyes aprobadas
    preg_match_all("'<li class=\"dispo\">(.*?)</li>'si", $content, $title);
//    preg_match_all('|<li class="dispo"><p>(.*?)</p></li>|is', $content, $title);

    //preparo un array con los datos
    $datos = $title[1];
    for ($i = 0; $i < count($datos); $i++) {

        //titulo
        preg_match("#<p>(.*?)</p>#U", $datos[$i], $var1);
        $titulo = html_entity_decode($var1[1]);

        //PDF
        preg_match("#<a href=\"(.*?)\" title#U", $datos[$i], $var2);
        $urlPDF = "www.boe.es" . $var2[1];


        //descargar el fichero en la carpeta /PDF
        $file  = $urlPDF;

        $nombreExplode = explode('/',$file);
        $nombre = $nombreExplode[count($nombreExplode)-1];


        $row['titulo'] = $titulo;
        $row['PDF'] = $nombre;
        $row['urlPDF'] = $urlPDF;

        $datosLeyes1[] = $row;
    }
    
    //III otras disposiciones
    $urlBOE = 'http://www.boe.es/boe/dias/'.$fechaE[2].'/'.$fechaE[1].'/'.$fechaE[0].'/index.php?s=3';
    
    $c = curl_init($urlBOE);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($c);
    curl_close($c);
    
    //ahora busco lo que hay en las distintas leyes aprobadas
    preg_match_all("'<li class=\"dispo\">(.*?)</li>'si", $content, $title);

    //preparo un array con los datos
    $datos = $title[1];
    for ($i = 0; $i < count($datos); $i++) {

        //titulo
        preg_match("#<p>(.*?)</p>#U", $datos[$i], $var1);
        $titulo = $var1[1];

        //PDF
        preg_match("#<a href=\"(.*?)\" title#U", $datos[$i], $var2);
        $urlPDF = "www.boe.es" . $var2[1];


        //descargar el fichero en la carpeta /PDF
        $file  = $urlPDF;

        $nombreExplode = explode('/',$file);
        $nombre = $nombreExplode[count($nombreExplode)-1];


        $row['titulo'] = $titulo;
        $row['PDF'] = $nombre;
        $row['urlPDF'] = $urlPDF;

        $datosLeyes3[] = $row;
    }
    
    $datosLeyes = array_merge($datosLeyes1,$datosLeyes3);
    
    $datosFinales['leyes'] = $datosLeyes;

    if(is_array($datosFinales)){
        //guardo los datos extraidos en la BBDD
        require_once './tools/BBDD.php';
        $BBDD = new BBDD();

        for ($i = 0; $i < count($datosFinales['leyes']); $i++) {
            $OK = $BBDD->insertar($datosFinales,$i);
            if($OK){
                echo "Insercion correcta<br/>";
            }else{
                echo "No se insertó<br/>";
            }
        }

        var_dump($datosFinales);
    }else{
        echo "No hay datos en esa fecha";
    }
    
    
    return true;
}


//si hemos submitido
if(isset($_POST['fecha'])){

    if($_POST['fecha'] === ''){
        echo "introduzca una fecha";
        echo "<input type='button' value='volver' onclick='javascript:history.back();' />";die;
    }else{

        $datosFinales=extraer2($_POST['fecha']);
        
        var_dump($datosFinales);
        //ahora
        
        
    
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
<!--        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">-->
<!--        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />-->
        <meta charset="UTF-8">
        <title>Extraer datos BOE</title>
        
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
        <script>

        function evaluar(url){
            $.ajax({
              data:{"url":url},  
              url: './bajarPDF.php',
              type:"get",
              success: function(data) {
                //recuperamos el valor del texto
                var val = document.getElementById("numValue");
                //actualizamos el indicador visual con el texto
                val.innerHTML = data+val.innerHTML;
              }
            });
        }

        //var idFacturas=new Array();
        <?php
        //recorro el post y recojo en un array los id** = on
//        $datos = '';
//        foreach ($_POST as $key => $value) {
//            if('id' === substr($key,0,2)){
//                $num = substr($key,2);
//                $datos[$num] = $_POST['opt'.$num];
//            }
//        }
        ?>
        var listadoProveedores = new Array();

        <?php
        for ($i = 0; $i < count($datosFinales['leyes']); $i++) {
            ?>
            listadoProveedores[<?php echo $i;?>] = "<?php echo $datosFinales['leyes'][$i]['urlPDF'];?>";
            <?php
        }
        ?>

        //progreso actual
        var currProgress = 0;
        //esta la tarea completa
        var done = false;
        //cantidad total de progreso
        var total = <?php echo count($datosFinales['leyes']);?>;

        function startProgress() {
            //ejecuto la funcion que llama por AJAX la los procedimientos para guardar la factura    
            evaluar(listadoProveedores[currProgress]);

            ////incrementamos el valor del progreso cada vez que la funciÃ³n se ejecuta
            currProgress++;
            //comprobamos si hemos terminado
            if(currProgress>(listadoProveedores.length-1)){
                done=true;
            }
            // sino hemos terminado, volvemos a llamar a la funciÃ³n despuÃ©s de un tiempo
            if(!done)
            {
//                document.getElementById("startBtn").disabled = true;
                setTimeout("startProgress()",0);
            }  
            //tarea terminada, habilitar el botón
            else{   
//                document.getElementById("startBtn").disabled = false;
            }
        }


        </script>
        
    </head>
    <body
        onload="
                <?php
                if(isset($_POST['fecha'])){
                    echo "startProgress();";
                }
                ?>
                "
        >
        <?php
        if(!isset($_POST['fecha'])){
        ?>
        <form action="index.php" method="POST" name="form1">
            <label>Elige fecha</label>
            <input type="text" name="fecha" id="fecha" />
            <input type="submit" name="submit" value="OK" />
        </form>
        <?php
        }
        ?>
        <span id="numValue"></span>
    </body>
</html>
<?php
//}
?>