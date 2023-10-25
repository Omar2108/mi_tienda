var tabla;

//funcion que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	//cargamos los items al select cliente
	$.post("../ajax/venta.php?op=selectCliente", function (r) {
		$("#idcliente").html(r);
		$('#idcliente').selectpicker('refresh');
	});

}

//funcion limpiar
function limpiar() {

	$("#idcliente").val("");
	$("#cliente").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#impuesto").val("");

	$("#total_venta").val("");
	$(".filas").remove();
	$("#total").html("0");

	//obtenemos la fecha actual
	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear() + "-" + (month) + "-" + (day);
	$("#fecha_hora").val(today);

	//marcamos el primer tipo_documento
	$("#tipo_comprobante").val("0");
	$("#tipo_comprobante").selectpicker('refresh');


}

//funcion mostrar formulario
function mostrarform(flag) {
	limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();
		consecutivoVentas();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles = 0;
		$("#btnAgregarArt").show();


	} else {
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();

	}
}

//cancelar form
function cancelarform() {
	limpiar();
	mostrarform(false);
	location.reload();
}

//funcion listar
function listar() {
	tabla = $('#tbllistado').dataTable({
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
			url: '../ajax/venta.php?op=listar',
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 5,//paginacion
		"order": [[0, "desc"]]//ordenar (columna, orden)
	}).DataTable();
}

function listarArticulos() {
	tabla = $('#tblarticulos').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [

		],
		"ajax":
		{
			url: '../ajax/venta.php?op=listarArticulos',
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 5,//paginacion
		"order": [[0, "desc"]]//ordenar (columna, orden)
	}).DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e) {
	e.preventDefault();//no se activara la accion predeterminada 
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/venta.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			bootbox.alert(datos);
			mostrarform(false);
			listar();
		}
	});

	limpiar();
}

function mostrar(idventa) {
	$.post("../ajax/venta.php?op=mostrar", { idventa: idventa },
		function (data, status) {
			data = JSON.parse(data);
			mostrarform(true);

			$("#idcliente").val(data.idcliente);
			$("#idcliente").selectpicker('refresh');
			$("#serie_comprobante").val(data.serie_comprobante);
			$("#num_comprobante").val(data.num_comprobante);
			$("#fecha_hora").val(data.fecha);
			$("#impuesto").val(data.impuesto);
			$("#idventa").val(data.idventa);

			//ocultar y mostrar los botones
			$("#btnGuardar").hide();
			$("#btnCancelar").show();
			$("#btnAgregarArt").hide();
		});
	$.post("../ajax/venta.php?op=listarDetalle&id=" + idventa, function (r) {
		$("#detalles").html(r);
	});

}

function enviar(idventa) {
	$("#myModal-loading").modal("show");
	$.post("../ajax/venta.php?op=enviar", { idventa: idventa },
		function (data, status) {
			console.log(data);
			if (!data == "") {
				$("#myModal-loading").modal("hide");
				bootbox.alert(data);
			} else {
				$("#myModal-loading").modal("hide");
				bootbox.alert(data);
				console.log(data);
			}

		});

}


//funcion para desactivar
function anular(idventa) {
	bootbox.confirm("¿Esta seguro de desactivar este dato?", function (result) {
		if (result) {
			$.post("../ajax/venta.php?op=anular", { idventa: idventa }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

//declaramos variables necesarias para trabajar con las compras y sus detalles
var cont = 0;
var detalles = 0;

$("#btnGuardar").hide();

function agregarDetalle(idarticulo, articulo, precio_venta, impuesto, stock) {
	var cantidad = 1;
	var descuento = 0;

	if (idarticulo != "" && stock > 0) {
		var subtotal = cantidad * precio_venta;
		var fila = '<tr class="filas" id="fila' + cont + '">' +
			'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')">X</button></td>' +
			'<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
			'<td><input type="number" name="cantidad[]" id="cantidad[]" value="' + cantidad + '"></td>' +
			'<td><input type="number" name="precio_venta[]" id="precio_venta[]" value="' + precio_venta + '"></td>' +
			'<td><input type="number" name="descuento[]" value="' + descuento + '"></td>' +
			'<td><span id="impuesto' + cont + '" name="impuesto">' + impuesto + '</span></td>' +
			'<td><span id="subtotal' + cont + '" name="subtotal">' + subtotal + '</span></td>' +
			'<td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>' +
			'</tr>';
		cont++;
		detalles++;
		$('#detalles').append(fila);
		modificarSubtotales();

	} else {
		if (stock == 0 || stock < 0) {
			alert(`¡El Producto ${articulo}, se encuentra agotado!`);
		} else {
			alert("¡Error al ingresar el detalle, revisar las datos del articulo!");
		}

	}
}

function modificarSubtotales() {
	var cant = document.getElementsByName("cantidad[]");
	var prev = document.getElementsByName("precio_venta[]");
	var desc = document.getElementsByName("descuento[]");
	var sub = document.getElementsByName("subtotal");


	for (var i = 0; i < cant.length; i++) {
		var inpV = cant[i];
		var inpP = prev[i];
		var inpS = sub[i];
		var des = desc[i];


		inpS.value = (inpV.value * inpP.value) - des.value;
		document.getElementsByName("subtotal")[i].innerHTML = inpS.value;
	}

	calcularTotales();
}

function formatNumberES(n, d = 0) {
	n = new Intl.NumberFormat("es-ES").format(parseFloat(n).toFixed(d))
	if (d > 0) {
		// Obtenemos la cantidad de decimales que tiene el numero
		const decimals = n.indexOf(",") > -1 ? n.length - 1 - n.indexOf(",") : 0;

		// añadimos los ceros necesios al numero
		n = (decimals == 0) ? n + "," + "0".repeat(d) : n + "0".repeat(d - decimals);
	}
	return n;
}


function calcularTotales() {
	var sub = document.getElementsByName("subtotal");

	var total = 0.0;
	var totaliva = 0.0;
	var totalSub = 0.0;


	for (var i = 0; i < sub.length; i++) {
		var id = "impuesto" + i.toString();
		var iva = document.getElementById(id).innerHTML;
		var subtotal = document.getElementsByName("subtotal")[i].value;
		var indice1 = "1." + iva;
		var indice2 = "0." + iva;


		//total
		total += subtotal;

		console.log("total : " + total);

		//sub total sin iva
		totalSub = (total / Number(indice1).toFixed(2));

		//iva
		totaliva = (totalSub * Number(indice2).toFixed(2));
	}
	//subtotal
	$("#sub-total").html("$/ " + totalSub.toFixed(2));
	$("#subtotal_venta").val(totalSub.toFixed(2));

	//iva
	$("#iva_total").html("$/ " + totaliva.toFixed(2));
	$("#iva").val(totaliva);

	//total
	$("#total").html("$/ " + total);
	$("#total_venta").val(total);
	evaluar();
}



function evaluar() {

	if (detalles > 0) {
		$("#btnGuardar").show();
	}
	else {
		$("#btnGuardar").hide();
		cont = 0;
	}
}

function eliminarDetalle(indice) {
	$("#fila" + indice).remove();
	calcularTotales();
	detalles = detalles - 1;

}


function consecutivoVentas() {

	$.ajax({
			url: "../ajax/venta.php?op=consecutivoVenta",
			type: "GET",
		})
		.done(function (data) {
			data = JSON.parse(data);
			console.log(data);
			$("#serie_comprobante").val(data.num_serie);
			$("#num_comprobante").val(data.num_comprobante);
		})
		.fail(function (data) {
			alert("error: " + data);
		});

}

init();