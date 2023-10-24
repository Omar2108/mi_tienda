<?php
require_once "../modelos/Venta.php";
require_once "../modelos/Persona.php";
require_once "../config/global.php";
require_once "../tcpdf/examples/tcpdf_include.php";
require_once "../tcpdf/tcpdf.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';


if (strlen(session_id()) < 1)
	session_start();

$venta = new Venta();


$mail = new PHPMailer(true);

$idventa = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";
$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$idusuario = $_SESSION["idusuario"];
$idempresa = $_SESSION["idempresa"];
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
$serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
$fecha_hora = isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";
$impuesto = isset($_POST["iva"]) ? limpiarCadena($_POST["iva"]) : "";
$subtotal = isset($_POST["subtotal_venta"]) ? limpiarCadena($_POST["subtotal_venta"]) : "";
$total_venta = isset($_POST["total_venta"]) ? limpiarCadena($_POST["total_venta"]) : "";
date_default_timezone_set('America/Bogota');
$hora = date("h:i:s");

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (empty($idventa)) {
			$rspta = $venta->insertar($idcliente, $idusuario, $idempresa, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora." " .$hora, $impuesto, $subtotal, $total_venta, $_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_venta"], $_POST["descuento"]);
			echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
		} else {
		}
		break;


	case 'anular':
		$rspta = $venta->anular($idventa);
		echo $rspta ? "Ingreso anulado correctamente" : "No se pudo anular el ingreso";
		break;


	case 'mostrar':
		$rspta = $venta->mostrar($idventa);
		echo json_encode($rspta);
		break;

	case 'enviar':

		$rsptacl = $venta->mostrar($idventa);
		$regcli = $rsptacl;

		$serial = $regcli['serie_comprobante'];
		$num = $regcli['num_comprobante'];

		try {
			//Server settings
			$mail->SMTPDebug = 0;                      //Enable verbose debug output
			$mail->isSMTP();
			$mail->CharSet = 'UTF-8';                                            //Send using SMTP
			$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = EMAIL;                     //SMTP username
			$mail->Password   = PASS_EMAIL;                               //SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
			$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			//Recipients
			$mail->setFrom(EMAIL, 'SisVentas');
			$mail->addAddress($regcli['email']);               //Name is optional;

			//Content
			$mail->isHTML(true);

			$mail->Subject = 'Envio de Factura de venta # ' . $num;
			$mail->Body    = '<p>Señor ' . $regcli['cliente'] . ', adjunto podra descargar su factura de venta Nº ' . $num . ' <br><strong>¡Gracias por su compra!</strong></br></p>';
			$mail->AltBody = 'Sistema de ventas - SisVentas';



			if ($regcli["tipo_comprobante"] == "Ticket") {

				$ruta_archivo = RUTA_GLOBAL . "/facturas/TK-$serial-$num.pdf";
			} else {
				$ruta_archivo = RUTA_GLOBAL . "/facturas/FV-$serial-$num.pdf";
			}


			//Attachments
			$mail->addAttachment($ruta_archivo);
			$mail->send();
			echo 'Envio realizado con exito';
		} catch (Exception $e) {
			echo "Error: {$mail->ErrorInfo}";
		}
		break;

	case 'listarDetalle':
		//recibimos el idventa
		$id = $_GET['id'];

		$rspta = $venta->listarDetalle($id);
		$total = 0;
		echo ' <thead style="background-color:#A9D0F5">
        <th>Opciones</th>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio Venta</th>
		<th>% Impuesto</th>
        <th>Descuento</th>
        <th>Subtotal</th>
       </thead>';
		while ($reg = $rspta->fetch_object()) {
			echo '<tr class="filas">
			<td></td>
			<td>' . $reg->nombre . '</td>
			<td>' . $reg->cantidad . '</td>
			<td>' . $reg->precio_venta . '</td>
			<td>' . $reg->descuento . '</td>
			<td>' . $reg->impuesto . '</td>
			<td>' . $reg->subtotal . '</td></tr>';
			$total = $total + ($reg->precio_venta * $reg->cantidad - $reg->descuento);
		}
		echo '<tfoot>
         <th>TOTAL</th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th><h4 id="total">$/ ' . $total . '</h4><input type="hidden" name="total_venta" id="total_venta"></th>
       </tfoot>';
		break;

	case 'listar':
		$rspta = $venta->listar();
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			if ($reg->tipo_comprobante == 'Ticket') {
				//$url = '../reportes/exTicket.php?id=';
				$url = '../reportes/ticket.php?id=';
			} else {
				$url = '../reportes/exFactura.php?id=';
			}


			$data[] = array(
				"0" => (($reg->estado == 'Aceptado') ? '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idventa . ')"><i class="fa fa-close"></i></button>' : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>') .
					'<button class="btn btn-sucess btn-xs" onclick="enviar(' . $reg->idventa . ')"><i class="fa fa-paper-plane-o"></i></button>' .
					'<a target="_blank" href="' . $url . $reg->idventa . '" > <button class="btn btn-info btn-xs"><i class="fa fa-file"></i></button></a>',
				"1" => $reg->fecha,
				"2" => $reg->cliente,
				"3" => $reg->usuario,
				"4" => $reg->tipo_comprobante,
				"5" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
				"6" => $reg->total_venta,
				"7" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' : '<span class="label bg-red">Anulado</span>'
			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);
		break;

	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'listarArticulos':
		require_once "../modelos/Articulo.php";
		$articulo = new Articulo();

		$rspta = $articulo->listarActivosVenta();
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-warning" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',' . $reg->precio_venta . ',' . $reg->impuesto . ',' . $reg->stock . ')"><span class="fa fa-plus"></span></button>',
				"1" => $reg->nombre,
				"2" => $reg->categoria,
				"3" => $reg->codigo,
				"4" => $reg->stock,
				"5" => $reg->impuesto,
				"6" => $reg->precio_venta,
				"7" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>"

			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;
	case 'selectTipoDocumento':

		$rsptas = $venta->getConsecutivoVentas($tipo_comprobante, $idempresa);

		$regt = $rsptas->fetch_object();

		$results = array(
			"num_serie" => $regt->num_serie,
			"num_comprobante" => $regt->num_comprobante
		);
		echo json_encode($results);
		break;
}
