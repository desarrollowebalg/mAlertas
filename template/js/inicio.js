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
		buttons: {
			Cancelar: function() {
				$("#agregarAlerta").dialog( "close" );
			},
			Agregar:function(){
				validarTodo();
				
				//se llama a la funcion para agregar los usuarios seleccionados
			}
		}
   	});

   	$("#agregarUnidades").dialog({
		autoOpen: false,
		height: 350,
		width: 400,
		modal: true,
		buttons: {
			Cancelar: function() {
				$("#agregarUnidades").dialog( "close" );
			},
			Agregar:function(){
				//se llama a la funcion para agregar las unidades seleccionadas
				agregarUnidadesSeleccionadas();
			}
		}
   	});

    //boton crear Tarea
    $("#crearAlerta").click(function(){
    	$("#agregarAlerta").dialog("open");
    	//$("#divNuevaTarea").dialog("open");
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
				//se llama a la funcion para agregar las expresiones
			}
		}
   	});

    $("#detalleAgregarCorreos").dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			Cancelar: function() {
				$("#detalleAgregarCorreos").dialog( "close" );
			},
			Agregar:function(){
				//se llama a la funcion para agregar los emails seleccionados
				agregarCorreosElectronicos();
			}
		}
    });

    //mensajes de error
	$("#divMenssajesAlertas").dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Cerrar: function() {
				$("#divMenssajesAlertas").dialog( "close" );
			}
		}
	});

	//dialog detalle tarea
	$("#detalleAlerta").dialog({
		autoOpen:false,
		height: 500,
		width: 600,
		buttons:{
			Cerrar:function(){
				$("#detalleAlerta").dialog("close");
			}
		}
	});

    //se redimensionan los tabs
    redimensionarAlertas();
    //peticion para listar las tareas
    cargarAlertas("vigentes");
	//esto es nuevo cre
	//que no me lo reconoce
	

	
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
			controladorAcciones(accion,data,divResultado);
		}
	});
}
/*
*Funcion que administra las diferentes acciones de las respuestas de las peticiones
*/
function controladorAcciones(accion,data,divResultado){
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
}
