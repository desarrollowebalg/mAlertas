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
	
	var sel = '<select style=" width:100px;">'+
              '<option>'+
               'Cargando'+
              '</option>'+
             '</select>';

	 $('#selec_tipo_pgr').html(sel);
		    $.ajax({
          url: "index.php?m=mAlertas&c=mPDIgcRSI",
		   data : {
            tip_g:tipes
		   },
		  type: "POST",
          success: function(data) {
            var result = data; 
       		$('#selec_tipo_pgr').html(result); 
		  }
      });
	  
	 // alert('ya se corrigio '+tipes);
	 
}

function liberarCerrar(iD,valor){

	if(valor === true){
		var b = false;
		$("#banSino").val( parseInt($("#banSino").val())+1);  
	}else{
		var b = true;
		$("#banSino").val( parseInt($("#banSino").val())-1);  
	}
	$("#"+iD).prop('disabled', b); 
	
}

//*************************************** funcion para validar expresiones
function validarExpersiones(){
	
   if(parseInt(($("#banSino").val()) ===0)){
	   alert('Debe de especificar una expresion, verifique!!');  
	   return false;
   }
		 
   if($("#velocidadEvento").is(':checked')) { 
        
         $("#txtVelocidad").prop('disabled', false); 
        if($("#txtVelocidad").val()===''){     
		   alert('Campo velocidad vacio, Verifique!!'); 
		    return false; 
		}
		 	
		
   } else{
	    $("#txtVelocidad").prop('disabled', true);
   }
   
   if($("#eEvento").is(':checked')) {  
      
	   $("#selEventos").prop('disabled', false);
        if($("#selEventos").val()==='-1'){     
		   alert('Elija un evento');  
		    return false;
		}
   } else{
	   $("#selEventos").prop('disabled', true);
   }
   
   if($("#prioridadEvento").is(':checked')) {  
                
		 $("#selPriori").prop('disabled', false);
		if($("#selPriori").val()==='-1'){     
		   alert('Elija una prioridad'); 
		    return false; 
		}
   } else{
	    $("#selPriori").prop('disabled', true);
   }
   
 /*  if($("#rd2").is(':checked') || $("#rd3").is(':checked') || $("#rd4").is(':checked')) {  
    	$("#banSino").val(1); 	
       
   } else{
      if($("#banSino").val()==='0'){   
	   alert('Debe de especificar la expresion a usar');  
	   return false;
	  }
   }*/
   
   
   
   
   
   
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
function mostrarUnidadesCliente(filtro){
	$("#agregarUnidades").dialog("open");
	idClienteAlerta=$("#idClienteAlertas").val();
    idUsuarioAlerta=$("#idUsuarioAlertas").val();
    parametros="action=mostrarUnidadesCliente&idCliente="+idClienteAlerta+"&idUsuarioAlerta="+idUsuarioAlerta+"&filtro="+filtro;
	ajaxAlertas("mostrarUnidadesCliente","controlador",parametros,"listadoUnidadesAlertas","listadoUnidadesAlertas","GET");
}
function buscarUnidadesCliente(){
	txtFiltro=$("#txtBuscarUnidadCliente").val();
	if(txtFiltro.length >=3 || txtFiltro != ""){
		//se llama a la funcion cargarUsuarios
		mostrarUnidadesCliente(txtFiltro);
	}else{
		mostrarUnidadesCliente('S/N');
	}
}
function agregarUnidadesSeleccionadas(){
	var elementos="";
	for (var i=0;i<document.frmListadoUnidades.elements.length;i++){
	 	if (document.frmListadoUnidades.elements[i].type=="checkbox"){
	 		if (document.frmListadoUnidades.elements[i].checked){				
	 			if (elementos=="")
	 				elementos=elementos+document.frmListadoUnidades.elements[i].value;
	 			else
	 				elementos=elementos+",,,"+document.frmListadoUnidades.elements[i].value;
	 		}	
	 	}
	}
	if(elementos==""){
		$("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Error, seleccione por lo menos una unidad para la alerta actual.</p>");
		$("#divMenssajesAlertas").dialog("open");
	}else{
		//alert("Unidades a asignar");
		$("#agregarUnidades").dialog( "close" );//se cierra el dialog de unidades
		arrayElementos=elementos.split(",,,");//separacion
		for(var i=0;i<arrayElementos.length;i++){
			unidadesAg=arrayElementos[i].split("|");
			//console.log("usuarioAg :"+unidadesAg);
			idDivUsuarios="idUnidadDiv_"+unidadesAg[0];
			//console.log("idDivUsuarios: "+idDivUsuarios);
			//se arma la estructura
			unidadesAgDiv="<div id='"+idDivUsuarios+"' class='estiloUsuariosAgregadosDiv'><a href='#' onclick='quitarUnidadesDiv("+unidadesAg[0]+")' title='Quitar Usuario de la tarea'><img src='./public/images/cross.png' border='0' /></a>&nbsp;"+unidadesAg[1]+"</div>";
			//console.log("usuariosDiv :"+unidadesAgDiv);
			//se verifica que no exista el usuario ya en el listado
			//console.log(divRespuesta);
			if ($('#'+idDivUsuarios).length==0){
 				//se agregan los usuarios al divResultado
				$("#txtUnidadesAsignadas").append(unidadesAgDiv);	
			}else{
				console.log("existe :"+idDivUsuarios);
			}
			unidadesAgDiv="";
		}
	}
}
/*
*Funcion para retirar un usuario del listado seleccionado 
*/
function quitarUnidadesDiv(idUsuarioAQuitar){
	idDivQuitar="idUnidadDiv_"+idUsuarioAQuitar;
	$("#"+idDivQuitar).remove();
}

	