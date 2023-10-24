var tabla;

//funcion que se ejecuta al inicio
function init(){
   mostrarform(false);
   listar();

   $("#formulario").on("submit",function(e){
   	guardaryeditar(e);
   })

   $("#imagenmuestra").hide();
//mostramos los permisos
$.post("../ajax/empresa.php?op=permisos&id=", function(r){
	$("#permisos").html(r);
});
}

//funcion limpiar
function limpiar(){
	$("#nombre-empresa").val("");
    $("#num_documento-empresa").val("");
	$("#direccion-empresa").val("");
	$("#telefono-empresa").val("");
	$("#email-empresa").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#idempresa").val("");
}

//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}else{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

//cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
	location.reload();
}

//funcion listar
function listar(){
	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
		],
		"ajax":
		{
			url:'../ajax/empresa.php?op=listar',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":5,//paginacion
		"order":[[0,"desc"]]//ordenar (columna, orden)
	}).DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e){
     e.preventDefault();//no se activara la accion predeterminada 
     $("#btnGuardar").prop("disabled",true);
     var formData=new FormData($("#formulario")[0]);

     $.ajax({
     	url: "../ajax/empresa.php?op=guardaryeditar",
     	type: "POST",
     	data: formData,
     	contentType: false,
     	processData: false,

     	success: function(datos){
     		bootbox.alert(datos);
     		mostrarform(false);
     		tabla.ajax.reload();
     	}
     });

     limpiar();
}

function mostrar(idempresa){
	$.post("../ajax/empresa.php?op=mostrar",{idempresa : idempresa},
		function(data,status)
		{
			data=JSON.parse(data);
			mostrarform(true);

			$("#nombre-empresa").val(data.nombre);
            $("#tipo_documento-empresa").val(data.tipo_documento);
            $("#tipo_documento-empresa").selectpicker('refresh');
            $("#num_documento-empresa").val(data.num_documento);
            $("#direccion-empresa").val(data.direccion);
            $("#telefono-empresa").val(data.telefono);
            $("#email-empresa").val(data.email);
            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src","../files/empresa/"+data.imagen);
            $("#imagenactual").val(data.imagen);
            $("#idempresa").val(data.idempresa);


		});
	$.post("../ajax/empresa.php?op=permisos&id="+idempresa, function(r){
	$("#permisos").html(r);
});
}


//funcion para desactivar
function desactivar(idempresa){
	bootbox.confirm("¿Esta seguro de desactivar este dato?", function(result){
		if (result) {
			$.post("../ajax/empresa.php?op=desactivar", {idempresa : idempresa}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idempresa){
	bootbox.confirm("¿Esta seguro de activar este dato?" , function(result){
		if (result) {
			$.post("../ajax/empresa.php?op=activar", {idempresa : idempresa}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}


init();