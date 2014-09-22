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
	
	$idCliente   = $userAdmin->user_info['ID_CLIENTE'];
	$idUsuario	 = $userAdmin->user_info['ID_USUARIO'];
	$select = '';
	
	//******************************* tipos
	
	$sql_tipo = "SELECT ID_OBJECT_MAP,DESCRIPCION FROM 	ADM_GEOREFERENCIAS WHERE TIPO = '".$_POST['tip_g']."' AND ID_CLIENTE =".$idCliente;
	$qry_tipo = $db->sqlQuery($sql_tipo);
	$cnt = $db->sqlEnumRows($qry_tipo);
	if($cnt > 0){
		$select = '<select style=" width:100px;" id="TiposPDIGCRSI">';
		  $select .= '<option value="-1">Elegir opci&oacute;n</option>';
	    while($row_tipo = $db->sqlFetchArray($qry_tipo)){	
		  $select .= '<option value="'.$row_tipo['ID_OBJECT_MAP'].'">'.$row_tipo['DESCRIPCION'].'</option>';
	   }
	    $select .= '</select>';
	}
	
	echo $select;
?>