//declaraciones iniciales
$(document).ready(function(){
   	//declaraciones y llamado a funciones del modulo
    $("#tabsAlertas").tabs();
    //declaracion de eventos para los tabs
	$("#vigentes").click(function(){
    	cargarAlertas("vigentes");
    });    
    $("#activas").click(function(){
    	cargarAlertas("activas");
    });
	$("#inactivas").click(function(){
    	cargarAlertas("inactivas");
    });    
   	$("#agregarAlerta").dialog({
		autoOpen: false,
		height: 550,
		width: 600,
		modal: true,
		resizable: false,
		buttons: {
			Cancelar: function() {
				$("#agregarAlerta").dialog( "close" );
			},
			Agregar:function(){
				validarTodo();//se llama a la funcion para agregar los usuarios seleccionados
			}
		}
   	});
   	$("#agregarUnidades").dialog({
		autoOpen: false,
		height: 350,
		width: 400,
		modal: true,
		resizable: false,
		buttons: {
			Cancelar: function() {
				$("#agregarUnidades").dialog( "close" );
			},
			Agregar:function(){
				agregarUnidadesSeleccionadas();//se llama a la funcion para agregar las unidades seleccionadas
			}
		}
   	});
    //boton crear Alerta
    $("#crearAlerta").click(function(){
    	$("#agregarAlerta").dialog("open");
    	idClienteAlerta=$("#idClienteAlertas").val();
    	idUsuarioAlerta=$("#idUsuarioAlertas").val();
    	parametros="action=mostrarFormularioAlerta&idCliente="+idClienteAlerta+"&idUsuarioAlerta="+idUsuarioAlerta;
		ajaxAlertas("mostrarFormularioAlerta","controlador",parametros,"agregarAlerta","agregarAlerta","GET");
    })
    $("#dialogo_generar_expresiones").dialog({
		autoOpen: false,
		height: 200,
		width: 650,
		modal: true,
		buttons: {
			Cancelar: function() {
				$("#dialogo_generar_expresiones").dialog( "close" );
			},
			Agregar:function(){
				validarExpersiones();
			}
		}
   	});
    $("#detalleAgregarCorreos").dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		resizable: false,
		buttons: {
			Cancelar: function() {
				$("#detalleAgregarCorreos").dialog( "close" );
			},
			Agregar:function(){
				agregarCorreosElectronicos();//se llama a la funcion para agregar los emails seleccionados
			}
		}
    });
	$("#divMenssajesAlertas").dialog({//mensajes de error
		autoOpen: false,
		modal: true,
		resizable: false,
		buttons: {
			Cerrar: function() {
				$("#divMenssajesAlertas").dialog( "close" );
			}
		}
	});
	$("#detalleAlerta").dialog({//dialog detalle alerta
		autoOpen:false,
		height: 600,
		width: 680,
		resizable: false,
		buttons:{
			Cerrar:function(){
				$("#detalleAlerta").dialog("close");
			}
		}
	});
	$("#eliminarAlertas").dialog({//dialog eliminar alertas
		autoOpen: false,
		modal: true,
		resizable: false,
		buttons: {
			Cerrar: function() {
				$("#eliminarAlertas").dialog( "close" );
			}
		}
	});
	$("#opcEliminarV, #opcEliminarA, #opcEliminarI").click(function(){
		seleccionarElementos(0);
	});
	$("#opcActDesV, #opcActDesA, #opcActDesI").click(function(){
		seleccionarElementos(1);
	});
	$("#divConfirmacionesMensajesAlertas").dialog({
		autoOpen: false,
		height: 170,
		width: 350,
		modal: true,
		resizable: false,
		buttons: {
			Cancelar: function() {
				$("#divConfirmacionesMensajesAlertas").dialog( "close" );
				alertasEliminar.length=0;
				banderaAccion="";
			},
			Aceptar:function(){
				$("#divConfirmacionesMensajesAlertas").dialog("close");
				if(banderaAccion==0){
					enviaAccion(0);//se llama a la funcion para el proceso de eliminacion
				}else if(banderaAccion==1){
					enviaAccion(1);//se llama a la funcion para el proceso de eliminacion
				}
			}
		}
    });

    $("#ayudaAlerta").click(function(){
    	var horizontalPadding = 0;
		var verticalPadding = 0;
		$('<iframe id="frameAyudaAlertas" src="http://movi.2gps.net/manuals/videos.php?video=alertas&bar=1&atras=0&manual=1" />').dialog({
			title: 'Ayuda - Alertas',
			autoOpen: true,
			width: 597,
			height: 390,
			modal: true,
			resizable: false,
			autoResize: true,
			buttons: {
				Cerrar: function() {
					$("#frameAyudaAlertas").remove();
					$( this ).dialog( "close" );
				}
			},
			open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog).hide(); }
		}).width(600 - horizontalPadding).height(390 - verticalPadding);
    });

    $("#agregarCorreoElectronico").dialog({
		autoOpen: false,
		height: 170,
		width: 350,
		modal: true,
		resizable: false,
		buttons: {
			Cancelar: function() {
				$("#agregarCorreoElectronico").dialog( "close" );
			},
			Aceptar:function(){
				verificarMail();
			}
		}
    });

    $("#confirmacionEliminarUnidad").dialog({
    	autoOpen:false,
    	height: 140,
    	width:350,
    	modal:true,
    	resizable:false,
    	buttons:{
    		Cancelar:function(){
    			$("#confirmacionEliminarUnidad").dialog("close");
    			unidadAEliminar=0;
    		},
    		Aceptar:function(){
    			console.log("Eliminar unidad");
    			eliminarUnidadAlerta();
    		}
    	}
    });

    redimensionarAlertas();//se redimensionan los tabs
    cargarAlertas("vigentes");//peticion para listar las tareas
});
/*
*Funcion que controla las peticiones ajax
*/
function ajaxAlertas(accion,c,parametros,divCarga,divResultado,tipoPeticion){
	$.ajax({
		url: "index.php?m=mAlertas&c="+c,
		type: tipoPeticion,
		data: parametros,
		beforeSend:function(){ 
			$("#"+divCarga).show().html("Procesando Informacion ..."); 
		},
		success: function(data) {
			//$("#"+divResultado).html(data);
			controladorAccionesAlertas(accion,data,divResultado);
		}
	});
}
/*
*Funcion que administra las diferentes acciones de las respuestas de las peticiones
*/
function controladorAccionesAlertas(accion,data,divResultado){
	switch(accion){
		case "mostrarFormularioAlerta":
			$("#"+divResultado).show().html(data);
		break;
		case "mostrarCorreosCliente":
			$("#listadoUsuariosEmail").html(data);
		break;
		case "mostrarUnidadesCliente":
			$("#listadoUnidadesAlertas").html(data);
		break;
		case "cargarAlertas":
			$("#"+divResultado).html(data);
		break;
		case "detalleAlerta":
			$("#detalleAlerta").html(data);
		break;
		case "eliminarAlertasDetalle":
			//$("#eliminarAlertas").html(data);
			evaluaResultado(data,banderaAccion);
		break;
		case "activarDesactivarAlertas":
			//$("#eliminarAlertas").html(data);
			evaluaResultado(data,banderaAccion);
		break;
		case "eliminarUnidadAlerta":
			$("#"+divResultado).html(data);
		break;
		case "agregarUnidadesAlerta":
			$("#"+divResultado).html(data);
		break;
	}
}

/*
*Funcion para cargar los diferentes status de las tareas
*/
function cargarAlertas(filtro){
	if(filtro=="vigentes"){
		div="tabVigentes";
	}else if(filtro=="activas"){
		div="tabActivas";
	}else if(filtro=="inactivas"){
		div="tabInactivas";
	}
	idCliente=$("#idClienteAlertas").val();
    idUsuarioAlerta=$("#idUsuarioAlertas").val();
	parametros="action=cargarAlertas&filtro="+filtro+"&idCliente="+idCliente+"&idUsuarioAlertas="+idUsuarioAlerta;
	ajaxAlertas("cargarAlertas","controlador",parametros,div,div,"GET");
}
/*
*Funcion para redimensionar las capas de Tareas
*/
function redimensionarAlertas(){
	altoAlertas=$("#adm_content").height();
    $("#tabsAlertas").css("height",(altoAlertas-5)+"px");
    $("#tabAlertasVigentes, #tabAlertasActivas, #tabAlertasInactivas").css("height",(altoAlertas-50)+"px");
    if($("#gbox_alertasvigentes").length){
    	$("#gbox_alertasvigentes").css("height",(altoAlertas-80)+"px");
    	$("#gview_alertasvigentes").css("height",(altoAlertas-107)+"px");
    	$(".ui-jqgrid-bdiv").css("height",(altoAlertas-154)+"px");	
    }

}
