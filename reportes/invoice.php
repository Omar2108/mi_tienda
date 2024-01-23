<?php
//activamos almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
    session_start();

if (!isset($_SESSION['nombre'])) {
    echo "debe ingresar al sistema correctamente para visualizar el reporte";
} else {

    if ($_SESSION['ventas'] == 1) {


        //incluimos el archivo factura
        require "./code128.php";
        require('../modelos/Consultas.php');

        $consulta = new Consultas();
        $idempresa = $_SESSION['idempresa'];

        //establecemos los datos de la empresa

        $rpt = $consulta->infoempresa($idempresa);
        $reg = $rpt->fetch_object();

        $logo = $reg->imagen;
        $empresa = $reg->nombre;
        $documento = $reg->num_documento;
        $direccion = $reg->direccion;
        $telefono = $reg->telefono;
        $email = $reg->email;

        //obtenemos los datos de la cabecera de la venta actual
        require_once "../modelos/Venta.php";
        $venta = new Venta();
        $rsptav = $venta->ventacabecera($_GET["id"], $idempresa);

        //recorremos todos los valores que obtengamos
        $regv = $rsptav->fetch_object();

        //configuracion de la factura
        $pdf = new PDF_Code128('P', 'mm', 'Letter');
        $pdf->SetMargins(17, 17, 17);
        $pdf->AddPage();
        $pdf->Image($logo, 140, 12, 65, 40, 'PNG');

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(32, 100, 210);
        $pdf->Cell(150, 10, iconv("UTF-8", "ISO-8859-1", strtoupper($empresa)), 0, 0, 'L');

        $pdf->Ln(9);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(39, 39, 51);
        $pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", "NIT: " . $documento), 0, 0, 'L');

        $pdf->Ln(5);

        $pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", "Direccion: " . $direccion), 0, 0, 'L');

        $pdf->Ln(5);

        $pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $telefono), 0, 0, 'L');

        $pdf->Ln(5);

        $pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", "Email: " . $email), 0, 0, 'L');

        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", 'Fecha de emisión:'), 0, 0);
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(116, 7, iconv("UTF-8", "ISO-8859-1", date("d/m/Y", strtotime($regv->fecha))), 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(39, 39, 51);
        $pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", strtoupper('Factura Nro.')), 0, 0, 'C');

        $pdf->Ln(7);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(12, 7, iconv("UTF-8", "ISO-8859-1", 'Cajero:'), 0, 0, 'L');
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(134, 7, iconv("UTF-8", "ISO-8859-1", $_SESSION["nombre"]), 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", strtoupper($regv->serie_comprobante . "-" . $regv->num_comprobante)), 0, 0, 'C');

        $pdf->Ln(7);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", 'Forma de pago:'), 0, 0);
        $pdf->SetTextColor(97, 97, 97);
        $pdf->SetFont('Arial','', 10);
        $pdf->Cell(120, 8, iconv("UTF-8", "ISO-8859-1", $regv->forma_pago), 0, 0);


        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(39, 39, 51);
        $pdf->Cell(13, 7, iconv("UTF-8", "ISO-8859-1", 'Cliente:'), 0, 0);
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(60, 7, iconv("UTF-8", "ISO-8859-1", $regv->cliente), 0, 0, 'L');
        $pdf->SetTextColor(39, 39, 51);
        $pdf->Cell(8, 7, iconv("UTF-8", "ISO-8859-1", "Doc: "), 0, 0, 'L');
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(60, 7, iconv("UTF-8", "ISO-8859-1", $regv->tipo_documento . ": " . $regv->num_documento), 0, 0, 'L');
        $pdf->SetTextColor(39, 39, 51);
        $pdf->Cell(7, 7, iconv("UTF-8", "ISO-8859-1", 'Tel:'), 0, 0, 'L');
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", $regv->telefono), 0, 0);
        $pdf->SetTextColor(39, 39, 51);

        $pdf->Ln(7);

        $pdf->SetTextColor(39, 39, 51);
        $pdf->Cell(6, 7, iconv("UTF-8", "ISO-8859-1", 'Dir:'), 0, 0);
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Cell(109, 7, iconv("UTF-8", "ISO-8859-1", $regv->direccion), 0, 0);


        $pdf->Ln(9);

        $pdf->SetFillColor(23, 83, 201);
        $pdf->SetDrawColor(23, 83, 201);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(30, 8, iconv("UTF-8", "ISO-8859-1", 'Codigo'), 1, 0, 'C', true);
        $pdf->Cell(55, 8, iconv("UTF-8", "ISO-8859-1", 'Descripción'), 1, 0, 'C', true);
        $pdf->Cell(15, 8, iconv("UTF-8", "ISO-8859-1", 'Cant.'), 1, 0, 'C', true);
        $pdf->Cell(30, 8, iconv("UTF-8", "ISO-8859-1", 'Precio'), 1, 0, 'C', true);
        $pdf->Cell(30, 8, iconv("UTF-8", "ISO-8859-1", 'Descuento'), 1, 0, 'C', true);
        $pdf->Cell(30, 8, iconv("UTF-8", "ISO-8859-1", 'Subtotal'), 1, 0, 'C', true);

        $pdf->Ln(8);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(39, 39, 51);

        /*----------  Seleccionando detalles de la venta  ----------*/
        //obtenemos todos los detalles del a venta actual

        $rsptad = $venta->ventadetalles($_GET["id"]);

        while ($regd = $rsptad->fetch_object()) {

            $pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", $regd->codigo), 'L', 0, 'C');
            $pdf->Cell(55, 7, iconv("UTF-8", "ISO-8859-1", $regd->articulo), 'L', 0, 'C');
            $pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", $regd->cantidad), 'L', 0, 'C');
            $pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($regd->precio_venta, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 'L', 0, 'C');
            $pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($regd->descuento, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 'LR', 0, 'C');
            $pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($regd->subtotal, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 'LR', 0, 'C');
            $pdf->Ln(7);
        }


        $total = $regv->total_venta;
        $subtotal = $regv->subtotal;
        $ivaTotal = $regv->impuesto;
        $descuento = $regv->descuento;

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), 'T', 0, 'C');
        $pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), 'T', 0, 'C');

        $pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'SUBTOTAL'), 'T', 0, 'C');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($subtotal, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), 'T', 0, 'C');

        $pdf->Ln(7);

        $pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
        $pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
        $pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'IVA'), '', 0, 'C');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($ivaTotal, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), '', 0, 'C');

        $pdf->Ln(7);

        $pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
        $pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
        $pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'DESCUENTO'), '', 0, 'C');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($descuento, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), '', 0, 'C');

        $pdf->Ln(7);

        $pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
        $pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
        $pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'NETO A PAGAR'), '', 0, 'C');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($total, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), '', 0, 'C');

        $pdf->Ln(5);

        require_once "Letras.php";
        $V = new EnLetras();
        $V->substituir_un_mil_por_mil = true;

        $con_letra = strtoupper($V->number_words($total, "PESOS", "Y", "CENTAVOS"));

        $pdf->Cell(13, 7, iconv("UTF-8", "ISO-8859-1", "MONTO EN LETRAS: " . $con_letra), 0, 0);
        $pdf->SetTextColor(97, 97, 97);
        $pdf->Ln(7);

        $pdf->Cell(13, 7, iconv("UTF-8", "ISO-8859-1", "Firma: ___________________________________________________________________"), 0, 0);
        $pdf->SetTextColor(97, 97, 97);

        $pdf->Ln(7);

        $pdf->Cell(13, 7, iconv("UTF-8", "ISO-8859-1", "Realizada por: " . $_SESSION["nombre"]), 0, 0);
        $pdf->SetTextColor(97, 97, 97);

        $pdf->Ln(7);

        $pdf->SetFont('Arial', '', 6);


        $pdf->SetTextColor(39, 39, 51);
        $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", "Acepto el contenido de la presente factura y hago constar el recibido conforme de la mercancia y/o servicio en ella discriminada"), 0, 'C', false);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 7, iconv("UTF-8", "ISO-8859-1", "¡Gracias por su compra!"), '', 0, 'C');

        $pdf->Ln(9);

        $pdf->SetFillColor(39, 39, 51);
        $pdf->SetDrawColor(23, 83, 201);
        $pdf->Code128(72, $pdf->GetY(), $regv->codigo_venta, 70, 20);
        $pdf->SetXY(12, $pdf->GetY() + 21);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $regv->codigo_venta), 0, 'C', false);

        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 6);


        $pdf->SetTextColor(39, 39, 51);
        $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", "La presente factura de venta tiene carácter de título valor y se rige por la ley 1231 de julio 17/2008. El comprador y el aceptante declara haber recibido real y materialmente las mercancías descritas en este título valor y se obliga a pagar el precio en la forma pactada aquí mismo. Se hace constar que la firma de persona diferente al comprador está autorizada por el comprador para firmar, recibir y confesar la deuda. La mora en el pago causa intereses a la máxima tasa autorizada por la ley."), 0, 'C', false);

        $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 6);

        $pdf->SetTextColor(39, 39, 51);
        $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", "Autorizo a, " . $empresa . ", a quien presente sus derechos u ostente en el futuro la calidad de acreedor a reportar, procesar, solicitar y divulgar a la central de información financiera -CIFIN- que administra la asociación bancaria y entidades financieras de Colombia, o cualquier otra entidad que maneje o administre bases de datos con los mismos fines, total la información referente a mi comportamiento comercial."), 0, 'C', false);

        $pdf->Output("FV-$regv->serie_comprobante-$regv->num_comprobante", 'I');
        $pdf->Output("C:/xampp/htdocs/mi_tienda/facturas/factura/FV-$regv->serie_comprobante-$regv->num_comprobante.pdf", 'F');;
    } else {
        echo "No tiene permiso para visualizar el reporte";
    }
}

ob_end_flush();
