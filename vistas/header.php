 <?php
  header('Content-Type: text/html; charset=UTF-8');
  if (strlen(session_id()) < 1)
    session_start();

  ?>
 <!DOCTYPE html>
 <html>

 <head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <title>SISVentas | <?php $url = explode("/", $_SERVER['REQUEST_URI']);
                      $arrstring = explode(".", $url[4]);
                      echo $arrstring[0];
                      ?></title>
   <!-- Tell the browser to be responsive to screen width -->
   <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
   <link rel="shortcut icon" href="../public/img/coffee.ico" />
   <!-- Bootstrap 3.3.7 -->
   <link rel="stylesheet" href="../public/css/bootstrap.min.css">

   <link rel="stylesheet" href="../public/css/style.css">
   <!-- Font Awesome -->


   <link rel="stylesheet" href="../public/css/font-awesome.min.css">

   <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
   <link rel="stylesheet" href="../public/css/_all-skins.min.css">

   <!-- DATATABLES-->
   <link rel="stylesheet" href="../public/datatables/jquery.dataTables.min.css">
   <link rel="stylesheet" href="../public/datatables/buttons.dataTables.min.css">
   <link rel="stylesheet" href="../public/datatables/responsive.dataTables.min.css">
   <link rel="stylesheet" href="../public/css/bootstrap-select.min.css">

   <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>

 </head>

 <body class="hold-transition skin-blue sidebar-mini">
   <div class="wrapper">

     <header class="main-header">
       <!-- Logo -->
       <a href="escritorio.php" class="logo">
         <!-- mini logo for sidebar mini 50x50 pixels -->
         <span class="logo-mini"><b>SIS</b> V</span>
         <!-- logo for regular state and mobile devices -->
         <span class="logo-lg"><b>SIS</b> VENTAS</span>
       </a>
       <!-- Header Navbar: style can be found in header.less -->
       <nav class="navbar navbar-static-top">
         <!-- Sidebar toggle button-->
         <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
           <span class="sr-only">NAVEGACIÓN</span>
         </a>

         <div style="display: flex; flex-direction: row;" class="navbar-custom-menu">
           <div class="checkbox">
             <label id="notificacion_toggle">
               <input id="toggle_notificacion" type="checkbox" data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
               Activar Notificaciones
             </label>
           </div>
           <ul class="nav navbar-nav">

             <li class="dropdown user user-menu">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                 <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="user-image" alt="User Image">
                 <span class="hidden-xs"><?php echo $_SESSION['nombre']; ?></span>
               </a>
               <ul class="dropdown-menu">
                 <!-- User image -->
                 <li class="user-header">
                   <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="img-circle" alt="User Image">

                   <p>
                     <?php echo $_SESSION['nombre']; ?>
                     <small>
                       <?php
                        require('../modelos/Consultas.php');
                        $consulta = new Consultas();
                        $idempresa = $_SESSION['idempresa'];
                        $rpts = $consulta->infoempresa($idempresa);
                        $regt = $rpts->fetch_object();

                        echo $regt->nombre; ?></small>
                     <small><?php
                            date_default_timezone_set('America/Bogota');
                            $fechaActual = date("d-m-Y");
                            echo $fechaActual; ?>
                     </small>
                   </p>
                 </li>
                 <!-- Menu Footer-->
                 <li class="user-footer">

                   <div class="pull-right">
                     <a href="../ajax/usuario.php?op=salir" class="btn btn-default btn-flat">Salir</a>
                   </div>
                 </li>
               </ul>
             </li>
             <!-- Control Sidebar Toggle Button -->

           </ul>
         </div>
       </nav>
     </header>
     <!-- Left side column. contains the logo and sidebar -->
     <aside class="main-sidebar">
       <!-- sidebar: style can be found in sidebar.less -->
       <section class="sidebar">
         <!-- Sidebar user panel -->

         <!-- /.search form -->
         <!-- sidebar menu: : style can be found in sidebar.less -->
         <ul class="sidebar-menu" data-widget="tree">

           <br>
           <?php
            if ($_SESSION['escritorio'] == 1) {
              echo ' <li><a href="escritorio.php"><i class="fa  fa-dashboard (alias)"></i> <span>Escritorio</span></a>
        </li>';
            }
            ?>
           <?php
            if ($_SESSION['almacen'] == 1) {
              echo ' <li class="treeview">
          <a href="#">
            <i class="fa fa-laptop"></i> <span>Almacen</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="articulo.php"><i class="fa fa-circle-o"></i> Articulos</a></li>
            <li><a href="categoria.php"><i class="fa fa-circle-o"></i> Categorias</a></li>
          </ul>
        </li>';
            }
            ?>
           <?php
            if ($_SESSION['compras'] == 1) {
              echo ' <li class="treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span>Compras</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="ingreso.php"><i class="fa fa-circle-o"></i> Ingresos</a></li>
            <li><a href="proveedor.php"><i class="fa fa-circle-o"></i> Proveedores</a></li>
          </ul>
        </li>';
            }
            ?>

           <?php
            if ($_SESSION['ventas'] == 1) {
              echo '<li class="treeview">
          <a href="#">
            <i class="fa fa-shopping-cart"></i> <span>Ventas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="venta.php"><i class="fa fa-circle-o"></i> ventas</a></li>
            <li><a href="cliente.php"><i class="fa fa-circle-o"></i> clientes</a></li>
          </ul>
        </li>';
            }
            ?>

           <?php
            if ($_SESSION['acceso'] == 1) {
              echo '  <li class="treeview">
          <a href="#">
            <i class="fa fa-folder"></i> <span>Configuracion</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
            <li><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
            <li><a href="empresa.php"><i class="fa fa-circle-o"></i> Empresa</a></li>
          </ul>
        </li>';
            }
            ?>
           <?php
            if ($_SESSION['consultac'] == 1) {
              echo '     <li class="treeview">
          <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Consulta Compras</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="comprasfecha.php"><i class="fa fa-circle-o"></i>Compras por fechas</a></li>
          </ul>
        </li>';
            }
            ?>

           <?php
            if ($_SESSION['consultav'] == 1) {
              echo '<li class="treeview">
          <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Consulta Ventas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="ventasfechacliente.php"><i class="fa fa-circle-o"></i> Consulta Ventas</a></li>

          </ul>
        </li>';
            }
            ?>


           <li><a href="#"><i class="fa fa-question-circle"></i> <span>Ayuda</span><small class="label pull-right bg-yellow">PDF</small></a></li>
           <li><a href="#"><i class="fa  fa-exclamation-circle"></i> <span>Ayuda</span><small class="label pull-right bg-yellow">IT</small></a></li>
         </ul>
       </section>
       <!-- /.sidebar -->
     </aside>


     <script>
       let url = window.location.href;

       if (!url.includes("escritorio")) {

         document.querySelector(".checkbox").style.display = "none";

       }
       document.querySelector('#toggle_notificacion').bootstrapToggle({
         onlabel: 'On',
         offlabel: 'Off'
       });

       let checked_actual = document.getElementById('toggle_notificacion').checked;

       if (localStorage.getItem("estado_toggle") !== checked_actual.toString()) {
        document.getElementById('toggle_notificacion').bootstrapToggle('toggle');

        let label = document.getElementById('notificacion_toggle').lastChild;

        label.nodeValue.includes("Activar") ? label.textContent = "Desactivar Notificaciones" : label.textContent = "Activar Notificaciones";
       }



       document.getElementById('toggle_notificacion').addEventListener("change", () => {


         let checked = document.getElementById('toggle_notificacion').checked;
         let estado = localStorage.getItem("estado_toggle");
         if (estado) {
           localStorage.removeItem("estado_toggle");
           localStorage.setItem("estado_toggle", checked);

           if (localStorage.getItem("estado_toggle") === "false") {
             document.getElementById('toggle_notificacion').removeAttribute("checked");

             let notificacion_toggle = document.getElementById('notificacion_toggle');

             notificacion_toggle.lastChild.textContent = "Activar Notificaciones";

           } else {

             notificacion_toggle.lastChild.textContent = "Desactivar Notificaciones";


           }


         } else {

           localStorage.setItem("estado_toggle", checked);

           if (localStorage.getItem("estado_toggle") === "false") {
             document.getElementById('toggle_notificacion').removeAttribute("checked");

             let notificacion_toggle = document.getElementById('notificacion_toggle');

             notificacion_toggle.lastChild.textContent = "Activar Notificaciones";

           } else {

             notificacion_toggle.lastChild.textContent = "Desactivar Notificaciones";


           }

         }


       })
     </script>