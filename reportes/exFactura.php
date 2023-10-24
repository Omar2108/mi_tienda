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
    require('Factura.php');
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
    $rsptav = $venta->ventacabecera($_GET["id"]);

    //recorremos todos los valores que obtengamos
    $regv = $rsptav->fetch_object();

    //configuracion de la factura
    $pdf = new PDF_Invoice('p', 'mm', 'A4');
    $pdf->AddPage();

    //enviamos datos de la empresa al metodo addSociete de la clase factura
    $pdf->addSociete(
      mb_convert_encoding($empresa, 'UTF-8', mb_detect_encoding($empresa)),
      $documento . "\n" .
        mb_convert_encoding("Direccion: ", 'UTF-8', mb_detect_encoding("Direccion: ")) . mb_convert_encoding($direccion, 'UTF-8', mb_detect_encoding($direccion)) . "\n" .
        mb_convert_encoding("Telefono: ", 'UTF-8', mb_detect_encoding("Telefono: ")) . $telefono . "\n" .
        "Email: " . $email,
      $logo
    );

    $pdf->fact_dev("$regv->tipo_comprobante ", "$regv->serie_comprobante- $regv->num_comprobante");
    $pdf->temporaire("");
    $pdf->addDate($regv->fecha);

    //enviamos los datos del cliente al metodo addClientAddresse de la clase factura
    $pdf->addClientAdresse(
      mb_convert_encoding($regv->cliente, 'UTF-8', mb_detect_encoding($regv->cliente)),
      "Domicilio: " . mb_convert_encoding($regv->direccion, 'UTF-8', mb_detect_encoding($regv->direccion)),
      $regv->tipo_documento . ": " . $regv->num_documento,
      "Email: " . $regv->email,
      "Telefono: " . $regv->telefono
    );

    //establecemos las columnas que va tener la seccion donde mostramos los detalles de la venta
    $cols = array(
      "CODIGO" => 23,
      "DESCRIPCION" => 78,
      "CANTIDAD" => 22,
      "P.U." => 25,
      "DSCTO" => 20,
      "SUBTOTAL" => 22
    );
    $pdf->addCols($cols);
    $cols = array(
      "CODIGO" => "L",
      "DESCRIPCION" => "L",
      "CANTIDAD" => "C",
      "P.U." => "R",
      "DSCTO" => "R",
      "SUBTOTAL" => "C"
    );
    $pdf->addLineFormat($cols);
    $pdf->addLineFormat($cols);

    //actualizamos el valor de la coordenada "y" quie sera la ubicacion desde donde empecemos a mostrar los datos 
    $y = 85;

    //obtenemos todos los detalles del a venta actual
    $rsptad = $venta->ventadetalles($_GET["id"]);

    while ($regd = $rsptad->fetch_object()) {
      $precio = number_format($regd->precio_venta);
      $descuento = number_format($regd->descuento);
      $subto = number_format($regd->subtotal);
      $line = array(
        "CODIGO" => "$regd->codigo",
        "DESCRIPCION" => mb_convert_encoding("$regd->articulo", 'UTF-8', mb_detect_encoding("$regd->articulo")),
        "CANTIDAD" => "$regd->cantidad",
        "P.U." => "$precio",
        "DSCTO" => "$descuento",
        "SUBTOTAL" => "$subto"
      );
      $size = $pdf->addLine($y, $line);
      $y += $size + 2;
    }

    /*aqui falta codigo de letras*/
    require_once "Letras.php";
    $V = new EnLetras();

    $total = $regv->total_venta;
    $subtotal = $regv->subtotal;
    $ivaTotal = $regv->impuesto;
    $V = new EnLetras();
    $V->substituir_un_mil_por_mil = true;

    $con_letra = strtoupper($V->number_words($total, "PESOS","Y","CENTAVOS"));
    $pdf->addCadreTVAs($con_letra);


    //mostramos el impuesto
    
    $pdf->addTVAs($regv->impuesto, $regv->total_venta,$regv->subtotal,$regv->descuento, "$");
    $pdf->addCadreEurosFrancs("IVA ");
    $pdf->Output("FV-$regv->serie_comprobante-$regv->num_comprobante", 'I');
    $pdf->Output("C:/xampp/htdocs/mi_tienda/facturas/FV-$regv->serie_comprobante-$regv->num_comprobante.pdf", 'F');
  } else {
    echo "No tiene permiso para visualizar el reporte";
  }
}

ob_end_flush();
