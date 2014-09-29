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
//******************************** funcion para cambiar valor de caja de pdi_in, out, on

function cambiaPDIinONout(dato){
	$("#parte3").val(dato);
}

function cambiaPDIcod(dato2){
	$("#parte2").val("ID_OBJECT_MAP="+dato2);
}

//********************************* funcion activa desactiva combos y textbox
function liberarCerrar(iD,valor){

	if(valor === true){
		var b = false;
		$("#banSino").val( parseInt($("#banSino").val())+1);  
	}else{
		var b = true;
		$("#banSino").val( parseInt($("#banSino").val())-1);  
	}
	$("#"+iD).prop('disabled', b); 
	  if(!$("#velocidadEvento").is(':checked') && !$("#eEvento").is(':checked') && !$("#prioridadEvento").is(':checked') ) { 
	       $("#parte4").val($("#parte4espejo").val());
	  }else{
		   $("#parte4").val("U");
	  }
}

//*************************************** funcion para validar expresiones
function validarExpersiones(){
	
	
if($("#chk_sino").is(':checked')) { 
     if($("#TiposPDIGCRSI").val() ==='-1'){
	     var msj_x = 'Debe elegir '+$("#parte6").val()+', Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open"); 
	   return false;
     }else{
		 //alert('crear expresion sin 3 checks'); 
		 
		 if($("#banSino").val() !== '0') { 
	      //alert('crear expresion con  3 checks'); 
		  
		    if($("#velocidadEvento").is(':checked')) { 
              $("#txtVelocidad").prop('disabled', false); 
				if($("#txtVelocidad").val()===''){     
				  var msj_x = 'Campo velocidad vacio, Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open"); 
					return false; 
				}
		   } else{
				$("#txtVelocidad").prop('disabled', true);
				$("#parte11").val('');
		   }
		   
		   if($("#eEvento").is(':checked')) {  
			  
			   $("#selEventos").prop('disabled', false);
				if($("#selEventos").val()==='-1'){     
				   var msj_x = 'Debe espesificar evento, Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open");  
					return false;
				}
		   } else{
			   $("#selEventos").prop('disabled', true);
			   $("#parte12").val('');
		   }
		   
		   if($("#prioridadEvento").is(':checked')) {  
						
				 $("#selPriori").prop('disabled', false);
				if($("#selPriori").val()==='-1'){     
				    var msj_x = 'Debe espesificar prioridad, Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open");
					return false; 
				}
		   } else{
				$("#selPriori").prop('disabled', true);
				$("#parte13").val('');
		   }
	
	    } 
	 }
   
}else{
   	
	if($("#banSino").val() === '0') {
		 
//	    alert('Debe de especificar las condiciones basicas de la alerta y/o agregar complementos');
				var msj_x = 'Debe de especificar las condiciones basicas de la alerta y/o incluir referencias';  
  	            $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open"); 
		return false;
	}else{
		    if($("#velocidadEvento").is(':checked')) { 
              $("#txtVelocidad").prop('disabled', false); 
				if($("#txtVelocidad").val()===''){     
				  // alert('Campo velocidad vacio, Verifique!!'); 
				var msj_x = 'Campo velocidad vacio, Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open"); 
					return false; 
				}
		   } else{
				$("#txtVelocidad").prop('disabled', true);
				$("#parte11").val('');
		   }
		   
		   if($("#eEvento").is(':checked')) {  
			  
			   $("#selEventos").prop('disabled', false);
				if($("#selEventos").val()==='-1'){     
				 //  alert('Elija un evento');  
				 var msj_x = 'Debe espesificar evento, Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open");   
					return false;
				}
		   } else{
			   $("#selEventos").prop('disabled', true);
			   $("#parte12").val('');
		   }
		   
		   if($("#prioridadEvento").is(':checked')) {  
						
				 $("#selPriori").prop('disabled', false);
				if($("#selPriori").val()==='-1'){     
				//   alert('Elija una prioridad');
				  var msj_x = 'Debe espesificar prioridad, Verifique!!';  
  			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+msj_x+"</p>");
				$("#divMenssajesAlertas").dialog("open");      
					return false; 
				}
		   } else{
				$("#selPriori").prop('disabled', true);
				$("#parte13").val('');
		   }
	}
	
}
	
   generaExpresiones();
}

//************************ funcion que genera las expresiones

function generaExpresiones(){
var resultado  = '';	
var resulatod2 = '';

	  if($("#velocidadEvento").is(':checked')) {
		   
		 if($("#parte11").val()==''){ 
		   $("#parte11").val('uni_velocidad>='+ $("#txtVelocidad").val() );
		   
	     }
		 resultado = '<p> velocidad='+ $("#txtVelocidad").val()+'</p>';
		 resulatod2 =  $("#parte11").val();
	  }
	  
	  if($("#eEvento").is(':checked')) { 
		 if($("#parte12").val()===''){ 
		   $("#parte12").val('uni_pk_event='+  $("#selEventos").val());
		 }
		 
		  resultado =  resultado +'<p> Evento= '+ $("#selEventos option:selected").html()+'</p>';
		if(resulatod2 == ''){ 
		     resulatod2  =  $("#parte12").val();
		}else{
			 resulatod2  = resulatod2 +' AND '+$("#parte12").val();
		}
	  }
	    
	   if($("#prioridadEvento").is(':checked')) { 
		 if($("#parte13").val()===''){ 
		   $("#parte13").val('uni_prio_event='+ $("#selPriori").val());
		 }
		  resultado =  resultado +'<p> Prioridad= '+ $("#selPriori option:selected").html()+'</p>';
		 
		if(resulatod2 == ''){ 
		    resulatod2 =  $("#parte13").val();
		}else{
			resulatod2 = resulatod2 +' AND '+$("#parte13").val();
		}
	  }
	  
	  if($("#chk_sino").is(':checked')) { 
	     resultado =  resultado +'<p>'+$("#parte6").val()+'='+$("#TiposPDIGCRSI option:selected").html() +' y validara la '+$("#parte5").val()+'</p>';
	      if(resulatod2 == ''){ 
		   resulatod2 =  $("#parte3").val();
		  }else{
			resulatod2 = resulatod2 +' AND '+$("#parte3").val();  
		  }
	  }
	  
 
    resulatod2 = resulatod2+'","'+  $("#parte4").val();
	  
	//alert(resultado);
	   $("#txtExpresionAlertas").html(resultado) ;  
	   $("#insertExpresion").val(resulatod2);
	   $("#insertUSUARIO").val($("#parte7").val());
	   $("#insertNOMBRE").val($("#parte8").val());
	   $("#dialogo_generar_expresiones").dialog( "close" );
}

//************************** funcion que valida antes de inserts
function validarTodo(){
 var valorRegreso = 1;
 var mensajez = ''; 
 
  if($("#txtNombreAlerta").val()==='' ){
	  mensajez = 'No se ha definido nombre de alerta, verifique!!';
	  $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+mensajez+"</p>");
      $("#divMenssajesAlertas").dialog("open");
	   return false;
  }
 
  if($("#txtCorreoElectronico").find('div').length===1){
	   mensajez = 'No se ha definido correo(s) electronico(s), verifique!!';
	  $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+mensajez+"</p>");
      $("#divMenssajesAlertas").dialog("open");
	   return false;
  }
//  console.log($("#txtCorreoElectronico").find('div').length);


  if( $('#txtExpresionAlertas').is(':empty')){
	   mensajez = 'No se ha definido una Expresión, verifique!!';
	  $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+mensajez+"</p>");
      $("#divMenssajesAlertas").dialog("open");
	   return false;
  }

  if(!$("#chkLunes").is(':checked') && !$("#chkMartes").is(':checked') && !$("#chkMiercoles").is(':checked') && !$("#chkJueves").is(':checked') && !$("#chkViernes").is(':checked') && !$("#chkSabado").is(':checked') && !$("#chkDomingo").is(':checked')){
 	   mensajez = 'No se ha definido un(os) día(s) , verifique!!';
	  $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+mensajez+"</p>");
      $("#divMenssajesAlertas").dialog("open");
	   return false;
  }
  
   if( $('#txtUnidadesAsignadas').is(':empty')){
	   mensajez = 'No se ha definido unidad(es), verifique!!';
	  $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+mensajez+"</p>");
      $("#divMenssajesAlertas").dialog("open");
	   return false;
  }
   
   crearInsert();
}



/************************** funcion que crea los inserts *******/
function crearInsert(){
	//alert('g');
	var insert = 's';
	var correos = '';
	var unidades = '';
	var partes ='';
	
	
		insert = '"'+$("#txtNombreAlerta").val()+'"';
	
	   
	   if($("#chkVigente").is(':checked')) {			// vigente
		  var vig = 1;
	   }else{
		  var vig = 0; 
	   }
	   
	   insert = insert +',"'+vig+'"';
	   
	   if($("#chkActiva").is(':checked')) {				// activa
		  var act = 'S';
	   }else{
		  var act = 'N'; 
	   }
	   insert = insert +',"'+ act +'"';
	   
	   $("#txtCorreoElectronico div").each(function (index) {
		     if(($( this ).attr( "title" ))!==undefined){
				  if(correos===''){
					  correos = $( this ).attr( "title" );
				  }else{
					  correos = correos+';'+$( this ).attr( "title" );
				  }
			 }
		     
	   })
	    //insert = insert +','+ correos;
	    insert = insert +',"'+ $("#insertExpresion").val()+'"';
		
	    if($("#chkLunes").is(':checked') ) {				// lunes
		  var lunes = 1;
	    }else{
		  var lunes = 0; 
	    }

       insert = insert +','+ lunes;
	   
		if($("#chkMartes").is(':checked') ) {				// martes
		  var martes = 1;
	    }else{
		  var martes = 0; 
	    }		

	  insert = insert +','+ martes;
	
		if($("#chkMiercoles").is(':checked') ) {				// martes
		  var miercoles = 1;
	    }else{
		  var miercoles = 0; 
	    }		
	 insert = insert +','+ miercoles;

		if($("#chkJueves").is(':checked') ) {				// martes
		  var jueves = 1;
	    }else{
		  var jueves = 0; 
	    }		
	 insert = insert +','+ jueves;

		if($("#chkViernes").is(':checked') ) {				// martes
		  var viernes = 1;
	    }else{
		  var viernes = 0; 
	    }		
	 insert = insert +','+ viernes;

		if($("#chkSabado").is(':checked') ) {				// martes
		  var sabado = 1;
	    }else{
		  var sabado = 0; 
	    }		
	 insert = insert +','+ sabado;

		if($("#chkDomingo").is(':checked') ) {				// martes
		  var domingo = 1;
	    }else{
		  var domingo = 0; 
	    }		
		  
		  insert = insert +','+ domingo;
		 
		  insert = insert +',"'+$("#hrInicio").val()+':'+$("#mnInicio").val()+':00","'+$("#hrFin").val()+':'+$("#mnFin").val()+':00"';
		 
		  insert = insert +',"'+$("#insertUSUARIO").val()+'","'+$("#insertNOMBRE").val()+'"';

          insert = insert +'|'+correos;
		  
		 $("#txtUnidadesAsignadas div").each(function (index) {   /// div de unidades
		     if(($( this ).attr( "id" ))!==undefined){
				partes = $( this ).attr( "id" ).split('_');
				   if(unidades===''){
					  unidades = partes[1];
				  }else{
					  unidades = unidades+','+partes[1];
				  }
				  //console.log($( this ).attr( "id" ))
			 }
		     
	   })  
		  
		   insert = insert +'|'+unidades+'|'+$("#parte2").val();

		console.log(insert);	
	
  $.ajax({
          url: "index.php?m=mAlertas&c=mGuardarNuevoEditar",
		   data : {
            cadena:insert,
			alertaG:$("#alertaGenerada").val()
		   },
		  type: "POST",
          success: function(data) {
            var result = data.split(','); 
			$("#agregarAlerta").dialog( "close" );
			  if(result[0]==='1'){
				    var mensaje_x = result[1];
			  }else{
				    var mensaje_x = result[1];
			  }
			  
			  var current_index = $("#tabsAlertas").tabs("option","selected");
				if(current_index==0){
					filtro="vigentes";
				}else if(current_index==1){
					filtro="activas";
				}else{
					filtro="inactivas";
				}
				cargarAlertas(filtro);
			  
			    $("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 20px 0;'></span>"+mensaje_x+"</p>");
				$("#divMenssajesAlertas").dialog("open");
       		//alert('Alerta guardada con exito'); 
			console.log(result[0]+''+result[1]);
		  }
      });

}
//********************************

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
			$("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Error, ingrese un correo electronico.</p>");
			$("#divMenssajesAlertas").dialog("open");
		}else if(validar_email(mailAValidar)==false){
			$("#divMenssajesAlertas").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Error, el correo electronico no es valido.</p>");
			$("#divMenssajesAlertas").dialog("open");
		}else{
			//si el mail es valido se elimina la caja de texto y se agrega un div con la opcion de eliminar el correo
			$("#"+idCaja).remove();
			mailOriginal=mailAValidar;
			if(mailAValidar.length > 23){
				mailAValidar=mailAValidar.substring(0,21)+"...";
			}
			strDiv="<div id='"+idCaja+"' class='destinatarios ui-corner-all' title='"+mailOriginal+"'>";
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
			if(posicionesMail[i].length > 23){
				mailAdd=posicionesMail[i].substring(0,21)+"...";
			}else{
				mailAdd=posicionesMail[i];
			}
			strDiv="<div id='"+idCajaS+"' class='destinatarios ui-corner-all' title='"+posicionesMail[i]+"'>";
	        strDiv+="<div class='destinatariosMail'>"+mailAdd+"</div>";
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
/*
*Funcion para mostrar el detalle de la Alerta
*/
function detalleAlerta(idAlerta,e){
	idAlerta=idAlerta.split("#");
	//alert("Detalle "+idAlerta[1]);
	$("#detalleAlerta").dialog("open");
	idClienteAlerta=$("#idClienteAlertas").val();
    idUsuarioAlerta=$("#idUsuarioAlertas").val();
	parametros="action=detalleAlerta&idCliente="+idClienteAlerta+"&idUsuario="+idUsuarioAlerta+"&idAlerta="+idAlerta[1];
	//ajaxTareas(accion,c,parametros,divCarga,divResultado,tipoPeticion)
	ajaxAlertas("detalleAlerta","controlador",parametros,"detalleAlerta","detalleAlerta","GET");
}
/*
*Funcion para seleccionar todas la unidades o quitar la seleccion de los checks
*/
function seleccionarTodas(opcion){
	var elementos="";
	for (var i=0;i<document.frmListadoUnidades.elements.length;i++){
	 	if (document.frmListadoUnidades.elements[i].type=="checkbox"){
	 		if(opcion==1){//se seleccionan todas
				document.frmListadoUnidades.elements[i].checked=1;
			}else{//se quita la seleccion
				document.frmListadoUnidades.elements[i].checked=0;
			}	
	 	}
	}
}
function seleccionarTodosCorreos(opcion){
	var elementos="";
	for (var i=0;i<document.frmListadoEmailsAlertas.elements.length;i++){
	 	if (document.frmListadoEmailsAlertas.elements[i].type=="checkbox"){
	 		if(opcion==1){//se seleccionan todas
				document.frmListadoEmailsAlertas.elements[i].checked=1;
			}else{//se quita la seleccion
				document.frmListadoEmailsAlertas.elements[i].checked=0;
			}	
	 	}
	}
}
/*
*Funcion para eliminar el detail de la alerta
*/
function eliminarElementos(){
	var current_index = $("#tabsAlertas").tabs("option","selected");
	if(current_index==0){
		filtro="vigentes";
	}else if(current_index==1){
		filtro="activas";
	}else{
		filtro="inactivas";
	}
	/*
	$("input:checkbox:checked").each(function(){
		//cada elemento seleccionado
		//alert($(this).val());
		cadE=$(this).attr("id");
		alert(cadE);
		if(cadE != undefined){
			cadE=cadE.split("_");
			alert(cadE[2]);
		}
		
	});
	*/
}