//declaraciones iniciales
$(document).ready(function(){
   	//declaraciones y llamado a funciones del modulo
    $("#tabsAlertas").tabs();

    //declaracion de eventos para los tabs
	$("#today").click(function(){
    	cargarTareas("today");
    });    

    $("#pendientes").click(function(){
    	cargarTareas("pendientes");
    });




	$("#vencidas").click(function(){
    	cargarTareas("vencidas");
    });    

   	$("#agregarAlerta").dialog({
		autoOpen: false,
		height: 500,
		width: 600,
		modal: true,
		buttons: {
			Cancelar: function() {
				$("#agregarAlerta").dialog( "close" );
			},
			Agregar:function(){
				//se llama a la funcion para agregar los usuarios seleccionados
			}
		}
   	});

   	$("#agregarUnidades").dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			Cancelar: function() {
				$("#agregarUnidades").dialog( "close" );
			},
			Agregar:function(){
				//se llama a la funcion para agregar los usuarios seleccionados
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
		ajaxTareas("mostrarFormularioAlerta","controlador",parametros,"agregarAlerta","agregarAlerta","GET");
    })
    


    $("#dialogo_generar_expresiones").dialog({
		autoOpen: false,
		height: 200,
		width: 600,
		modal: true,
		buttons: {
			Cancelar: function() {
				$("#dialogo_generar_expresiones").dialog( "close" );
			},
			Agregar:function(){
				//se llama a la funcion para agregar las expresiones
			}
		}
   	});


    //se redimensionan los tabs
    redimensionarAlertas();
    //peticion para listar las tareas
    //cargarTareas("today");
	//esto es nuevo cre
	//que no me lo reconoce
	

	
});

/*
*Funcion que controla las peticiones ajax
*/
function ajaxTareas(accion,c,parametros,divCarga,divResultado,tipoPeticion){
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
	}
}
/*
*Funcion para cargar los diferentes status de las tareas
*/
function cargarAlertas(filtro){
	if(filtro=="vigentes"){
		div="tabs-1";
	}else if(filtro=="activas"){
		div="tabs-2";
	}else if(filtro=="inactivas"){
		div="tabs-3";
	}
	idCliente=$("#idClienteTareas").val()
	parametros="action=cargarTareas&filtro="+filtro+"&idCliente="+idCliente;
	ajaxTareas("cargarTareas","controlador",parametros,div,div,"GET");
}
/*
*Funcion para redimensionar las capas de Tareas
*/
function redimensionarAlertas(){
	altoAlertas=$("#adm_content").height();
    $("#tabsAlertas").css("height",(altoAlertas-5)+"px");
}
