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
  	$cadenaMail = '';
	$cadenaMailMaster = '';
    $master = explode("|",$_POST['cadena']);
	$id_master = 0;
	$id_mail   = 0;
    $variables = explode(",",$_POST['cadena']);
	$variables2 = explode("AND",$variables[3]);
	$insert = '';
	$values = '';
	$idObjectMap = explode("=", $master[3]);
	$unidadesVariables = explode(",", $master[2]);
	
	 $sql_master = "INSERT INTO ALERT_XP_MASTER
				 (COD_CLIENT,
				 NAME_ALERT,
				 ACTIVE,
				 VIGENTE,
				 ALARM_EXPRESION,
				 TYPE_EXPRESION,
				 HORARIO_FLAG_LUNES,
				 HORARIO_FLAG_MARTES,
				 HORARIO_FLAG_MIERCOLES,
				 HORARIO_FLAG_JUEVES,
				 HORARIO_FLAG_VIERNES,
				 HORARIO_FLAG_SABADO,
				 HORARIO_FLAG_DOMINGO,
				 HORARIO_HORA_INICIO,
				 HORARIO_HORA_FIN,
				 COD_USER_CREATE,
				 NICKNAME_USER_CREATE,
				 FECHA_CREATE)
				 VALUES (".$idCliente.",".stripslashes($master[0]).",'".date('Y-m-d H:m:s')."')";
				 
	$qry_master = $db3->sqlQuery($sql_master);

    if($qry_master){
      
	            $sql_v   = "SELECT LAST_INSERT_ID() AS INDX FROM ALERT_XP_MASTER";
				$query_v = $db3->sqlQuery($sql_v);
				$row_v   = $db3->sqlFetchArray($query_v);
                $id_master = $row_v['INDX'];
				
	  $cadenaMail =  $idCliente.',"'.$master[1].'"';  
       $sql_mail = "INSERT INTO ALERT_XP_EMAIL
				   (COD_CLIENT,CORREO_ELECTRONICO)
	   			    VALUES (".$cadenaMail.")";
					
		$qry_mail = $db3->sqlQuery($sql_mail);
		
		 if($qry_mail){
			    $sql_w   = "SELECT LAST_INSERT_ID() AS INDX2 FROM ALERT_XP_EMAIL";
				$query_w = $db3->sqlQuery($sql_w);
				$row_w   = $db3->sqlFetchArray($query_w);
                $id_mail = $row_w['INDX2'];
				
			
				/********************************************************/
								  
				    $con1 = '';
					for($v=0;$v<count($variables2);$v++){
	  			        $con1 = explode("=", stripslashes(str_replace('>','',str_replace('"','',$variables2[$v]))));
					    if($insert === ''){
							$insert = $con1[0];
							$values = '"'.trim($con1[1]).'"';
						}else{
							$insert = $insert.','.$con1[0];
							$values = $values.',"'.trim($con1[1]).'"';
						}
					
					}
					
					  if($master[3]===''){
						 $idObjectMap2  = 'ID_OBJECT_MAP';
						 $idObjectMapVal = '0'; 
					  }else{
						 $idObjectMap2  = $idObjectMap[0];
						 $idObjectMapVal = $idObjectMap[1];   
					  }
					  
					  $insert = $insert.','.$idObjectMap2;
					  $values = $values.',"'.$idObjectMapVal.'"';
					  $values2 ='';
				     for($v2=0;$v2<count($unidadesVariables);$v2++){
						 if($values2 ===''){
							$values2 = '('.$values.',"'.$unidadesVariables[$v2].'","'.$unidadesVariables[$v2].'","'.$id_master.'")'; 
						 }else{
							 $values2 = $values2.',('.$values.',"'.$unidadesVariables[$v2].'","'.$unidadesVariables[$v2].'","'.$id_master.'")'; 
						 }
						 
					 }
					 
					 
					  $sql_varaibles_detalle = "INSERT INTO ALERT_XP_DETAIL_VARIABLES(".$insert.",COD_ENTITY,DESCRIP_ENTITY,COD_ALERT_MASTER) VALUES ".$values2."";
					 
				 $qry_detail_variables = $db3->sqlQuery($sql_varaibles_detalle);					 
				
				 if($qry_detail_variables){
				     
				       $sql_recuperar_unidades = "SELECT COD_ALERT_ENTITY, COD_ENTITY, DESCRIP_ENTITY FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_MASTER =".$id_master;
				       $qry_recuperar_unidades = $db3->sqlQuery($sql_recuperar_unidades);	
					  if($qry_recuperar_unidades){
					      while($row_recuperar_unidades = $db->sqlFetchArray($qry_recuperar_unidades)){	
						     if($cadenaMailMaster === ''){
						$cadenaMailMaster = "('".$id_mail."','".$id_master."','".$row_recuperar_unidades['COD_ALERT_ENTITY']."','".$row_recuperar_unidades['COD_ENTITY']."','".$row_recuperar_unidades['DESCRIP_ENTITY']."')"; 
							 }else{
						$cadenaMailMaster = $cadenaMailMaster.",('".$id_mail."','".$id_master."','".$row_recuperar_unidades['COD_ALERT_ENTITY']."','".$row_recuperar_unidades['COD_ENTITY']."','".$row_recuperar_unidades['DESCRIP_ENTITY']."')"; 	 
							 }
						  }
						//$cadenaMailMaster = $id_mail.','.$id_master;
					  }
					 
					        $sql_mail_master = "INSERT INTO ALERT_XP_EMAIL_MASTER
												(COD_ALERT_XP_EMAIL,COD_ALERT_MASTER,COD_ALERT_ENTITY, COD_ENTITY, DESCRIP_ENTITY)
												 VALUES ".$cadenaMailMaster;
							$qry_mail_master = $db3->sqlQuery($sql_mail_master);
							
							 if($qry_mail_master){
							  	 echo "1,Alerta Generada con Exito";
							 }else{
								 echo "2,Error al generar la alerta (error en qry_mail_master)";
							 }   

				 }else{
					   echo "3,Error al generar la alerta (error en qry_detail_variables)";
				 }
				
				
				
				/********************************************************/
		 }else{
					 echo "4,Error al generar la alerta (error en qry_mai)";
					
		 }
					
	}else{

					 echo "5,Error al generar la alerta (error en qry_master)";
					 
 }

?>
