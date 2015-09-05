<?php
/* Clase encargada de gestionar las conexiones a la base de datos */

class Db {
//        private $servidor = 'qpo213.qualidad.info';
//        private $usuario = 'qpo213';
//        private $password = 'Manuel5053';
//        private $base_datos = 'qpo213';

        private $servidor = 'localhost';
        private $usuario = 'root';
        private $password = '';
        private $base_datos = 'extraer';

	private $link;
	private $stmt;
	private $array;

        public function conectar($base_datos){
            $this->link = @mysql_connect ($this->servidor,$this->usuario,$this->password);
            @mysql_select_db ( $this->base_datos, $this->link );
            @mysql_query ( "SET NAMES 'utf8'" );
            //@mysql_set_charset('utf8');
        }
        
	
	/* Método para ejecutar una sentencia sql */
	public function ejecutar($sql) {
		try {
			$this->stmt = mysql_query ( $sql, $this->link );
//			$this->stmt = @mysql_query ( $sql, $this->link ) or logger('error','conexion.php-' ,"Usuario: ".$_SESSION['strUsuario'].', Empresa: '.$_SESSION['base'].', SesionID: '.  session_id(). ' -Error MySQL= '.  mysql_errno($this->link).': '. mysql_error($this->link));
			return $this->stmt;
		} catch ( Exception $e ) {
			// die("Metodo query: Error al ejecutar la sentencia SQL");
			return false;
		}
	}
	
	/* Método para obtener una fila de resultados de la sentencia sql */
	public function obtener_fila($stmt, $fila) {
		try {
			if ($fila == 0) {
				$this->array = mysql_fetch_array ( $stmt );
			} else {
				mysql_data_seek ( $stmt, $fila );
				$this->array = mysql_fetch_array ( $stmt );
			}
			return $this->array;
		} catch ( Exception $e ) {
			// die("Metodo query: Error al ejecutar la sentencia SQL");
			return false;
		}
	}
	
	// Devuelve el último id del insert introducido
	public function lastID() {
		try {
			return mysql_insert_id ( $this->link );
		} catch ( Exception $e ) {
			// die("Metodo query: Error al ejecutar la sentencia SQL");
			return false;
		}
	}
        
        public function desconectar(){
            mysql_close($this->link);
        }
}
?>