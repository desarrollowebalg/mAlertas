<?php
/** * 
 *  @package             
 *  @name                Pagina default del modulo silver 
 *  @version             1
 *  @copyright           Air Logistics & GPS S.A. de C.V.   
 *  @author              Rodwyn Moreno
 *  @modificado          23-04-2012
**/
 
	$db = new sql($config_bd['host'],$config_bd['port'],$config_bd['bname'],$config_bd['user'],$config_bd['pass']);
	
	if(!$userAdmin->u_logged())
		echo '<script>window.location="index.php?m=login"</script>';
	
	$tpl->set_filenames(array('default'=>'default'));	
	$idProfile   = $userAdmin->user_info['ID_PROFILE'];
	$idCliente   = $userAdmin->user_info['ID_CLIENTE'];
	$idUsuario	 = $userAdmin->user_info['ID_USUARIO'];
	/*echo "<pre>";
	print_r($userAdmin);
	echo "</pre>";*/
	$tpl->assign_vars(array(
		'PAGE_TITLE'	=> "Administraci&oacute;n de tareas",	
		'PATH'			=> $dir_mod,
		'IDCLIENTE'		=> $idCliente,
		'IDUSUARIO'	=> $idUsuario
	));

	$tpl->pparse('default');
?>