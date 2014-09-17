<?php
/**
*@name 				Controlador para las funciones de las Tareas
*@copyright         Air Logistics & GPS S.A. de C.V.  
*@author 			Gerardo Lara
*/

if($_SERVER["HTTP_REFERER"]==""){
	echo "0";
}else{
	include "claseAlertas.php";
	//se instancia la clase que contiene las funciones de los cuestionarios
	$objT=new alertas();
	
	switch($_GET["action"]){
		case "mostrarFormularioAlerta":
			/*echo "<pre>";
			print_r($_GET);
			echo "</pre>";*/
			$tpl->set_filenames(array('controlador' => 'tcrearAlerta'));
			$tpl->assign_vars(array(
				'IDCLIENTE'          	=> $_GET["idCliente"],
				'IDUSUARIO'       		=> $_GET["idUsuarioAlerta"]
			));
			//se muestra el template
			$tpl->pparse('controlador');
		break;
	}
	
	switch($_POST["action"]){
		
	}
}

?>