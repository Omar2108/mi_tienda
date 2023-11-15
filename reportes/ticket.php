<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();

if (!isset($_SESSION['nombre'])) {
    echo "debe ingresar al sistema correctamente para visualizar el reporte";
} else {

    if ($_SESSION['ventas'] == 1) {

        # Incluyendo librerias necesarias #
        require "./code128.php";
        require_once "../modelos/Venta.php";
        require('../modelos/Consultas.php');

        $venta = new Venta();

        $pdf = new PDF_Code128('P', 'mm', array(80, 258));
        $pdf->SetMargins(4, 10, 4);
        $pdf->AddPage();

        $id = $_GET["id"];
        $vendedor = $_SESSION['nombre'];
        $idempresa = $_SESSION["idempresa"];

        $rspta = $venta->ventacabecera($id, $idempresa);

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

        # Encabezado y datos de la empresa #
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper($empresa)), 0, 'C', false);
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "NIT: " . $documento), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Direccion: " . $direccion), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $telefono), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Email: " . $email), 0, 'C', false);

        $pdf->Ln(1);
        $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
        $pdf->Ln(5);

        $fecha = $reg->fecha;

        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . $fecha), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Caja Nro: 1"), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cajero: " . $vendedor), 0, 'C', false);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Ticket Nro: " . $reg->serie_comprobante . " - " . $reg->num_comprobante)), 0, 'C', false);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Ln(1);
        $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cliente: " . $reg->cliente), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $reg->tipo_documento . ": " . $reg->num_documento), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $reg->telefono), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " . $reg->direccion), 0, 'C', false);

        $pdf->Ln(1);
        $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
        $pdf->Ln(3);

        # Tabla de productos #
        $pdf->Cell(10, 5, iconv("UTF-8", "ISO-8859-1", "Cant."), 0, 0, 'C');
        $pdf->Cell(19, 5, iconv("UTF-8", "ISO-8859-1", "Precio"), 0, 0, 'C');
        $pdf->Cell(15, 5, iconv("UTF-8", "ISO-8859-1", "Desc."), 0, 0, 'C');
        $pdf->Cell(28, 5, iconv("UTF-8", "ISO-8859-1", "Total"), 0, 0, 'C');

        $pdf->Ln(3);
        $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
        $pdf->Ln(3);



        /*----------  Detalles de la tabla  ----------*/

        $rsptad = $venta->ventadetalles($id);
        $cantidad = 0;
        while ($regd = $rsptad->fetch_object()) {
            $sub = number_format($regd->subtotal);

            $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", $regd->articulo), 0, 'C', false);
            $pdf->Cell(10, 4, iconv("UTF-8", "ISO-8859-1", $regd->cantidad), 0, 0, 'C');
            $pdf->Cell(19, 4, iconv("UTF-8", "ISO-8859-1", number_format($regd->precio_venta)), 0, 0, 'C');
            $pdf->Cell(19, 4, iconv("UTF-8", "ISO-8859-1", number_format($regd->descuento)), 0, 0, 'C');
            $pdf->Cell(28, 4, iconv("UTF-8", "ISO-8859-1", $sub), 0, 0, 'C');
            $pdf->Ln(4);
        }
        $pdf->Ln(7);

        /*---------- Fin Detalles de la tabla ----------*/



        $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');

        $pdf->Ln(5);

        # Impuestos & totales #
        $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
        $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "SUBTOTAL"), 0, 0, 'C');
        $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", number_format($reg->subtotal)), 0, 0, 'C');

        $pdf->Ln(5);

        $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
        $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "IVA"), 0, 0, 'C');
        $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", number_format($reg->impuesto)), 0, 0, 'C');

        $pdf->Ln(5);

        $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');

        $pdf->Ln(5);

        $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
        $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "TOTAL A PAGAR"), 0, 0, 'C');
        $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", number_format($reg->total_venta)), 0, 0, 'C');

        $pdf->Ln(5);


        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "*** Los precios de los productos incluyen iva. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"), 0, 'C', false);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 7, iconv("UTF-8", "ISO-8859-1", "¡Gracias por su compra!"), '', 0, 'C');

        $pdf->Ln(9);

        # Codigo de barras #
        $pdf->Code128(5, $pdf->GetY(), $reg->codigo_venta, 70, 20);
        $pdf->SetXY(0, $pdf->GetY() + 21);
        $pdf->SetFont('Arial', '', 14);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $reg->codigo_venta), 0, 'C', false);

        # Nombre del archivo PDF #
        $pdf->Output("TK-$reg->serie_comprobante-$reg->num_comprobante", 'I');
        $pdf->Output("C:/xampp/htdocs/mi_tienda/facturas/tickets/TK-$reg->serie_comprobante-$reg->num_comprobante.pdf", 'F');
    } else {
        echo "No tiene permiso para visualizar el reporte";
    }
}
