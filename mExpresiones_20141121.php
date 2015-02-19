<?php
/** * 
 *  @package             
 *  @name                carga de datos de expresiones, etc. 
 *  @version             1
 *  @copyright           Air Logistics & GPS S.A. de C.V.   
 *  @author              danielArazoSaladino
 *  @modificado         19-09-2014
**/
 
	 $db = new sql($config_bd['host'],$config_bd['port'],$config_bd['bname'],$config_bd['user'],$config_bd['pass']);
	$db3 = new sql($config_bd3['host'],$config_bd3['port'],$config_bd3['bname'],$config_bd3['user'],$config_bd3['pass']);
	
	if(!$userAdmin->u_logged())
		echo '<script>window.location="index.php?m=login"</script>';
	
	$tpl->set_filenames(array('mExpresiones'=>'tExpresiones'));	
	$idCliente     = $userAdmin->user_info['ID_CLIENTE'];
	$idUsuario	   = $userAdmin->user_info['ID_USUARIO'];
	$nombreUsuario = $userAdmin->user_info['NOMBRE_COMPLETO'];
	
	//******************************* Eventos
	
	$sql_eventos = "SELECT COD_EVENT,DESCRIPTION FROM ADM_EVENTOS WHERE ID_CLIENTE =".$idCliente;
	$qry_eventos = $db->sqlQuery($sql_eventos);
	$cnt = $db->sqlEnumRows($qry_eventos);
	if($cnt > 0){
	    while($row_evento = $db->sqlFetchArray($qry_eventos)){	
			$tpl->assign_block_vars('eventos',array(
			'CLV_EVENTO'  => $row_evento['COD_EVENT'],
			'DESCRIPCION' => $Functions->codif($row_evento['DESCRIPTION']),
			));
	   }
	}


//******************************* Prioridad
	
	$sql_prioridad = "SELECT * FROM DSP2_PRIORIDAD";
	$qry_prioridad = $db->sqlQuery($sql_prioridad);
	$cnt2 = $db->sqlEnumRows($qry_prioridad);
	if($cnt2 > 0){
	    while($row_prioridad = $db->sqlFetchArray($qry_prioridad)){	
			$tpl->assign_block_vars('prioridades',array(
			'CLV_PRIORIDAD'  => $row_prioridad['ID_PRIORIDAD'],
			'DESCRIPCION' => $Functions->codif($row_prioridad['DESCRIPCION']),
			));
	   }
	}	
	
	//************************** por defualt se cargan los PDI
	
	$sql_tipo = "SELECT ID_OBJECT_MAP,DESCRIPCION FROM 	ADM_GEOREFERENCIAS WHERE TIPO = 'G' AND ID_CLIENTE =".$idCliente;
	$qry_tipo = $db->sqlQuery($sql_tipo);
	$cnt_tipo = $db->sqlEnumRows($qry_tipo);
	if($cnt_tipo > 0){
	   while($row_tipo = $db->sqlFetchArray($qry_tipo)){	
		  $tpl->assign_block_vars('tipos_x',array(
			'ID_OBJECT_MAP'  => $row_tipo['ID_OBJECT_MAP'],
			'DESCRIPCION'    => $Functions->codif($row_tipo['DESCRIPCION'])
		  ));
	   }
	   
	}else{
		 $tpl->assign_block_vars('tipos_x',array(
			'ID_OBJECT_MAP'  => -1,
			'DESCRIPCION'    => 'Sin PDI'
		  ));
		
	}
	
	
	
	$tpl->assign_vars(array(
		'PAGE_TITLE'	=> "Expresiones",	
		'PATH'			=> $dir_mod,
		'IDCLIENTE'		=> $idCliente,
		'IDUSUARIO'	    => $idUsuario,
		'NOMBREUSUARIO' => $nombreUsuario
	));

	$tpl->pparse('mExpresiones');
?>