
/*ja ya llego el nuevo git*/

function hey2(){
var f= 'hola';	
	alert(f);
}
//funciones para el modulo
/*
*Muestra los usuarios para ser seleccionados mediante el catalogo
*/
function cargarUsuarios(txtFiltro,origen){
	$("#accionTareaEditar").attr("value",origen);
	(txtFiltro=="N/A") ? filtro=txtFiltro : filtro=txtFiltro;
	idCliente=$("#idClienteTareas").val();
	idUsuario=$("#idUsuarioTareas").val();
	parametros="action=listarUsuarios&idCliente="+idCliente+"&idUsuario="+idUsuario+"&filtro="+filtro;
	//ajaxTareas(accion,c,parametros,divCarga,divResultado,tipoPeticion)
	ajaxTareas("listarUsuarios","controlador",parametros,"listadoUsuariosTareas","listadoUsuariosTareas","GET");
}
/*
*Muestra los cuestionarios para ser seleccionados mediante el catalogo
*/
function cargarCuestionarios(txtFiltro,origen){
	$("#accionTareaEditar").attr("value",origen);
	(txtFiltro=="N/A") ? filtro=txtFiltro : filtro=txtFiltro;
	idCliente=$("#idClienteTareas").val();
	idUsuario=$("#idUsuarioTareas").val();
	parametros="action=listarCuestionarios&idCliente="+idCliente+"&idUsuario="+idUsuario+"&filtro="+filtro;
	ajaxTareas("listarCuestionarios","controlador",parametros,"listadoCuestionarioTareas","listadoCuestionarioTareas","GET");
}
/*
*Funcion para recuperar el contenido de la caja de texto de las busqueda de usuarios
*/
function buscarUsuariosTareas(){
	txtFiltro=$("#txtBuscarUsuariosTarea").val();
	if(txtFiltro.length >=3 ){
		//se llama a la funcion cargarUsuarios
		cargarUsuarios(txtFiltro);
	}else{
		cargarUsuarios('N/A');
	}
}
/*
*Funcion para recuperar el contenido de la caja de texto de las busqueda de cuestionarios
*/
function buscarCuestionariosTareas(){
	txtFiltro=$("#txtBuscarCuestionariosTarea").val();
	if(txtFiltro.length >=1 ){
		//se llama a la funcion cargarUsuarios
		cargarCuestionarios(txtFiltro);
	}else{
		cargarCuestionarios('N/A');
	}
}


	