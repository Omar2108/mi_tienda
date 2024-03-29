<?php
header('Content-Type: text/html; charset=UTF-8');
// Esto le dice a PHP que usaremos cadenas UTF-8 hasta el final
mb_internal_encoding('UTF-8');

// Esto le dice a PHP que generaremos cadenas UTF-8
mb_http_output('UTF-8');
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {


  require 'header.php';

  if ($_SESSION['escritorio'] == 1) {

    require_once "../modelos/Consultas.php";
    $consulta = new Consultas();
    $rsptac = $consulta->totalcomprahoy();
    $regc = $rsptac->fetch_object();
    $totalc = $regc->total_compra;

    $rsptav = $consulta->totalventahoy();
    $regv = $rsptav->fetch_object();
    $totalv = $regv->total_venta;

    //obtener valores para cargar al grafico de barras
    $compras10 = $consulta->comprasultimos_10dias();
    $fechasc = '';
    $totalesc = '';
    while ($regfechac = $compras10->fetch_object()) {
      $fechasc = $fechasc . '"' . $regfechac->fecha . '",';
      $totalesc = $totalesc . $regfechac->total . ',';
    }


    //quitamos la ultima coma
    $fechasc = substr($fechasc, 0, -1);
    $totalesc = substr($totalesc, 0, -1);



    //obtener valores para cargar al grafico de barras
    $ventas12 = $consulta->ventasultimos_12meses();
    $fechasv = '';
    $totalesv = '';
    while ($regfechav = $ventas12->fetch_object()) {
      $fechasv = $fechasv . '"' . $regfechav->fecha . '",';
      $totalesv = $totalesv . $regfechav->total . ',';
    }


    //quitamos la ultima coma
    $fechasv = substr($fechasv, 0, -1);
    $totalesv = substr($totalesv, 0, -1);
?>
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">

        <!-- Default box -->
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Escritorio</h1>
                <div class="box-tools pull-right">

                </div>
              </div>
              <!--box-header-->
              <!--centro-->
              <div class="panel-body">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h4 style="font-size: 17px;">
                        <strong>$/. <?php echo number_format($totalc); ?> </strong>
                      </h4>
                      <p>Compras</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    <a href="ingreso.php" class="small-box-footer">Compras <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h4 style="font-size: 17px;">
                        <strong>$/. <?php echo number_format($totalv); ?> </strong>
                      </h4>
                      <p>Ventas</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    <a href="venta.php" class="small-box-footer">Ventas <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="panel-body">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      Compras de los ultimos 10 dias
                    </div>
                    <div class="box-body">
                      <canvas id="compras" width="400" height="300"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      Ventas de los ultimos 12 meses
                    </div>
                    <div class="box-body">
                      <canvas id="ventas" width="400" height="300"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <!--fin centro-->
            </div>
          </div>
        </div>
        <!-- /.box -->

      </section>
      <!-- /.content -->

      <section class="content">
        <div style="display: flex; justify-content: center; align-items: center;">
          <h2>Productos Pronto Por Agotarse</h2>
        </div>
        <div class="panel-body table-responsive" id="listadoregistros">
          <table id="tbllistadoAgotar" class="table table-striped table-bordered table-condensed table-hover">
            <thead>
              <th>#</th>
              <th>Nombre</th>
              <th>Categoria</th>
              <th>Codigo</th>
              <th>Stock</th>
              <th>Descripcion</th>
              <th>Imagen</th </thead>
            <tbody>
            </tbody>
            <tfoot>
              <th>#</th>
              <th>Nombre</th>
              <th>Categoria</th>
              <th>Codigo</th>
              <th>Stock</th>
              <th>Descripcion</th>
              <th>Imagen</th>
            </tfoot>
          </table>
        </div>
      </section>
    </div>

  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script src="../public/js/Chart.bundle.min.js"></script>
  <script src="../public/js/Chart.min.js"></script>
  <script src="../public/js/push.min.js"></script>
  <script>
    var ctx = document.getElementById("compras").getContext('2d');
    var ventas10dias = [<?php echo $totalesc ?>];
    let total = 0;
    ventas10dias.forEach((e) => total += e);

    var compras = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [<?php echo $fechasc ?>],
        datasets: [{
          label: `# Compras en $/. ${total.toLocaleString()} de los últimos 10 dias`,
          data: [<?php echo $totalesc ?>],
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)'
          ],
          borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
    var ctx = document.getElementById("ventas").getContext('2d');
    var ventas12meses = [<?php echo $totalesv ?>];
    var ventas = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [<?php echo $fechasv ?>],
        datasets: [{
          label: `# Ventas en $/. ${ventas12meses.toLocaleString()} de los últimos 12 meses`,
          data: [<?php echo $totalesv ?>],
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  </script>
  <script>
    var tabla;

    function init() {

      listarArticulosPorAgotar();

    }
    //funcion listar
    function listarArticulosPorAgotar() {
      tabla = $('#tbllistadoAgotar').dataTable({
        "aProcessing": true, //activamos el procedimiento del datatable
        "aServerSide": true, //paginacion y filrado realizados por el server
        dom: 'Bfrtip', //definimos los elementos del control de la tabla
        "ajax": {
          url: '../ajax/articulo.php?op=listarPorAgotar',
          type: "get",
          dataType: "json",
          error: function(e) {
            console.log(e.responseText);
          }
        },
        "bDestroy": true,
        "iDisplayLength": 5, //paginacion
        "order": [
          [0, "asc"]
        ] //ordenar (columna, orden)
      }).DataTable();
    }

    function notificacion() {
      $.get("../ajax/articulo.php?op=listarPorAgotar", function(data, status) {

        let datos = JSON.parse(data);

        if (datos.aaData.length > 0) {
          $.each(datos.aaData, function(ind, elem) {
            Push.create("Producto por Agotarse", {
              body: `El producto ${elem[1]}, esta pronto por agotarse solo quedan ${elem[4]} disponible.`,
              icon: "../public/images/importante.jpg",
              onClick: function() {
                window.focus();
                this.close();
              }

            });
          });

        }
      });
    }


    init();

    if (localStorage.getItem("estado_toggle")) {

      let estado = localStorage.getItem("estado_toggle");
      if (estado === "true") {
        notificacion();
      }
      
    }


    document.getElementById('toggle_notificacion').addEventListener("change", () => {

      let estado = localStorage.getItem("estado_toggle");
      if (estado === "true") {
        notificacion();
      }

    });
  </script>
<?php
}

ob_end_flush();
?>