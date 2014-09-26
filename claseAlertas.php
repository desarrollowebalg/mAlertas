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
   		$objBd=mysql_connect($this->host,$this->user,$this->pass);
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
			$mensaje=$rowAD["COD_ALERT_MASTER"]."||".$rowAD["NAME_ALERT"]."||".$rowAD["HORARIO_FLAG_LUNES"]."||".$rowAD["HORARIO_FLAG_MARTES"]."||".$rowAD["HORARIO_FLAG_MIERCOLES"]."||".$rowAD["HORARIO_FLAG_JUEVES"]."||".$rowAD["HORARIO_FLAG_VIERNES"]."||".$rowAD["HORARIO_FLAG_SABADO"]."||".$rowAD["HORARIO_FLAG_DOMINGO"]."||".$rowAD["HORARIO_HORA_INICIO"]."||".$rowAD["HORARIO_HORA_FIN"]."||".$rowAD["TYPE_EXPRESION"]."||".$rowAD["ACTIVE"]."||".$rowAD["VIGENTE"]."||".$rowAD["COD_USER_CREATE"]."||".$rowAD["NICKNAME_USER_CREATE"]."||".$rowAD["FECHA_CREATE"]."?????";
			//SE EXTRAE EL DETALLE DE LA ALERTA
			$sqlADD="SELECT * FROM ALERT_XP_DETAIL_VARIABLES WHERE COD_ALERT_MASTER='".$rowAD["COD_ALERT_MASTER"]."'";
			$resADD=$objBDA->sqlQuery($sqlADD);
			while($rowADD=$objBDA->sqlFetchArray($resADD)){
				$mensaje.=$rowADD["ID_OBJECT_MAP"]."|||".$rowADD["COD_ENTITY"]."|||".$rowADD["DESCRIP_ENTITY"];
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
	*
	*/
	public function mostrarUnidadesCliente($idCliente,$idUsuarioAlerta,$filtro){
		$mensaje="";
		$objMovi=$this->iniciarConexionDb();
		$objMovi->sqlQuery("SET NAMES 'utf8'");
		if($filtro=="S/N"){
			$sqlG="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
            FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO) 
		        INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
            WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuarioAlerta."' ORDER BY NOMBRE,COD_ENTITY";
		}else{
			$sqlG="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
            FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO) 
		        INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
            WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuarioAlerta."' AND ADM_UNIDADES.DESCRIPTION LIKE '%".$filtro."%' ORDER BY NOMBRE,COD_ENTITY";
		}
		//echo $sqlG;
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
	/**
	*@method 		mostrarCorreosCliente
	*@description 	Funcion para mostrar los correos electronicos del cliente seleccionado
	*@paramas 				
	*
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
			$sql="SELECT ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,NAME_ALERT, IF(VIGENTE='N','NO','SI') AS VIGENTE,IF (ACTIVE = 1,'ACTIVA','NO ACTIVA') AS ACTIVE,IF(TYPE_EXPRESION='U','UNIDAD',(IF(TYPE_EXPRESION='P','PDI',(IF(TYPE_EXPRESION='G','GEOCERCA',(IF(TYPE_EXPRESION='R','RSI','N/A'))))))) AS TYPE_EXPRESION,DESCRIP_ENTITY,NICKNAME_USER_CREATE,FECHA_CREATE,CONCAT('Detalle') AS MAS,COD_ALERT_ENTITY
			FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER
			WHERE COD_CLIENT='".$idCliente."' AND VIGENTE='S'";
		}else if($filtro=="activas"){
			$sql="SELECT ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,NAME_ALERT, IF(VIGENTE='N','NO','SI') AS VIGENTE,IF (ACTIVE = 1,'ACTIVA','NO ACTIVA') AS ACTIVE,IF(TYPE_EXPRESION='U','UNIDAD',(IF(TYPE_EXPRESION='P','PDI',(IF(TYPE_EXPRESION='G','GEOCERCA',(IF(TYPE_EXPRESION='R','RSI','N/A'))))))) AS TYPE_EXPRESION,DESCRIP_ENTITY,NICKNAME_USER_CREATE,FECHA_CREATE,CONCAT('Detalle') AS MAS,COD_ALERT_ENTITY
			FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER
			WHERE COD_CLIENT='".$idCliente."' AND ACTIVE='1'";
		}else if($filtro=="inactivas"){
			$sql="SELECT ALERT_XP_MASTER.COD_ALERT_MASTER AS COD_ALERT_MASTER,NAME_ALERT, IF(VIGENTE='N','NO','SI') AS VIGENTE,IF (ACTIVE = 1,'ACTIVA','NO ACTIVA') AS ACTIVE,IF(TYPE_EXPRESION='U','UNIDAD',(IF(TYPE_EXPRESION='P','PDI',(IF(TYPE_EXPRESION='G','GEOCERCA',(IF(TYPE_EXPRESION='R','RSI','N/A'))))))) AS TYPE_EXPRESION,DESCRIP_ENTITY,NICKNAME_USER_CREATE,FECHA_CREATE,CONCAT('Detalle') AS MAS,COD_ALERT_ENTITY
			FROM ALERT_XP_MASTER INNER JOIN ALERT_XP_DETAIL_VARIABLES ON ALERT_XP_MASTER.COD_ALERT_MASTER=ALERT_XP_DETAIL_VARIABLES.COD_ALERT_MASTER
			WHERE COD_CLIENT='".$idCliente."' AND ACTIVE='0'";
		}
		//echo $sql;
		//exit();
		//conexion hacia la base de datos
		$conn=$this->iniciarConexionDbGrid();
		// set your db encoding -- for ascent chars (if required)
		mysql_query("SET NAMES 'utf8'",$conn);
		include "public/libs/phpgridv1.5.2/lib/inc/jqgrid_dist.php";
		//definicion de las columnas del grid
		
		$col = array();
		$col["title"] = "# Alerta"; // caption of column
		$col["name"] = "COD_ALERT_MASTER"; // grid column name, same as db field or alias from sql
		$col["dbname"] = "ALERT_XP_MASTER.COD_ALERT_MASTER";
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
		$col["width"] = "10"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Activa"; // caption of column
		$col["name"] = "ACTIVE"; // grid column name, same as db field or alias from sql
		$col["width"] = "13"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Tipo de Expresión"; // caption of column
		$col["name"] = "TYPE_EXPRESION"; // grid column name, same as db field or alias from sql
		//$col["dbname"] = "DSP_ESTATUS.DESCRIPCION";
		$col["width"] = "15"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "UNIDAD"; // caption of column
		$col["name"] = "DESCRIP_ENTITY"; // grid column name, same as db field or alias from sql
		//$col["dbname"] = "DSP2_PRIORIDAD.DESCRIPCION";
		$col["width"] = "20"; // width on grid
		$col["align"] = "left";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Creada por"; // caption of column
		$col["name"] = "NICKNAME_USER_CREATE"; // grid column name, same as db field or alias from sql
		//$col["dbname"] = "ADM_USUARIOS.NOMBRE_COMPLETO";
		$col["width"] = "20"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;

		$col = array();
		$col["title"] = "Fecha de Creación"; // caption of column
		$col["name"] = "FECHA_CREATE"; // grid column name, same as db field or alias from sql
		//$col["dbname"] = "ADM_USUARIOS.NOMBRE_COMPLETO";
		$col["width"] = "14"; // width on grid
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
		//$col["link"] = "http://localhost?id={ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["link"] = "#{COD_ALERT_MASTER}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["linkoptions"] = "title='Ver detalle de la alerta' onclick='detalleAlerta(this.href,this.event)'"; // extra params with <a> tag
		$cols[] = $col;
		/*
		$col = array();
		$col["title"] = "";
		$col["name"] = "Editar";
		$col["width"] = "7";
		$col["search"] = false;
		$col["editable"] = false;
		$col["sortable"] = false; // this column is not sortable 
		$col["align"] = "center";
		//$col["link"] = "http://localhost?id={ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["link"] = "#{ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["linkoptions"] = "title='Editar Registro' onclick='editarTarea(this.href)'"; // extra params with <a> tag
		$cols[] = $col;

		$col = array();
		$col["title"] = "";
		$col["name"] = "Eliminar";
		$col["width"] = "7";
		$col["search"] = false;
		$col["editable"] = false;
		$col["sortable"] = false; // this column is not sortable 
		$col["align"] = "center";
		//$col["link"] = "http://localhost?id={ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["link"] = "#{ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["linkoptions"] = "title='Eliminar Registro' onclick='eliminarTarea(this.href)'"; // extra params with <a> tag
		$cols[] = $col;
		*/
		//se instancia el objeto
		$g = new jqgrid();
		// parametros de configuracion
		//$grid["caption"] = "Tareas";
		$grid["multiselect"] 	= false;
		$grid["autowidth"] 		= true; // expand grid to screen width
		$grid["resizable"] 		= true;
		$grid["altRows"] 		= true;
		$grid["altclass"] 		="alternarRegistros";
		$grid["scroll"] 		= false;
		$grid["height"] 		= "100%";
		$grid["sortorder"]		="desc";

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
                        "showhidecolumns" => false
                    )
                );

		// set database table for CRUD operations
		$g->table = "ALERT_XP_MASTER";
		$g->set_columns($cols);

		// subqueries are also supported now (v1.2)
		
		$g->select_command = $sql;
		// render grid
		$out = $g->render("alertas".$filtro);
		echo $out;
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

}//fin de la clase Cuestionarios

?>