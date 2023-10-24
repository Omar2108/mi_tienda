<?php
//activamos almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
	session_start();

if (!isset($_SESSION['nombre'])) {
	echo "debe ingresar al sistema correctamente para vosualizar el reporte";
} else {

	if ($_SESSION['ventas'] == 1) {

?>

		<html>

		<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<link rel="stylesheet" href="../public/css/ticket.css">
		</head>

		<body onload="window.print();">
			<?php
			// incluimos la clase venta
			require_once "../modelos/Venta.php";
			require('../modelos/Consultas.php');

			$venta = new Venta();
			$id = $_GET["id"];

			//en el objeto $rspta obtenemos los valores devueltos del metodo ventacabecera del modelo
			$rspta = $venta->ventacabecera($id);

			$reg = $rspta->fetch_object();

			$consulta = new Consultas();
			$idempresa = $_SESSION['idempresa'];

			$rpt = $consulta->infoempresa($idempresa);
			$regt = $rpt->fetch_object();

			//establecemos los datos de la empresa
			$empresa = $regt->nombre;
			$documento = $regt->num_documento;
			$direccion = $regt->direccion;
			$telefono = $regt->telefono;
			$email = $regt->email;
			?>
			<div class="zona_impresion">
				<!--codigo imprimir-->
				<br>
				<table border="0" align="center" width="300px">
					<tr>
						<td align="center">
							<!--mostramos los datos de la empresa en el doc HTML-->
							.::<strong> <?php echo $empresa; ?></strong>::.<br>
							<?php echo $documento; ?><br>
							<?php echo $direccion . ' - ' . $telefono; ?><br>
							<?php echo $email; ?>
						</td>
					</tr>
					<tr>
						<td align="center"><?php echo $reg->fecha; ?></td>
					</tr>
					<tr>
						<td align="center"></td>
					</tr>
					<tr>
						<!--mostramos los datos del cliente -->
						<td>Cliente: <?php echo $reg->cliente; ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $reg->tipo_documento . ": " . $reg->num_documento; ?>
						</td>
					</tr>
					<br>
					<tr>
						<td>
							Ticket N°: <?php echo $reg->serie_comprobante . " - " . $reg->num_comprobante; ?>
						</td>
					</tr>
					<br>
					<br>
				</table>
				<br>

				<!--mostramos lod detalles de la venta -->

				<table border="0" align="center" width="300px">
					<tr>
						<td>CANT.</td>
						<td>DESCRIPCION</td>
						<td align="right">IMPORTE</td>
					</tr>
					<tr>
						<td colspan="3">=============================================</td>
					</tr>
					<?php
					$rsptad = $venta->ventadetalles($id);
					$cantidad = 0;
					while ($regd = $rsptad->fetch_object()) {
						$sub = number_format($regd->subtotal);
						echo "<tr>";
						echo "<td>" . $regd->cantidad . "</td>";
						echo "<td>" . $regd->articulo . "</td>";
						echo "<td align='right'>$/. " . $sub . "</td>";
						echo "</tr>";
						$cantidad += $regd->cantidad;
					}

					?>
					<!--mostramos los totales de la venta-->
					<tr>
						<td>&nbsp;</td>
						<td align="right"><b>TOTAL:</b></td>
						<td align="right"><b>$/. <?php 
						echo number_format($reg->total_venta); ?>
						</b></td>
					</tr>
					<br>
					<tr>
						<td colspan="3">N° de articulos: <?php echo $cantidad; ?> </td>
					</tr>
					<br>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center">¡Gracias por su compra!</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><?php echo $empresa ?></td>
					</tr>
					<tr>
						<td colspan="3" align="center">Colombia</td>
					</tr>
				</table>
				<br>
			</div>
			<p>&nbsp;</p>
		</body>

		<script>
			
		</script>

		</html>



<?php

	} else {
		echo "No tiene permiso para visualizar el reporte";
	}
}


ob_end_flush();
?>