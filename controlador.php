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
	$objA=new alertas();
	
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
		case "mostrarCorreosCliente":
			/*echo "<pre>";
			print_r($_GET);
			echo "</pre>";*/
			$correosE=$objA->mostrarCorreosCliente($_GET["idCliente"],$_GET["idUsuarioAlerta"],$_GET["filtro"]);
			
			if($correosE=="S/N"){
				echo "( 0 ) registros encontrados.";
			}else{
				$correosET=explode(";",$correosE);
				
				$tpl->set_filenames(array('controlador' => 'tCorreosElectronicos'));

				for($i=0;$i<count($correosET);$i++){
					$tpl->assign_block_vars('listadoEmails',array(
	                    'EMAIL' =>	$correosET[$i]
	                ));
				}
				$tpl->pparse('controlador');
			}
		break;
		case "mostrarUnidadesCliente":
			/*echo "<pre>";
			print_r($_GET);
			echo "</pre>";*/
			$unidades=$objA->mostrarUnidadesCliente($_GET["idCliente"],$_GET["idUsuarioAlerta"],$_GET["filtro"]);
			if($unidades=="S/N"){
				echo "( 0 ) registros encontrados.";
			}else{
				//echo $unidades;
				$unidades=explode("|||||",$unidades);
				/*echo "<pre>";
				print_r($unidades);
				echo "</pre>";*/
				$tpl->set_filenames(array('controlador' => 'tListadoUnidades'));

				for($i=0;$i<count($unidades);$i++){
					$unidad=explode("|||",$unidades[$i]);
					$tpl->assign_block_vars('listadoUnidades',array(
	                    'IDUNIDAD' 	=>	$unidad[2],
	                    'UNIDAD'	=>	$unidad[3]
	                ));
				}
				$tpl->pparse('controlador');
			}
		break;
	}
	
	switch($_POST["action"]){
		
	}
}

?>