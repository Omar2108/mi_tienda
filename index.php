<?php 
// Esto le dice a PHP que usaremos cadenas UTF-8 hasta el final
mb_internal_encoding('UTF-8');

 
// Esto le dice a PHP que generaremos cadenas UTF-8
mb_http_output('UTF-8');
//redireccionar a la vista de login

header('location: vistas/login.html');
 ?>