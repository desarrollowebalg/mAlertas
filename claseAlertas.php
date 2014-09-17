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
		$this->host=$config_bd['host'];
		$this->port=$config_bd['port'];
		$this->bname=$config_bd['bname'];
		$this->user=$config_bd['user'];
		$this->pass=$config_bd['pass'];
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
   		$objBd=new sql($this->host,$this->port,$this->bname,$this->user,$this->pass);
   		return $objBd;
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
	*@method 		listarCuestionariosFiltro
	*@description 	Funcion para mostrar los cuestionarios asociados al cliente para agregar a la tarea
	*@paramas 				
	*
	*/
	public function listarCuestionariosFiltro($idCliente,$idUsuario,$filtro){
		$mensaje="";
		$objDb=$this->iniciarConexionDb();
		$objDb ->sqlQuery("SET NAMES 'utf8'");	
		if($filtro=="N/A"){
			$sqlCuestionarios="SELECT ORDEN,ID_CUESTIONARIO,DESCRIPCION,ITEM_NUMBER,ACTIVO FROM CRM2_CUESTIONARIOS WHERE COD_CLIENT=".$idCliente." AND ACTIVO='S' ORDER BY ORDEN;";
		}else{
			$sqlCuestionarios="SELECT ORDEN,ID_CUESTIONARIO,DESCRIPCION,ITEM_NUMBER,ACTIVO FROM CRM2_CUESTIONARIOS WHERE COD_CLIENT=".$idCliente." AND ITEM_NUMBER LIKE '%".$filtro."%' OR DESCRIPCION LIKE '%".$filtro."%' AND ACTIVO='S' ORDER BY ORDEN;";	
		}
		//echo $sqlCuestionarios;
		$resultCuestionarios=$objDb->sqlQuery($sqlCuestionarios);
		if($objDb->sqlEnumRows($resultCuestionarios) > 0){
			//recorrer el listado de usuarios y devolver el array
			while($rowU=$objDb->sqlFetchArray($resultCuestionarios)){
				$mensaje[]=$rowU["ID_CUESTIONARIO"]."|".$rowU["ITEM_NUMBER"]."|".$rowU["DESCRIPCION"];
			}
		}else{
			$mensaje=0;
		}
		return $mensaje;
	}
	/**
	*@method 			listarTareas
	*@description 	Funcion para mostrar las tareas en dus diferentes filtros
	*@paramas 		$filtro,$idCliente
	*
	*/
	public function listarTareas($filtro,$idCliente){
		if($filtro=="today"){
			$sql="SELECT CONCAT('Editar') AS Editar,DSP2_TAREAS.ID_TAREA,FECHA_CAPTURA,NOMBRE_TAREA,FECHA_PROGRAMADA,DSP_ESTATUS.DESCRIPCION AS ESTATUS,DSP2_PRIORIDAD.DESCRIPCION AS PRIORIDAD,ADM_USUARIOS.NOMBRE_COMPLETO AS USUARIO,CONCAT('Ver más') AS Detalle,CONCAT('Eliminar') AS Eliminar FROM (((DSP2_TAREAS INNER JOIN DSP2_CAPTURA ON DSP2_TAREAS.ID_TAREA=DSP2_CAPTURA.ID_TAREA)INNER JOIN DSP_ESTATUS ON DSP2_CAPTURA.ID_STATUS=DSP_ESTATUS.ID_ESTATUS)INNER JOIN DSP2_PRIORIDAD ON DSP2_TAREAS.ID_PRIORIDAD=DSP2_PRIORIDAD.ID_PRIORIDAD)INNER JOIN ADM_USUARIOS ON DSP2_CAPTURA.ID_USUARIO=ADM_USUARIOS.ID_USUARIO WHERE DSP2_TAREAS.FECHA_PROGRAMADA = '".date("Y-m-d")."' AND DSP2_TAREAS.ID_CLIENTE='".$idCliente."'";
		}else if($filtro=="pendientes"){
			$sql="SELECT CONCAT('Editar') AS Editar,DSP2_TAREAS.ID_TAREA,FECHA_CAPTURA,NOMBRE_TAREA,FECHA_PROGRAMADA,DSP_ESTATUS.DESCRIPCION AS ESTATUS,DSP2_PRIORIDAD.DESCRIPCION AS PRIORIDAD,ADM_USUARIOS.NOMBRE_COMPLETO AS USUARIO,CONCAT('Ver más') AS Detalle,CONCAT('Eliminar') AS Eliminar FROM (((DSP2_TAREAS INNER JOIN DSP2_CAPTURA ON DSP2_TAREAS.ID_TAREA=DSP2_CAPTURA.ID_TAREA)INNER JOIN DSP_ESTATUS ON DSP2_CAPTURA.ID_STATUS=DSP_ESTATUS.ID_ESTATUS)INNER JOIN DSP2_PRIORIDAD ON DSP2_TAREAS.ID_PRIORIDAD=DSP2_PRIORIDAD.ID_PRIORIDAD)INNER JOIN ADM_USUARIOS ON DSP2_CAPTURA.ID_USUARIO=ADM_USUARIOS.ID_USUARIO WHERE DSP2_TAREAS.FECHA_PROGRAMADA > '".date("Y-m-d")."' AND DSP2_TAREAS.ID_CLIENTE='".$idCliente."'";
		}else if($filtro=="vencidas"){
			$sql="SELECT CONCAT('Editar') AS Editar,DSP2_TAREAS.ID_TAREA,FECHA_CAPTURA,NOMBRE_TAREA,FECHA_PROGRAMADA,DSP_ESTATUS.DESCRIPCION AS ESTATUS,DSP2_PRIORIDAD.DESCRIPCION AS PRIORIDAD,ADM_USUARIOS.NOMBRE_COMPLETO AS USUARIO,CONCAT('Ver más') AS Detalle,CONCAT('Eliminar') AS Eliminar FROM (((DSP2_TAREAS INNER JOIN DSP2_CAPTURA ON DSP2_TAREAS.ID_TAREA=DSP2_CAPTURA.ID_TAREA)INNER JOIN DSP_ESTATUS ON DSP2_CAPTURA.ID_STATUS=DSP_ESTATUS.ID_ESTATUS)INNER JOIN DSP2_PRIORIDAD ON DSP2_TAREAS.ID_PRIORIDAD=DSP2_PRIORIDAD.ID_PRIORIDAD)INNER JOIN ADM_USUARIOS ON DSP2_CAPTURA.ID_USUARIO=ADM_USUARIOS.ID_USUARIO WHERE DSP2_TAREAS.FECHA_PROGRAMADA < '".date("Y-m-d")."' AND DSP2_TAREAS.ID_CLIENTE='".$idCliente."'";
		}
		//echo $sql;
		//conexion hacia la base de datos
		$conn=$this->iniciarConexionDbGrid();
		// set your db encoding -- for ascent chars (if required)
		mysql_query("SET NAMES 'utf8'",$conn);
		include "public/libs/phpgridv1.5.2/lib/inc/jqgrid_dist.php";
		//definicion de las columnas del grid
		
		$col = array();
		$col["title"] = "# Tarea"; // caption of column
		$col["name"] = "ID_TAREA"; // grid column name, same as db field or alias from sql
		$col["dbname"] = "DSP2_TAREAS.ID_TAREA";
		$col["width"] = "9"; // width on grid
		$col["align"] = "center";
		$col["sortable"] = true; // this column is not sortable 
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Fecha de Creación"; // caption of column
		$col["name"] = "FECHA_CAPTURA"; // grid column name, same as db field or alias from sql
		$col["width"] = "20"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Nombre de la Tarea"; // caption of column
		$col["name"] = "NOMBRE_TAREA"; // grid column name, same as db field or alias from sql
		$col["width"] = "30"; // width on grid
		$col["align"] = "left";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Fecha programada"; // caption of column
		$col["name"] = "FECHA_PROGRAMADA"; // grid column name, same as db field or alias from sql
		$col["width"] = "18"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;
		
		$col = array();
		$col["title"] = "Estatus"; // caption of column
		$col["name"] = "ESTATUS"; // grid column name, same as db field or alias from sql
		$col["dbname"] = "DSP_ESTATUS.DESCRIPCION";
		$col["width"] = "10"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;

		$col = array();
		$col["title"] = "Prioridad"; // caption of column
		$col["name"] = "PRIORIDAD"; // grid column name, same as db field or alias from sql
		$col["dbname"] = "DSP2_PRIORIDAD.DESCRIPCION";
		$col["width"] = "15"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;

		$col = array();
		$col["title"] = "Responsable de la Tarea"; // caption of column
		$col["name"] = "USUARIO"; // grid column name, same as db field or alias from sql
		$col["dbname"] = "ADM_USUARIOS.NOMBRE_COMPLETO";
		$col["width"] = "20"; // width on grid
		$col["align"] = "center";
		$col["resizable"] = true;
		$col["search"] = true;
		$cols[] = $col;

		$col = array();
		$col["title"] = "";
		$col["name"] = "Detalle";
		$col["width"] = "7";
		$col["search"] = false;
		$col["editable"] = false;
		$col["sortable"] = false; // this column is not sortable 
		$col["align"] = "center";
		//$col["link"] = "http://localhost?id={ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["link"] = "#{ID_TAREA}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		$col["linkoptions"] = "title='Ver detalle de la tarea' onclick='detalleTarea(this.href,this.event)'"; // extra params with <a> tag
		$cols[] = $col;

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
		$g->table = "DSP2_TAREAS";
		$g->set_columns($cols);

		// subqueries are also supported now (v1.2)
		
		$g->select_command = $sql;
		// render grid
		$out = $g->render("tareas".$filtro);
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