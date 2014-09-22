var cajaEmail=0;//contador para las cajas de texto
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
	  
	  alert('ya se corrigio '+tipes);
	 
}
/*
*Funcion para agregar una caja de texto para introducir el correo electronico
*/
function agregarCajaCorreo(){
	idCajaMail="cajaMail_"+cajaEmail;
	cajaMail="<input type='text' id='"+idCajaMail+"' class='cajaMails' onkeypress='verificarMail(idCajaMail,event)' />"
	$("#txtCorreoElectronico").prepend(cajaMail);
	$("#"+idCajaMail).focus();
	cajaEmail+=1;
	cajaMail="";
}
/*
*Funcion para verificar el mail introducido
*/
function verificarMail(idCaja,evento){
	if(evento.which==13){
		//se recupera el correo electronico
		mailAValidar=$("#"+idCaja).val();
		if(mailAValidar==''){
			alert("Ingrese un email");
		}else if(validar_email(mailAValidar)==false){
			alert("El mail no es valido");
		}else{
			//si el mail es valido se elimina la caja de texto y se agrega un div con la opcion de eliminar el correo
			$("#"+idCaja).remove();
			if(mailAValidar.length > 23){
				mailAValidar=mailAValidar.substring(0,21)+"...";
			}
			strDiv="<div id='"+idCaja+"' class='destinatarios ui-corner-all' title='"+mailAValidar+"'>";
            strDiv+="<div class='destinatariosMail'>"+mailAValidar+"</div>";
            strDiv+="<div class='eliminarDestinatario'>";
            strDiv+="<a href='#' onclick='eliminaCorreo(\""+idCaja+"\")'><span class='ui-icon ui-icon-circle-close'></span></a>";
            strDiv+="</div></div>";
            $("#txtCorreoElectronico").prepend(strDiv);
            strDiv="";
		}
	}
}
/*
*Funcion para eliminar un destinatario de los existentes
*/
function eliminaCorreo(idCajaAEliminar){
	$("#"+idCajaAEliminar).remove();
}
/*
*Funcion para validar si el mail es valido
*/
function validar_email(valor){
	// creamos nuestra regla con expresiones regulares.
	var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
	// utilizamos test para comprobar si el parametro valor cumple la regla
	if(filter.test(valor))
		return true;
	else
		return false;
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


	