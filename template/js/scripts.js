var cajaEmail=0;//contador para las cajas de texto

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
/*
*Funcion para extraer las direcciones guardadas de los clientes
*/
function mostrarDireccionesCliente(filtro){
	$("#detalleAgregarCorreos").dialog("open");
	idClienteAlerta=$("#idClienteAlertas").val();
    idUsuarioAlerta=$("#idUsuarioAlertas").val();
    parametros="action=mostrarCorreosCliente&idCliente="+idClienteAlerta+"&idUsuarioAlerta="+idUsuarioAlerta+"&filtro=S/N";
	ajaxAlertas("mostrarCorreosCliente","controlador",parametros,"listadoUsuariosEmail","listadoUsuariosEmail","GET");
}
function buscarCorreosClientes(){
	correoE=$("#txtBuscarCorreosCliente").val();
	mostrarDireccionesCliente(correoE);
}
function agregarCorreosElectronicos(){
	var elementos="";
	for (var i=0;i<document.frmListadoEmailsAlertas.elements.length;i++){
	 	if (document.frmListadoEmailsAlertas.elements[i].type=="checkbox"){
	 		if (document.frmListadoEmailsAlertas.elements[i].checked){				
	 			if (elementos=="")
	 				elementos=elementos+document.frmListadoEmailsAlertas.elements[i].value;
	 			else
	 				elementos=elementos+",,,"+document.frmListadoEmailsAlertas.elements[i].value;
	 		}	
	 	}
	}
	if(elementos==""){
		$("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Error, seleccione por lo menos un correo electronico del listado.</p>");
		$("#divMenssajesAlertas").dialog("open");
	}else{
		posicionesMail=elementos.split(",,,");
		idCajaS="cajaMail_"+cajaEmail;		
		for(i=0;i<posicionesMail.length;i++){
			strDiv="<div id='"+idCajaS+"' class='destinatarios ui-corner-all' title='"+posicionesMail[i]+"'>";
	        strDiv+="<div class='destinatariosMail'>"+posicionesMail[i]+"</div>";
	        strDiv+="<div class='eliminarDestinatario'>";
	        strDiv+="<a href='#' onclick='eliminaCorreo(\""+idCajaS+"\")'><span class='ui-icon ui-icon-circle-close'></span></a>";
	        strDiv+="</div></div>";
	        $("#txtCorreoElectronico").prepend(strDiv);
	        strDiv="";
		}
		$("#detalleAgregarCorreos").dialog("close");
	}
}



	