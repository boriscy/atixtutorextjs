<?php
include_once('db.php');
class Arbol{
	
	var $datos, $bd;
	
	function __construct($data, $bd) {
		$params = $data;
		unset($params['accion']);
		$this->bd = $bd;
		$this->{$data['accion']}($params);
	}
	
	function crear($params) {
		$nodo = $params['nodo'];
		
		$padre = $params['padre'];
		
		$res = mysql_query("select * from arbol", $this->bd);
		
		while ($fila = mysql_fetch_assoc($res)) {
			print_r($fila);
		}
		//$this->presentar(array('nodo' =>$nodo, 'padre' => $padre) );
	}
	function borrar($params) {
		
	}
	
	// Presenta los hijos de un padre
	function mostrar($params) {
		$buscar = "={$params['node']}";
		if($params['node'] == 0) {
			$buscar = " IS NULL";
		}
		
		$res = mysql_query("select * from arbol where parent_id".$buscar, $this->bd);
		$array = array();
		while ($fila = mysql_fetch_assoc($res)) {
			$array[] = array('id' => $fila['id'], 'text' => $fila['nombre'], 'leaf' => false);
		}
		
		$this->presentar($array);
	}
	
	function mover($params) {
		$query = "update arbol set parent_id={$params['nuevoPadre']} where id={$params['nodo']}";
		if (mysql_query($query, $this->bd)) {
			$this->presentar(array('success' => true));
		}else{
			$this->presentar(array('success' => false));
		}
	}
	
	function presentar($data) {
		echo json_encode($data);
	}
}

if(isset($_REQUEST)) {
	$arbol = new Arbol($_REQUEST, $link);
}


