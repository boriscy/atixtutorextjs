<?php
// Se incluye este archivo para pa conexión a la base de datos y se crea la variable
// $link que hace referecia a la conexión
include_once('db.php');
/**
* Clase Arbol, permite la adición, reemparentamiento y borrado de nodos de nodos
* de un árbol
*/
class Arbol{
	
	var $datos, $bd;
	
	function __construct($data, $bd) {
		$params = $data;
		unset($params['accion']);
		$this->bd = $bd;
		$this->{$data['accion']}($params);
	}
	
	/**
	* Función que permite la ceración de nuevos nodos
	* @param array() $params Parametros
	* @param string $params['texto'] Texto del nuevo nodo
	* @param integer $param['padre'] ID del nodo padre
	*/
	function crear($params) {
		$texto = mysql_real_escape_string($params['texto']);
		$padre = intval($params['padre']);
		
        $query = "INSERT INTO arbol (nombre, parent_id) VALUES ('$texto', $padre) ";
		if (mysql_query($query, $this->bd)) {
			$this->presentar(array('success' => true));
		}else{
			$this->presentar(array('success' => false));
		}
	}
	
	/**
	* Funcion que permite borrar un nodo seleccionado
	* @param array $params Parametros 
	* @param integer $params['nodo'] ID del nodo que se desea borrar
	*/
	function borrar($params) {
		$nodo = intval($params['nodo']);
		$res = mysql_query("SELECT * FROM arbol WHERE parent_id=".$nodo, $this->bd);
		if(!$res) {
		    $query = "DELETE FROM arbol WHERE id=".$nodo;
		    mysql_query($query, $this->bd);
		    $this->presentar(array('success' => true) );
		}else{
		    $this->presentar(array('success' => false, 'error' => 'Debe seleccionar un nodo sin hijos') );
		}
	}
	
	/**
	* Función que presenta todos los nodos hijo de un determinado Nodo en caso de que sea el nodo tenga un valor
	* igual a cero o nulo se presenta todos los nodos raiz
	* @param array $params Parametros 
    * @param array $params['node'] ID del nod del cual se tiene que mostrar
	*/
	function mostrar($params) {
	    $nodo = intval($params['node']);
		$buscar = "=".$nodo;
		if($nodo == 0) {
			$buscar = " IS NULL";
		}
		
		$res = mysql_query("SELECT * FROM arbol WHERE parent_id".$buscar, $this->bd);
		$array = array();
		while ($fila = mysql_fetch_assoc($res)) {
			$array[] = array('id' => $fila['id'], 'text' => $fila['nombre'], 'leaf' => false);
		}
		
		$this->presentar($array);
	}
	/**
	* Función que permite reemparentar los nodos moviendolos
	* @param array $params Parametros 
	* @param integer $params['nodo'] Nodo el cual se mueve
	* @param integer $params['nuevoPadre'] ID del nuevo padre
	*/
	function mover($params) {
	    $nodo = intval($params['nodo']);
	    $nuevoPadre = intval($params['nuevoPadre']);
		$query = "update arbol set parent_id=$nuevoPadre where id=$nodo";
		if (mysql_query($query, $this->bd)) {
			$this->presentar(array('success' => true));
		}else{
			$this->presentar(array('success' => false));
		}
	}
	
	/**
	* Función que presenta la información
	* @param array() $data Array con la lista de parametros a presentar
	*/
	function presentar($data) {
		echo json_encode($data);
	}
}

if(isset($_REQUEST)) {
	$arbol = new Arbol($_REQUEST, $link);
}


