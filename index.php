<?php
require_once './tools/simple_html_dom.php';
require_once './tools/BBDD.php';
$BBDD = new BBDD();


//funcion que extrae los datos
function extraer($fecha){
    require_once './tools/BBDD.php';
    $BBDD = new BBDD();
    

    $fechaE = explode('/',$fecha);
    //I Disposiciones Generales
    $urlBOE = 'http://www.boe.es/boe/dias/'.$fechaE[2].'/'.$fechaE[1].'/'.$fechaE[0].'/index.php?s=1';

    $html = file_get_html($urlBOE);

    //busco numero de BOE
    foreach($html->find('li.puntoPDF a') as $a){
        $tit = $a->href;
        //se recorre todos los li, solo quiero el primero, que es donde esta el nombre de BOE
        break;
    }
    $titulo = explode('/',$tit);
    $titulo = $titulo[count($titulo)-1];
    $titulo = explode('.',$titulo);
    $titulo = $titulo[0];
    
    
    $datosFinales = '';
    $datosFinales['fecha'] = $fecha;
    $datosFinales['NumeroBOE'] = $titulo;
    
    
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
    

    //III Otras disposiciones
    $urlBOE = 'http://www.boe.es/boe/dias/'.$fechaE[2].'/'.$fechaE[1].'/'.$fechaE[0].'/index.php?s=3';

    $html = file_get_html($urlBOE);

    
    //ahora busco lo que hay en las distintas leyes aprobadas
    $row3 = '';
    $i=0;
    foreach ($html->find('li.dispo p') as $value) {
        $row3[$i]['titulo'] = $value->plaintext;
        $i++;
    }
    $i=0;
    foreach ($html->find('li.dispo div ul li.puntoPDF a') as $value) {
        $urlPDF = "www.boe.es" . $value->href;
        $row3[$i]['urlPDF'] = $urlPDF;
        
        $file  = $urlPDF;

        $nombreExplode = explode('/',$file);
        $nombre = $nombreExplode[count($nombreExplode)-1];
        $row3[$i]['PDF'] = $nombre;
        $i++;
    }
    
    $rowFinal = array_merge($row,$row3);
    
    
    $datosFinales['leyes'] = $rowFinal;  
    
    
    
    //guardo los datos extraidos en la BBDD
    if(is_array($datosFinales)){
        for ($i = 0; $i < count($datosFinales['leyes']); $i++) {
            $OK = $BBDD->insertar($datosFinales,$i);
            if($OK){
                echo "Insercion correcta<br/>";
            }else{
                echo "No se insertó<br/>";
            }
        }

        //var_dump($datosFinales);
    }else{
        echo "No hay datos en esa fecha";
    }
    
    
    return $datosFinales;
}




//si hemos submitido
if(isset($_POST['fecha'])){

    if($_POST['fecha'] === ''){
        echo "introduzca una fecha";
        echo "<input type='button' value='volver' onclick='javascript:history.back();' />";die;
    }else{
        $datosFinales = extraer($_POST['fecha']);
    }
}


////si hemos submitido
//if(isset($_POST['borrar'])){
//    $BBDD->borrarTabla();
//            
//}


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Extraer datos BOE</title>
        
<!--        <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <script src="https://code.jquery.com/ui/1.10.0/jquery-ui.js"></script> -->
        
<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
<script src="./js/jQuery/js/jquery.dataTables.qualidad.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />

<link rel="stylesheet" href="./js/jQuery/css/jquery-ui.qualidad.css" />
<script src="https://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>

<style type="text/css">
    @import "./js/jQuery/css/demo_table_jui.css";
    @import "./js/jQuery/themes/smoothness/jquery-ui-1.8.4.custom.css";
    @import "./js/jQuery/css/table_qualidad.css";
</style>
        

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
        
        <script type="text/javascript" charset="utf-8">

            $(document).ready(function(){

                //formatear y traducir los datos de la tabla
                $('#datatables').dataTable({
                    "bProcessing": true,
                    "sPaginationType":"full_numbers",
                    "oLanguage": {
                        "sLengthMenu": "Ver _MENU_ registros por pagina",
                        "sZeroRecords": "No se han encontrado registros",
                        "sInfo": "Ver _START_ al _END_ de _TOTAL_ Registros",
                        "sInfoEmpty": "Ver 0 al 0 de 0 registros",
                        "sInfoFiltered": "(filtrados _MAX_ total registros)",
                        "sSearch": "Busqueda:"
                    },
                    "bSort":true,
                    "aaSorting": [[ 0, "desc" ]],
                    "aoColumns": [
                            null,
                            { "sType": 'string' },
                            { "sType": 'string' },
                            { "sType": 'string' },
                            { "sType": 'string' }
                    ],
                    "bJQueryUI":true,
                    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
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
        
        <?php
        $arDoc = $BBDD->listado();
        ?>
        <br/><br/>
        <div align="center" style="width: 950px;">
            <h3>Listado</h3>
        <table id="datatables" class="display">
            <thead>
                <tr>
                    <th style="width:5%;">Id</th>
                    <th style="width:15%;">Número BOE</th>
                    <th style="width:10%;">Fecha</th>
                    <th style="width:55%;">Título</th>
                    <th style="width:15%;">PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(is_array($arDoc)){
                    for ($i = 0; $i < count($arDoc); $i++) {
                        //$link="javascript:document.location.href='../vista/docverext.php?id=".$arDoc[$i]['Id']."';";
                        $link="";

                        //arreglo la fecha
                        $fecha = explode('/',$arDoc[$i]['fecha']);

                        ?>
                        <tr>
                            <td onClick="<?php echo $link; ?>"><?php echo $arDoc[$i]['id']; ?></td>
                            <td onClick="<?php echo $link; ?>"><?php echo $arDoc[$i]['numeroBOE']; ?></td>
                            <td onClick="<?php echo $link; ?>"><?php echo "<!-- ".$fecha[2].$fecha[1].$fecha[0]." -->".$arDoc[$i]['fecha']; ?></td>
                            <td onClick="<?php echo $link; ?>"><?php echo $arDoc[$i]['titulo']; ?></td>
                            <td onClick="<?php echo $link; ?>"><?php echo $arDoc[$i]['pdf']; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        </div>
         
        <form action="borrarTabla.php" method="POST" name="form2">
            <input type="submit" value="borrar datos" name="borrar" />
        </form>
    </body>
</html>
<?php
//}
?>