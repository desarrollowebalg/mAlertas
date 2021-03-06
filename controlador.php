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
	//se instancia la clase que contiene las funciones de las alertas
	$objA=new alertas();
	switch($_GET["action"]){
		case "mostrarFormularioAlerta":
			$tpl->set_filenames(array('controlador' => 'tcrearAlerta'));
			$tpl->assign_vars(array(
				'IDCLIENTE'          	=> $_GET["idCliente"],
				'IDUSUARIO'       		=> $_GET["idUsuarioAlerta"]
			));
			for($i=0;$i<24;$i++){
				if($i<10)
					$horas=	"0".$i;
				else
					$horas= $i;
				$tpl->assign_block_vars('listadoHoras',array(
	            	'HORAS' =>	$horas
	        	));
			}
			for($i=0;$i<60;$i++){
				if($i<10)
					$minutos="0".$i;
				else
					$minutos= $i;
				$tpl->assign_block_vars('listadoMinutos',array(
	            	'MINUTOS' =>	$minutos
	        	));
			}
			$arreglo_y = array();
			$arreglo_y = $objA->regresarDatosEventos();
			
		    for($ev=0;$ev<count($arreglo_y);$ev++){	 
				 $tpl->assign_block_vars('eventosX',array(
				'CLV_EVENTO'  => $arreglo_y[$ev][0],
				'DESCRIPCION' => $arreglo_y[$ev][1],
				));
			}
			//se muestra el template
			$tpl->pparse('controlador');
		break;
		case "mostrarCorreosCliente":
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
			//echo "<pre>";
			//print_r($_GET);
			//echo "</pre>";
			$unidades=$objA->mostrarUnidadesCliente($_GET["idCliente"],$_GET["idUsuarioAlerta"],$_GET["filtro"],$_GET["origen"],$_GET["alerta"]);
			if($unidades=="S/N"){
				echo "( 0 ) registros encontrados.";
			}else{
				$unidades=explode("|||||",$unidades);
				$tpl->set_filenames(array('controlador' => 'tListadoUnidades'));
				$tpl->assign_vars(array(
					'ORIGEN'	=> 	$_GET["origen"]
				));
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
		case "cargarAlertas":
			$objA->listarAlertas($_GET["filtro"],$_GET["idCliente"],$_GET["idUsuarioAlertas"]);
		break;
		case "detalleAlerta":
			$msgAlerta=$objA->mostrarDetalleAlerta($_GET["idCliente"],$_GET["idUsuario"],$_GET["idAlerta"]);
			//se separa la cadena original
			$msgAlerta=explode("?????",$msgAlerta);
			//echo "<pre>";
			//print_r($msgAlerta);
			//echo "</pre>";
			
			$detalleAlerta=explode("??",$msgAlerta[1]);
			
			$correos=explode(";",$msgAlerta[2]);
			/*echo "<pre>";
			print_r($correos);
			echo "</pre>";*/
			//echo "<pre>";
			//print_r($detalleAlerta);
			//echo "</pre>";

			$tpl->set_filenames(array('controlador' => 'tDetalleAlerta1'));
			$datosAlerta=explode("||",$msgAlerta[0]);
			/*echo "<pre>";
			print_r($datosAlerta);
			echo "</pre>";*/
			(count($detalleAlerta) > 1) ? $mostrar="inline;" : $mostrar="none;";
			
			for($i=0;$i<count($detalleAlerta);$i++){
				$detalleAlerta1=explode("|||",$detalleAlerta[$i]);
				$tpl->assign_block_vars('listadoUnidadesDetalle',array(
						'IDOBJECTMAP'	=>  $detalleAlerta1[0],
	                    'IDUNIDAD' 		=>	$detalleAlerta1[1],
	                    'UNIDAD'		=>	$detalleAlerta1[2],
	                    'IDREGISTRO'	=>	$detalleAlerta1[3]
	            ));
			}
			for($i=0;$i<count($correos);$i++){
				$correoE=str_replace("???","",$correos[$i]);
				$tpl->assign_block_vars('listadoCorreos',array(
						'EMAIL'		=>  $correoE
	            ));	
			}
			($datosAlerta[16]==" ") ? $fechaCreacion="Sin Fecha especificada" : $fechaCreacion=explode(" ",$datosAlerta[16]);
			
			$textoFecha=$objA->regresarDatosFecha($fechaCreacion[0],$fechaCreacion[1]);
			$usuarioCreo=$objA->regresaNombreUsuario($datosAlerta[14]);
			switch($datosAlerta[11]){
				case "U":
					$tipoExpresion="UNIDAD";
				break;
				case "P":
					$tipoExpresion="PUNTO DE INTERES (GEOPUNTO)";
				break;
				case "G":
					$tipoExpresion="GEOCERCA";
				break;
				case "R":
					$tipoExpresion="RSI";
				break;
			}
			($datosAlerta[2]==0) ? $imagenLunes="<img src='./public/images/cross.png' border='0' />" : $imagenLunes="<img src='./public/images/icon-unselect.png' border='0' />";
			($datosAlerta[3]==0) ? $imagenMartes="<img src='./public/images/cross.png' border='0' />" : $imagenMartes="<img src='./public/images/icon-unselect.png' border='0' />";
			($datosAlerta[4]==0) ? $imagenMiercoles="<img src='./public/images/cross.png' border='0' />" : $imagenMiercoles="<img src='./public/images/icon-unselect.png' border='0' />";
			($datosAlerta[5]==0) ? $imagenJueves="<img src='./public/images/cross.png' border='0' />" : $imagenJueves="<img src='./public/images/icon-unselect.png' border='0' />";
			($datosAlerta[6]==0) ? $imagenViernes="<img src='./public/images/cross.png' border='0' />" : $imagenViernes="<img src='./public/images/icon-unselect.png' border='0' />";
			($datosAlerta[7]==0) ? $imagenSabado="<img src='./public/images/cross.png' border='0' />" : $imagenSabado="<img src='./public/images/icon-unselect.png' border='0' />";
			($datosAlerta[8]==0) ? $imagenDomingo="<img src='./public/images/cross.png' border='0' />" : $imagenDomingo="<img src='./public/images/icon-unselect.png' border='0' />";
			
			($datosAlerta[12]==1) ? $activa="SI" : $activa="NO";
			($datosAlerta[13]=="N") ? $vigente="NO" : $vigente="SI";
			//modificacion para mostrar el origen de la alerta
			$expresionAlerta=$datosAlerta[17];
			//$expresionAlerta=explode("AND",$expresionAlerta);

			//echo "<pre>";
			//print_r($expresionAlerta);
			//echo "</pre>";			

			$tpl->assign_vars(array(
				'NOALERTA'          	=> $datosAlerta[0],
				'NOMBREALERTA'       	=> $datosAlerta[1],
				'LUNES'					=> $imagenLunes,
				'MARTES'				=> $imagenMartes,
				'MIERCOLES'				=> $imagenMiercoles,
				'JUEVES'				=> $imagenJueves,
				'VIERNES'				=> $imagenViernes,
				'SABADO'				=> $imagenSabado,
				'DOMINGO'				=> $imagenDomingo,
				'HORAINICIO'			=> $datosAlerta[9],
				'HORAFIN'				=> $datosAlerta[10],
				'TIPOEXPRESION'			=> $tipoExpresion,
				'ACTIVA'				=> $activa,
				'VIGENTE'				=> $vigente,
				'USUARIOCREO'			=> $usuarioCreo,
				'FECHACREACION'			=> $textoFecha,
				'EXPRESIONALERTA'		=> $expresionAlerta,
				'MOSTRARBTNBORRAR'		=> $mostrar
			));
			$tpl->pparse('controlador');
		break;
		case "eliminarAlertasDetalle":
			$eliminarAlerta=$objA->eliminarAlertas($_GET["idCliente"],$_GET["idUsuario"],$_GET["elementoActualizar"]);
			echo $eliminarAlerta;
		break;
		case "activarDesactivarAlertas":
			$actualizacion=$objA->actualizarAlertasActDes($_GET["idCliente"],$_GET["idUsuario"],$_GET["elementoActualizar"]);
			echo $actualizacion;
		break;
	}
	switch($_POST["action"]){
		case "eliminarUnidadAlerta":
			//echo "<pre>";
			//print_r($_POST);
			//echo "</pre>";
			$objA->eliminarUnidadesAlerta($_POST["idUnidades"],$_POST["noAlerta"],$_POST["idUsuarioAlerta"],$_POST["idCliente"]);
		break;
		case "agregarUnidadesAlerta":
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			$objA->agregarUnidadesAlerta($_POST["unidades"],$_POST["noAlerta"],$_POST["idClienteAlerta"],$_POST["idUsuarioAlerta"]);
		break;
	}
}
?>