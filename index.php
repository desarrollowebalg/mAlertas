<?php
/** * 
 *  @package             
 *  @name                Indice del modulo silver
 *  @version             1
 *  @copyright           Air Logistics & GPS S.A. de C.V.   
 *  @author              Enrique Peña 
 *  @modificado          27-04-2011
**/
	/*$tpl = new Template('modules/'.$_GET['m'].'/template');
	$dir_pimages = "public/images";
	$dir_mod 	 = 'modules/'.$_GET['m'].'/template';
	$dbf         = new dbFunctions();
	
	if(isset($_GET['c'])){
		$filename = $config['modules']."".$_GET['m']."/".$_GET['c'].'.php';
		if(file_exists($filename)){
			include $filename;
		}else{
			echo "<script>window.location='errors/index.php?e=error404';</script>";
		}
	}else{
		echo "<script>window.location='index.php?m=".$_GET['m']."&c=default&j=".$_GET['j']."';</script>";
	}*/	
	$tpl = new Template('modules/'.$_GET['m'].'/template');
	$dir_pimages = "public/images";
	$dir_mod 	 = 'modules/'.$_GET['m'].'/template';
	$dbf         = new dbFunctions();	
	if(isset($_GET['c'])){
		$filename = $config['modules']."".$_GET['m']."/".$_GET['c'].'.php';
		if(file_exists($filename)){
			include $filename;
		}else{
			echo "<script>window.location='errors/index.php?e=error404';</script>";
		}
	}else{
		echo "<script>window.location='index.php?m=".$_GET['m']."&c=default';</script>";
	}		
?>