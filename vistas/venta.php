<?php

header('Content-Type: text/html; charset=UTF-8');
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {


  require 'header.php';

  if ($_SESSION['ventas'] == 1) {

?>


    <div class="content-wrapper" style="height: auto;">

      <!-- Main content -->
      <section class="content" style="height:auto;">
        <!-- Default box -->
        <div class="row" style="height:auto;">
          <div class="col-md-12" style="height:auto;">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Ventas <button class="btn btn-success" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i>Agregar</button></h1>
                <div class="box-tools pull-right">

                </div>
              </div>
              <!--box-header-->
              <!--centro-->
              <div class="panel-body table-responsive" id="listadoregistros">
                <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                  <thead>
                    <th>Opciones</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Usuario</th>
                    <th>Número</th>
                    <th>Total Venta</th>
                    <th>Estado</th>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <th>Opciones</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Usuario</th>
                    <th>Número</th>
                    <th>Total Venta</th>
                    <th>Estado</th>
                  </tfoot>
                </table>
              </div>
              <div class="panel-body" style="height: 400px;" id="formularioregistros">
                <form action="" name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-8 col-md-8 col-xs-12">
                    <label for="">Cliente(*):</label>
                    <input class="form-control" type="hidden" name="idventa" id="idventa">
                    <select name="idcliente" id="idcliente" class="form-control selectpicker" data-live-search="true" required>
                    </select>
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-xs-12">
                    <label for="">Fecha(*): </label>
                    <input class="form-control" type="date" name="fecha_hora" id="fecha_hora" required>
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-xs-6">
                    <label for="">Serie: </label>
                    <input class="form-control" type="text" name="serie_comprobante" id="serie_comprobante" maxlength="7" placeholder="Serie">
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-xs-6">
                    <label for="">Número: </label>
                    <input class="form-control" type="text" name="num_comprobante" id="num_comprobante" maxlength="10" placeholder="Número" required>
                  </div>
                  <div class="form-group col-lg-8 col-md-8 col-xs-12">
                    <label for="">Forma de pago(*):</label>
                    <input class="form-control" type="hidden" name="idventa" id="idventa">
                    <select name="forma_pago" id="forma_pago" class="form-control selectpicker" data-live-search="true" required>
                      <option value="Efectivo">Efectivo</option>
                      <option value="Tarjeta debido">Tarjeta Debido</option>
                      <option value="Tarjeta credito">Tarjeta Credito</option>
                      <option value="Transferencia">Transferencia</option>
                    </select>
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <a data-toggle="modal" href="#myModal">
                      <button id="btnAgregarArt" type="button" class="btn btn-primary"><span class="fa fa-plus"></span>Agregar Articulos</button>
                    </a>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-xs-12">
                    <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                      <thead style="background-color:#A9D0F5">
                        <th>Opciones</th>
                        <th>Articulo</th>
                        <th>Cantidad</th>
                        <th>Precio Venta</th>
                        <th>Descuento</th>
                        <th>Impuesto</th>
                        <th>Subtotal</th>
                        <th>Accion</th>
                      </thead>
                      <tfoot>
                        <tr>
                          <th>SUBTOTAL</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th colspan="2">
                            <p id="sub-total">$/ 0.00</p><input type="hidden" name="subtotal_venta" id="subtotal_venta">
                          </th>
                        </tr>

                        <tr>
                          <th>IVA</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th colspan="2">
                            <p id="iva_total">$/ 0.00</p><input type="hidden" name="iva" id="iva">
                          </th>
                        </tr>
                        <tr>
                          <th>TOTAL</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th colspan="2">
                            <p id="total">$/ 0.00</p><input type="hidden" name="total_venta" id="total_venta">
                          </th>
                        </tr>

                      </tfoot>
                      <tbody>

                      </tbody>
                    </table>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                    <button class="btn btn-danger" onclick="cancelarform()" type="button" id="btnCancelar"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                  </div>
                </form>
              </div>
              <!--fin centro-->
            </div>
          </div>
        </div>
        <!-- /.box -->

      </section>
      <!-- /.content -->
    </div>
    <!-- Scrollable modal -->


    <!--Modal-->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 65% !important;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Seleccione un Articulo</h4>
          </div>
          <div class="modal-body">
            <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Categoria</th>
                <th>Código</th>
                <th>Stock</th>
                <th>% Impuesto</th>
                <th>Precio Venta</th>
                <th>Imagen</th>
              </thead>
              <tbody>

              </tbody>
              <tfoot>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Categoria</th>
                <th>Código</th>
                <th>Stock</th>
                <th>% Impuesto</th>
                <th>Precio Venta</th>
                <th>Imagen</th>
              </tfoot>
            </table>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- fin Modal-->

    <!--Modal-->
    <div class="modal fade" id="myModal-loading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 40% !important; height: 30% !important;">
        <div class="modal-content">
          <div class="modal-body">
            <div class="modal-dialog modal-dialog-centered" style="width: 100%; height: 100%;">
              <img style="width: 100%; height: 100%;" src="https://i.gifer.com/1amw.gif">
            </div>
            <div class="modal-footer">
            </div>
          </div>
        </div>
      </div>
      <!-- fin Modal-->
    <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
    ?>
    <script src="scripts/venta.js"></script>
  <?php
}

ob_end_flush();
  ?>