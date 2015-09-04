<?php

//si hemos submitido
if(isset($_POST['fecha'])){

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
    $datosFinales = '';
    for ($i = 0; $i < count($datos); $i++) {
        
//        echo $datos[$i].'<br/>';
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
        

//        $path = dirname(__FILE__)."\PDF\\".$nombre;
//
//        $ch = curl_init($file);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $data = curl_exec($ch);
//
//        curl_close($ch);
//
//        $OK = file_put_contents($path, $data);
        
        $row['titulo'] = $titulo;
        $row['PDF'] = $nombre;
        $row['urlPDF'] = $urlPDF;

        $datosFinales[] = $row;
    }

    var_dump($datosFinales);
    
    //ahora
    
    
}

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
        for ($i = 0; $i < count($datosFinales); $i++) {
            ?>
            listadoProveedores[<?php echo $i;?>] = "<?php echo $datosFinales[$i]['urlPDF'];?>";
            <?php
        }
        ?>

        //progreso actual
        var currProgress = 0;
        //esta la tarea completa
        var done = false;
        //cantidad total de progreso
        var total = <?php echo count($datosFinales);?>;

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
        <form action="index.php" method="POST" name="form1">
            <label>Elige fecha</label>
            <input type="text" name="fecha" id="fecha" />
            <input type="submit" name="submit" value="OK" />
        </form>
        <span id="numValue"></span>
    </body>
</html>
<?php
//}
?>