
/*
*permite la captura masiva de correos electronicos
*/


//*************************************** funcion para cargar el dwt de expresiones
function expresionesAlertas() {
	    $.ajax({
          url: "index.php?m=mAlertas&c=mExpresiones",
          success: function(data) {
            var result = data; 
            $('#dialogo_generar_expresiones').html('');
            $('#dialogo_generar_expresiones').dialog('open');
			$('#dialogo_generar_expresiones').html(result); 
		  }
      });
	 
}

//*************************************** funcion para cargar el tipo PDI/GC/RSI

function tipoPDIgcRSI(tipes) {
	  /*  $.ajax({
          url: "index.php?m=mAlertas&c=mPDIgcRSI",
		   data : {
            tip_g:tipes
		   },
		  type: "POST",
          success: function(data) {
            var result = data; 
            $('#dialogo_generar_expresiones').html('');
            $('#dialogo_generar_expresiones').dialog('open');
			$('#dialogo_generar_expresiones').html(result); 
		  }
      });*/
	  
	  alert(tipes);
	 
}



var cajaEmail=0;
function agregarCajaCorreo(){
	alert("Hola");
	idCajaMail="cajaMail_"+cajaEmail;
	var cajaMail="<input type='text' id='"+idCajaMail+"' style='float:left;' />"
	$("#txtCorreoElectronico").append(cajaMail);
	$("#"+idCajaMail).focus();
}


function pruebaXXX(){
	alert("Prueba de la funcion");
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


	