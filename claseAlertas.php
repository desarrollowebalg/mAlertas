<?php
/**
*@description       Clase para el manejo de las diferentes operaciones en el modulo de alertas
*@copyright         Air Logistics & GPS S.A. de C.V.  
*@author 			Gerardo Lara
*@version 			1.0.0
*/

class alertas{

	private $objDb;
	private $host;
	private $port;
	private $bname;
	private $user;
	private $pass;

	function __construct() {
		include "config/database.php";
		$this->host=$config_bd3['host'];
		$this->port=$config_bd3['port'];
		$this->bname=$config_bd3['bname'];
		$this->user=$config_bd3['user'];
		$this->pass=$config_bd3['pass'];
   	}
	/**
	*@method 			iniciar Conexion con la BD
	*@description 	Funcion para conectar con la base de datos
	*@paramas 		
	*
	*/
   	private function iniciarConexionDbGrid(){
   		$objBd=@mysql_connect($this->host,$this->user,$this->pass);
   		if($objBd){
   			@mysql_select_db($this->bname)or die("Error al conectar con la base de datos");
   		}
   		return $objBd;
   	}
   	private function iniciarConexionDb(){
   		include "config/database.php";
   		$objBd=new sql($config_bd['host'],$config_bd['port'],$config_bd['bname'],$config_bd['user'],$config_bd['pass']);
   		return $objBd;
   	}
   	private function iniciarConexionAlertas(){
   		$objBDA=new sql($this->host,$this->port,$this->bname,$this->user,$this->pass);
   		return $objBDA;
   	}
   	/**
	*@method 		escribir en archivo log
	*@description 	Funcion para guardar informacion en el archivo log
	*@paramas 		$texto
	*
	*/
	public function escribirLog($texto,$origen,$idUsuario){
		$path="public/Logs/logTareasWeb.txt";//ruta del archivo
		if(file_exists($path)){
			$file = fopen($path, "a");//se abre el archivo para escritura
			
			switch($origen){
				case "editarTarea":
					$datosTarea=explode("||",$texto);
					$datosIniciales=explode("???",$datosTarea[0]);
					$detalleTarea=explode("????",$datosTarea[1]);
					$datos=array("Id","Fecha creacion","Nombre Tarea","Descripcion de tarea","Direccion","Fecha Programada","id prioridad","Item number","Nombre cuestionario");
					$texto="\n*************************Inicio del Registro*************************\n\n";
					$texto.="Generado el: ".$this->regresarDatosFecha(date("Y-m-d"),date("H:i:s"))."\n\n";
					$texto.="Datos de la tarea\n==========================================\n";
					for($i=0;$i<count($datosIniciales);$i++){
						$texto.=$datos[$i]."=>".$datosIniciales[$i]."\n";
					}
					$texto.="\n\nDetalle de la tarea\n==========================================\n";
					$datosDetalle=array("Nombre","Estatus","Fecha Inicio","Fecha Fin");
					for($i=0;$i<count($detalleTarea);$i++){
						$texto.=$datosDetalle[$i]."=>".$detalleTarea[$i]."\n";
					}
					$texto.="\nUsuario que modifico => ".$idUsuario;
					$texto.="\n\n*************************Fin del Registro*************************\n";
					fwrite($file, $texto . PHP_EOL);
				break;
			}
			fclose($file);//se cierra el archivo
		}else{
			echo "<br>Error al encontrar el archivo";
		}
	}
	/**
	*@method 		eliminarUnidadAlerta
	*@descripction 	Funcion para eliminar un unidad de la alerta seleccionada
	*@params 		@idRegistro
	*/
	public function eliminarUnidadesAlerta($idRegistros,$noAlerta,$idUsuarioAlerta,$idCliente){
		$mensaje="";
		$objBDA=$this->iniciarConexionAlertas();
		$objBDA->sqlQuery("SET NAMES 'UTF8'");
		$idRegistros=explode(",,,",$idRegistros);
		for($i=0;$i<count($idRegistros);$i++){
			echo "<br>".$sql="DELETE FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_ENTITY='".$idRegistros[$i]."'";
			$res=$objBDA->sqlQuery($sql);
		}
		echo "<script type='text/javascript'> detalleAlerta('0#".$noAlerta."-0',0) 
			var current_index = $('#tabsAlertas').tabs('option','selected');//se obtiene el tab actual
			if(current_index==0){
				filtro='vigentes';
			}else if(current_index==1){
				filtro='activas';
			}else{
				filtro='inactivas';
			}
			cargarAlertas(filtro);

		 </script>";
	}
	/**
	*@method 		actualizarAlertasActDes
	*@description 	Funcion para activar / desactivar una o varias alertas
	*@paramas 				
	*
	*/
	public function actualizarAlertasActDes($idCliente,$idUsuario,$elementosActualizar){
		$mensaje="";
		$objBDA=$this->iniciarConexionAlertas();
		$objBDA->sqlQuery("SET NAMES 'utf8'");
		$elementosActualizar=explode(",",$elementosActualizar);
		foreach($elementosActualizar as $valorActualizar){
			//se extrae el identificador de la alerta
			$sqlIdAlerta="SELECT ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,ACTIVE FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER WHERE COD_ALERT_ENTITY='".$valorActualizar."'";
			$resIdAlerta=$objBDA->sqlQuery($sqlIdAlerta);
			if($objBDA->sqlEnumRows($resIdAlerta)!=0){
				$rowDatosAlerta=$objBDA->sqlFetchArray($resIdAlerta);//identificador de la alerta
				if($rowDatosAlerta["ACTIVE"]=="0"){
					$sqlAct="UPDATE ALERT_XP_MASTER SET ACTIVE='1' WHERE COD_ALERT_MASTER='".$rowDatosAlerta["COD_ALERT_MASTER"]."'";
				}else if($rowDatosAlerta["ACTIVE"]=="1"){
					$sqlAct="UPDATE ALERT_XP_MASTER SET ACTIVE='0' WHERE COD_ALERT_MASTER='".$rowDatosAlerta["COD_ALERT_MASTER"]."'";
				}
				$resAct=$objBDA->sqlQuery($sqlAct);//se ejecuta la funcion
				if($resAct){
					$mensaje=1;
				}else{
					$mensaje=0;
				}
			}else{
				$mensaje=0;
			}
		}
		return $mensaje;
	}
	/**
	*@method 		eliminarAlertas
	*@description 	Funcion para eliminar el detalle de las alertas
	*@paramas 				
	*
	*/
	public function eliminarAlertas($idCliente,$idUsuario,$elementosEliminar){
		$mensaje="";
		$objBDA=$this->iniciarConexionAlertas();
		$objBDA->sqlQuery("SET NAMES 'utf8'");
		$elementosEliminar=explode(",",$elementosEliminar);
		foreach($elementosEliminar as $valorEliminar){
			//se extrae el identificador de la alerta
			$sqlIdAlerta="SELECT COD_ALERT_MASTER FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_ENTITY='".$valorEliminar."'";
			$resIdAlerta=$objBDA->sqlQuery($sqlIdAlerta);
			if($objBDA->sqlEnumRows($resIdAlerta)!=0){
				$rowIdAlerta=$objBDA->sqlFetchArray($resIdAlerta);//identificador de la alerta
				//se verifica la cantidad de elementos a Eliminar
				$sqlContador="SELECT COUNT(*) AS TOTALDETALLES FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_MASTER='".$rowIdAlerta["COD_ALERT_MASTER"]."'";
				$resContador=$objBDA->sqlQuery($sqlContador);
				$rowContador=$objBDA->sqlFetchArray($resContador);
				$rowContador["TOTALDETALLES"];	
				//se evalua la operacion en la base de datos
				if($rowContador["TOTALDETALLES"]==1){
					//se inicia el proceso de eliminacion para el detalle de la alerta
					$sqlEliminarDetalle="DELETE FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_ENTITY='".$valorEliminar."'";
					$resEliminarDetalle=$objBDA->sqlQuery($sqlEliminarDetalle);
					if($resEliminarDetalle){
						//se elimina xp_email_master
						$sqlEliminarMailMaster="DELETE FROM ALERT_XP_EMAIL_MASTER WHERE COD_ALERT_ENTITY='".$valorEliminar."'";
						$resEliminarMailMaster=$objBDA->sqlQuery($sqlEliminarMailMaster);
						if($resEliminarMailMaster){
							//se elimina la alerta de la tabla principal
							$sqlEliminarMaster="DELETE FROM ALERT_XP_MASTER WHERE COD_ALERT_MASTER='".$rowIdAlerta["COD_ALERT_MASTER"]."'";
							$objBDA->sqlQuery($sqlEliminarMaster);
						}
					}
					
					$mensaje=1;
				}else{
					//se inicia el proceso de eliminacion para el detalle de la alerta
					$sqlEliminarDetalle="DELETE FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_ENTITY='".$valorEliminar."'";
					
					$resEliminarDetalle=$objBDA->sqlQuery($sqlEliminarDetalle);
					if($resEliminarDetalle){
						//se elimina xp_email_master
						$sqlEliminarMailMaster="DELETE FROM ALERT_XP_EMAIL_MASTER WHERE COD_ALERT_ENTITY='".$valorEliminar."'";
						$resEliminarMailMaster=$objBDA->sqlQuery($sqlEliminarMailMaster);
						if($resEliminarMailMaster){
							$mensaje=1;
						}
					}
				}
			}else{
				$mensaje=0;
			}
		}//recorrido del array para eliminar
		return $mensaje;
	}
	/**
	*@method 		mostrarDetalleAlerta
	*@description 	Funcion para mostrar el detalle de la alerta generada
	*@paramas 				
	*
	*/
	public function mostrarDetalleAlerta($idCliente,$idUsuario,$idAlerta){
		$mensaje="";
		$objBDA=$this->iniciarConexionAlertas();
		$objBDA->sqlQuery("SET NAMES 'utf8'");
		$sqlAD="SELECT * FROM ALERT_XP_MASTER WHERE COD_ALERT_MASTER='".$idAlerta."' AND COD_CLIENT='".$idCliente."'";
		$resAD=$objBDA->sqlQuery($sqlAD);
		if($objBDA->sqlEnumRows($resAD)==0){
			$mensaje="Sin Datos";
		}else{
			$rowAD=$objBDA->sqlFetchArray($resAD);
			$mensaje=$rowAD["COD_ALERT_MASTER"]."||".$rowAD["NAME_ALERT"]."||".$rowAD["HORARIO_FLAG_LUNES"]."||".$rowAD["HORARIO_FLAG_MARTES"]."||".$rowAD["HORARIO_FLAG_MIERCOLES"]."||".$rowAD["HORARIO_FLAG_JUEVES"]."||".$rowAD["HORARIO_FLAG_VIERNES"]."||".$rowAD["HORARIO_FLAG_SABADO"]."||".$rowAD["HORARIO_FLAG_DOMINGO"]."||".$rowAD["HORARIO_HORA_INICIO"]."||".$rowAD["HORARIO_HORA_FIN"]."||".$rowAD["TYPE_EXPRESION"]."||".$rowAD["ACTIVE"]."||".$rowAD["VIGENTE"]."||".$rowAD["COD_USER_CREATE"]."||".$rowAD["NICKNAME_USER_CREATE"]."||".$rowAD["FECHA_CREATE"]."||".$rowAD["ALARM_EXPRESION"]."?????";
			//SE EXTRAE EL DETALLE DE LA ALERTA
			$sqlADD="SELECT ID_OBJECT_MAP,COD_ENTITY,COD_ALERT_ENTITY FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_MASTER='".$rowAD["COD_ALERT_MASTER"]."'";
			$resADD=$objBDA->sqlQuery($sqlADD);
			while($rowADD=$objBDA->sqlFetchArray($resADD)){
				$desUnidad=$this->extraerNombreEntidad($rowADD["COD_ENTITY"]);
				$mensaje.=$rowADD["ID_OBJECT_MAP"]."|||".$rowADD["COD_ENTITY"]."|||".$desUnidad."|||".$rowADD["COD_ALERT_ENTITY"]."???";
			}
			//se extrae la informacion de los correos electronicos asociados a la alerta
			$sqlAE="SELECT CORREO_ELECTRONICO 
			FROM ALERT_XP_EMAIL_MASTER INNER JOIN ALERT_XP_EMAIL ON ALERT_XP_EMAIL_MASTER.COD_ALERT_XP_EMAIL=ALERT_XP_EMAIL.COD_ALERT_XP_EMAIL
			WHERE COD_ALERT_MASTER='".$rowAD["COD_ALERT_MASTER"]."'";
			$resAE=$objBDA->sqlQuery($sqlAE);
			$rowAE=$objBDA->sqlFetchArray($resAE);
			$mensaje.="?????".$rowAE["CORREO_ELECTRONICO"];
		}
		return $mensaje;
	}
	/**
	*@method 		mostrarUnidadesCliente
	*@description 	Funcion para mostrar las diferentes unidades de los cientes
	*@paramas 			
	*/
	public function mostrarUnidadesCliente($idCliente,$idUsuarioAlerta,$filtro,$origen,$alerta){
		$mensaje="";
		$objMovi=$this->iniciarConexionDb();
		$objMovi->sqlQuery("SET NAMES 'utf8'");
		if($filtro=="S/N"){
			if($origen=="nuevo"){
				$sqlG="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
	            FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO) INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
	            WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuarioAlerta."' ORDER BY NOMBRE,COD_ENTITY";
			}else if($origen=="agregar"){
				//se extraen las unidades de las alertas
				$sqlG="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
	            FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO) INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
	            WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuarioAlerta."' AND ADM_UNIDADES.COD_ENTITY NOT IN (".$this->extraerIdsUnidadesAlerta($alerta).") ORDER BY NOMBRE,COD_ENTITY";
			}
		}else{
			if($origen=="nuevo"){
				$sqlG="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
	            FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO) INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
	            WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuarioAlerta."' AND ADM_UNIDADES.DESCRIPTION LIKE '%".$filtro."%' ORDER BY NOMBRE,COD_ENTITY";
			}else if($origen=="agregar"){
				$sqlG="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
	            FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO) INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
	            WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuarioAlerta."' AND ADM_UNIDADES.DESCRIPTION LIKE '%".$filtro."%' AND ADM_UNIDADES.COD_ENTITY NOT IN (".$this->extraerIdsUnidadesAlerta($alerta).") ORDER BY NOMBRE,COD_ENTITY";
			}
		}
		$resG=$objMovi->sqlQuery($sqlG);
		if($objMovi->sqlEnumRows($resG)==0){
			$mensaje="S/N";
		}else{
			while($rowG=$objMovi->sqlFetchArray($resG)){
				if($mensaje==""){
					$mensaje=$rowG["ID_GRUPO"]."|||".$rowG["NOMBRE"]."|||".$rowG["COD_ENTITY"]."|||".$rowG["DESCRIPTION"];
				}else{
					$mensaje.="|||||".$rowG["ID_GRUPO"]."|||".$rowG["NOMBRE"]."|||".$rowG["COD_ENTITY"]."|||".$rowG["DESCRIPTION"];
				}
			}
		}
		return $mensaje;
	}
	private function extraerIdsUnidadesAlerta($alerta){
		$mensaje="";
		$objDb=$this->iniciarConexionAlertas();
		$objDb ->sqlQuery("SET NAMES 'utf8'");
		$sql="SELECT COD_ENTITY FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_MASTER='".$alerta."'";
		$res=$objDb->sqlQuery($sql);
		while($row=$objDb->sqlFetchArray($res)){
			($mensaje=="") ? $mensaje=$row["COD_ENTITY"] : $mensaje.=",".$row["COD_ENTITY"];
		}
		return $mensaje;
	}
	/**
	*@method 		mostrarCorreosCliente
	*@description 	Funcion para mostrar los correos electronicos del cliente seleccionado
	*@paramas 
	*/
	public function mostrarCorreosCliente($idCliente,$idUsuarioAlerta,$filtro){
		$mensaje="";
		$objDb=$this->iniciarConexionAlertas();
		$objDb ->sqlQuery("SET NAMES 'utf8'");
		if($filtro=="S/N"){
			//$sqlE="SELECT CORREO_ELECTRONICO FROM ALERT_XP_EMAIL WHERE COD_CLIENT='".$idCliente."'";
			$sqlE="SELECT CORREO_ELECTRONICO FROM ALERT_XP_EMAIL WHERE COD_CLIENT='".$idCliente."' GROUP BY CORREO_ELECTRONICO";
		}
		$resE=$objDb->sqlQuery($sqlE);
		if($objDb->sqlEnumRows($resE)==0){
			$mensaje="S/N";
		}else{
			$mails=array();
			while($rowE=$objDb->sqlFetchArray($resE)){
				$correos=explode(";",$rowE["CORREO_ELECTRONICO"]);
				foreach ($correos as $valor) {
					if(count($mails)==0){
						array_push($mails, $valor);
					}else{
						if(!in_array($valor, $mails)){//se busca si existe el mail en el array
							array_push($mails, $valor);
						}
					}
				}
			}
			$mensaje=implode(";",$mails);
		}
		return $mensaje;
	}
	/**
	*@method 			listarTareas
	*@description 	Funcion para mostrar las tareas en dus diferentes filtros
	*@paramas 		$filtro,$idCliente
	*
	*/
	public function listarAlertas($filtro,$idCliente,$idUsuario){
		if($filtro=="vigentes"){
			$sql="SELECT CONCAT(ALERT_XP_MASTER.COD_ALERT_MASTER,'-',COD_ALERT_ENTITY) AS CLAVE,ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,COD_ALERT_ENTITY,NAME_ALERT, IF(VIGENTE='N','NO','SI') AS VIGENTE,IF (ACTIVE = 1,'ACTIVA','NO ACTIVA') AS ACTIVE,IF(TYPE_EXPRESION='U','UNIDAD',(IF(TYPE_EXPRESION='P','PDI',(IF(TYPE_EXPRESION='G','GEOCERCA',(IF(TYPE_EXPRESION='R','RSI','N/A'))))))) AS TYPE_EXPRESION,DESCRIP_ENTITY,NICKNAME_USER_CREATE,FECHA_CREATE,CONCAT('Detalle') AS MAS
			FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER
			WHERE COD_CLIENT='".$idCliente."' AND VIGENTE='S'";
		}else if($filtro=="activas"){
			$sql="SELECT CONCAT(ALERT_XP_MASTER.COD_ALERT_MASTER,'-',COD_ALERT_ENTITY) AS CLAVE,ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,COD_ALERT_ENTITY,NAME_ALERT, IF(VIGENTE='N','NO','SI') AS VIGENTE,IF (ACTIVE = 1,'ACTIVA','NO ACTIVA') AS ACTIVE,IF(TYPE_EXPRESION='U','UNIDAD',(IF(TYPE_EXPRESION='P','PDI',(IF(TYPE_EXPRESION='G','GEOCERCA',(IF(TYPE_EXPRESION='R','RSI','N/A'))))))) AS TYPE_EXPRESION,DESCRIP_ENTITY,NICKNAME_USER_CREATE,FECHA_CREATE,CONCAT('Detalle') AS MAS
			FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER
			WHERE COD_CLIENT='".$idCliente."' AND ACTIVE='1'";
		}else if($filtro=="inactivas"){
			$sql="SELECT CONCAT(ALERT_XP_MASTER.COD_ALERT_MASTER,'-',COD_ALERT_ENTITY) AS CLAVE,ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,COD_ALERT_ENTITY,NAME_ALERT, IF(VIGENTE='N','NO','SI') AS VIGENTE,IF (ACTIVE = 1,'ACTIVA','NO ACTIVA') AS ACTIVE,IF(TYPE_EXPRESION='U','UNIDAD',(IF(TYPE_EXPRESION='P','PDI',(IF(TYPE_EXPRESION='G','GEOCERCA',(IF(TYPE_EXPRESION='R','RSI','N/A'))))))) AS TYPE_EXPRESION,DESCRIP_ENTITY,NICKNAME_USER_CREATE,FECHA_CREATE,CONCAT('Detalle') AS MAS
			FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER
			WHERE COD_CLIENT='".$idCliente."' AND ACTIVE='0'";
		}
		$conn=$this->iniciarConexionDbGrid();//conexion hacia la base de datos
		mysql_query("SET NAMES 'utf8'",$conn);// set your db encoding -- for ascent chars (if required)
		include "public/libs/phpgridv1.5.2/lib/inc/jqgrid_dist.php";
		//definicion de las columnas del grid
		$col = array();
		$col["title"] = "Alerta-#"; // caption of column
		$col["name"] = "CLAVE"; // grid column name, same as db field or alias from sql
		$col["dbname"] = "ALERT_XP_MASTER.COD_ALERT_MASTER";//campo en la base de datos si es de tipo [TABLA].[CAMPO]
		$col["width"] = "10"; // width on grid
		$col["align"] = "center";
		$col["sortable"] = true; // this column is not sortable 
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Nombre Alerta"; // caption of column
		$col["name"] = "NAME_ALERT"; // grid column name, same as db field or alias from sql
		$col["width"] = "25"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Vigente"; // caption of column
		$col["name"] = "VIGENTE"; // grid column name, same as db field or alias from sql
		$col["width"] = "7"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Activa"; // caption of column
		$col["name"] = "ACTIVE"; // grid column name, same as db field or alias from sql
		$col["width"] = "11"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Tipo de Expresión"; // caption of column
		$col["name"] = "TYPE_EXPRESION"; // grid column name, same as db field or alias from sql
		$col["width"] = "15"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "UNIDAD"; // caption of column
		$col["name"] = "DESCRIP_ENTITY"; // grid column name, same as db field or alias from sql
		$col["width"] = "20"; // width on grid
		$col["align"] = "left";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Creada por"; // caption of column
		$col["name"] = "NICKNAME_USER_CREATE"; // grid column name, same as db field or alias from sql
		$col["width"] = "20"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;

		$col = array();
		$col["title"] = "Fecha de Creación"; // caption of column
		$col["name"] = "FECHA_CREATE"; // grid column name, same as db field or alias from sql
		$col["width"] = "19"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "";
		$col["name"] = "MAS";
		$col["width"] = "7";
		$col["search"] = false;
		$col["editable"] = false;
		$col["sortable"] = false; // this column is not sortable 
		$col["align"] = "center";
		$col["link"] = "#{CLAVE}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["linkoptions"] = "title='Ver detalle de la alerta' onclick='detalleAlerta(this.href,this.event)'"; // extra params with <a> tag
		$cols[] = $col;
	
		$g = new jqgrid();//se instancia el objeto
		// parametros de configuracion
		//$grid["caption"] = "Alertas";
		$grid["multiselect"] 	= true;
		$grid["autowidth"] 		= true; // expand grid to screen width
		//$grid["resizable"] 		= true;
		$grid["altRows"] 		= true;
		$grid["altclass"] 		="alternarRegistros";
		$grid["scroll"] 		= false;
		$grid["sortorder"]		="desc";
		//$grid["rowNum"] 		= 10; // by default 20 
		$g->set_options($grid);
		$g->set_actions(array(  
                        "add"=>false,
                        "edit"=>false,
                        "delete"=>false,
                        "view"=>false,
                        "rowactions"=>false,
                        "export"=>false,
                        "autofilter" => true,
                        "search" => "advance",
                        "inlineadd" => false,
                        "showhidecolumns" => true
                    )
                );
		$g->table = "ALERT_XP_MASTER";// set database table for CRUD operations
		$g->set_columns($cols);
		$g->select_command = $sql;// comando SQL
		$out = $g->render("alertas".$filtro);// render grid
		echo $out;
		echo "<script type='text/javascript'> redimensionarAlertas(); </script>";
	}
	/**
	*@method 		extraerNombreEntity
	*@description 	Funcion para extraer el nombre del CODENTITY
	*@paramas 		$codEntity
	*
	*/
	public function extraerNombreEntidad($codEntity){
		$mensaje="";
		$objMovi=$this->iniciarConexionDb();
		$objMovi->sqlQuery("SET NAMES 'utf8'");
		$sqlDU="SELECT DESCRIPTION FROM ADM_UNIDADES WHERE COD_ENTITY='".$codEntity."'";
		$resDU=$objMovi->sqlQuery($sqlDU);
		if($objMovi->sqlEnumRows($resDU)==0){
			$descripcion="Informacion No Disponible";
		}else{
			$rowDU=$objMovi->sqlFetchArray($resDU);
			$descripcion=$rowDU["DESCRIPTION"];
		}
		return $descripcion;
	}
	/**
	*@method 		regresaDatosFecha
	*@description 	Funcion para formatear la fecha con dia de la semana y mes en modo legible
	*@paramas 		$fecha
	*
	*/
	public function regresaNombreUsuario($idUsuario){
		$mensaje="";
		$objDb=$this->iniciarConexionDb();
		$objDb ->sqlQuery("SET NAMES 'utf8'");
		$sqlNUsuario="SELECT NOMBRE_COMPLETO FROM ADM_USUARIOS WHERE ID_USUARIO='".$idUsuario."'";
		$resNUsuario=$objDb->sqlQuery($sqlNUsuario);
		$rowNUsuario=$objDb->sqlFetchArray($resNUsuario);
		return $rowNUsuario["NOMBRE_COMPLETO"];
	}
	/**
	*@method 		regresaDatosFecha
	*@description 	Funcion para formatear la fecha con dia de la semana y mes en modo legible
	*@paramas 		$fecha
	*
	*/
	public function regresarDatosFecha($fecha,$hora){
		$fechaB=explode("-",$fecha);						
		$diaSeg=date("w",mktime(0,0,0,$fechaB[1],$fechaB[2],$fechaB[0]));
		$mesSeg=date("n",mktime(0,0,0,$fechaB[1],$fechaB[2],$fechaB[0]));
		$dias= array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","S&aacute;bado");
		$meses= array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		if($hora==""){
			$textoFecha=$dias[$diaSeg]." ".substr($fechaB[2],0,2)." de ".$meses[$mesSeg-1]." de ".$fechaB[0];
		}else{
			$textoFecha=$dias[$diaSeg]." ".substr($fechaB[2],0,2)." de ".$meses[$mesSeg-1]." de ".$fechaB[0]." a las: ".$hora."";
		}
		return $textoFecha;
	}
/**
	*@method 		regresa datos de eventos
	*@description 	Funcion para formatear la fecha con dia de la semana y mes en modo legible
	*@paramas 		$fecha
	*
	*/
	public function regresarDatosEventos(){
		$arreglo_x = array();
		$conta_x = -1;
		$objDbE = $this->iniciarConexionDb();
		$objDbE ->sqlQuery("SET NAMES 'utf8'");
		$sql_events = "SELECT COD_EVENT,DESCRIPTION FROM ADM_EVENTOS WHERE PRIORITY = 1 AND FLAG_EVENT_ALERT = 1 ORDER BY COD_EVENT";
		$resEvents  = $objDbE->sqlQuery($sql_events);
		 while($row_evento = $objDbE->sqlFetchArray($resEvents)){
			 $conta_x = $conta_x+1;	
			$arreglo_x[$conta_x][0] = $row_evento['COD_EVENT'];
			$arreglo_x[$conta_x][1] = $row_evento['DESCRIPTION'];
	     }
		 return $arreglo_x;
	}

}//fin de la clase Cuestionarios

?>