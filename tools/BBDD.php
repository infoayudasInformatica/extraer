<?php

class BBDD {
    
    function insertar($datosFinales,$i){
        require_once 'conexion.php';
        $db = new Db();
        $db->conectar('');

        
        $strSQL = "
                    SELECT IF(ISNULL(MAX(id)),1,MAX(id)+1) AS id FROM ia_datos
                   ";
        
        $stmt = $db->ejecutar ( $strSQL );
                
        if(!$stmt){
            //si ha fallado la consulta DEVOLVEMOS false
            $db->desconectar ();
            return false;
        }

        $row =  mysql_fetch_array($stmt);
        $Id =  $row['id'];
        
        $strSQL = "
                    INSERT INTO ia_datos (id, numeroBOE, fecha, titulo, pdf)
                    VALUES ($Id,".trim($datosFinales['NumeroBOE']).",'".$this->fecha_to_DATETIME($datosFinales['fecha'])."',
                            '". utf8_decode($datosFinales['leyes'][$i]['titulo'])."','".$datosFinales['leyes'][$i]['PDF']."')
                   ";
        
        $stmt = $db->ejecutar ( $strSQL );
        $db->desconectar ();
                
        if($stmt){
            //se ha insertado, devolvemos true
            return true;
        }else{
            //si ha fallado, DEVOLVEMOS false
            return false;
        }
    }
    
    //funcion que escribe la fecha 'dd/mm/yyyy' a date time '0000-00-00 00:00:00'
    function fecha_to_DATETIME($fecha){
        $trozos=explode('/',$fecha);
        //compruebo que tenga 2 digitos el formato de dia, sino le añado ceros a la izda
        $long=strlen($trozos[0]);
        for($i=1;$i<=2-$long;$i++){
            $trozos[0]='0'.$trozos[0];
        }
        //compruebo que tenga 2 digitos el formato de dia, sino le añado ceros a la izda
        $long=strlen($trozos[1]);
        for($i=1;$i<=2-$long;$i++){
            $trozos[1]='0'.$trozos[1];
        }

        return $trozos[2].'-'.$trozos[1].'-'.$trozos[0].' 00:00:00';
    }
    
}
