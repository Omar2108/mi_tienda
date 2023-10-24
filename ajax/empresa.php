<?php 
session_start();
require_once "../modelos/Empresa.php";

$empresa=new Empresa();

$idempresa=isset($_POST["idempresa"])? limpiarCadena($_POST["idempresa"]):"";
$nombre=isset($_POST["nombre-empresa"])? limpiarCadena($_POST["nombre-empresa"]):"";
$tipo_documento=isset($_POST["tipo_documento-empresa"])? limpiarCadena($_POST["tipo_documento-empresa"]):"";
$num_documento=isset($_POST["num_documento-empresa"])? limpiarCadena($_POST["num_documento-empresa"]):"";
$direccion=isset($_POST["direccion-empresa"])? limpiarCadena($_POST["direccion-empresa"]):"";
$telefono=isset($_POST["telefono-empresa"])? limpiarCadena($_POST["telefono-empresa"]):"";
$email=isset($_POST["email-empresa"])? limpiarCadena($_POST["email-empresa"]):"";
$imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";

switch ($_GET["op"]) {
	case 'guardaryeditar':

	if (!file_exists($_FILES['imagen']['tmp_name'])|| !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
		$imagen=$_POST["imagenactual"];
	}else{
		$ext=explode(".", $_FILES["imagen"]["name"]);
		if ($_FILES['imagen']['type']=="image/jpg" || $_FILES['imagen']['type']=="image/jpeg" || $_FILES['imagen']['type']=="image/png") {
			$imagen=round(microtime(true)).'.'. end($ext);
			move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/empresa/".$imagen);
			move_uploaded_file($_FILES["imagen"]["tmp_name"], "../reportes/".$imagen);
		}
	}

	
	if (empty($idempresa)) {
		$rspta=$empresa->insertar($nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$imagen);
		echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar todos los datos del usuario";
	}else{
		$rspta=$empresa->editar($idempresa,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$imagen);
		echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
	}
	break;
	

	case 'desactivar':
	$rspta=$empresa->desactivar($idempresa);
	echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
	break;

	case 'activar':
	$rspta=$empresa->activar($idempresa);
	echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
	break;
	
	case 'mostrar':
	$rspta=$empresa->mostrar($idempresa);
	echo json_encode($rspta);
	break;

	case 'listar':
	$rspta=$empresa->listar();
	$data=Array();

	while ($reg=$rspta->fetch_object()) {
		$data[]=array(
			"0"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fa fa-pencil"></i></button>'.' '.'<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idempresa.')"><i class="fa fa-close"></i></button>':'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fa fa-pencil"></i></button>'.' '.'<button class="btn btn-primary btn-xs" onclick="activar('.$reg->idempresa.')"><i class="fa fa-check"></i></button>',
			"1"=>$reg->nombre,
			"2"=>$reg->tipo_documento,
			"3"=>$reg->num_documento,
			"4"=>$reg->direccion,
			"5"=>$reg->telefono,
			"6"=>$reg->email,
			"7"=>"<img src='../files/empresa/".$reg->imagen."' height='50px' width='50px'>",
			"8"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'
		);
	}

	$results=array(
             "sEcho"=>1,//info para datatables
             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable
             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
             "aaData"=>$data); 
	echo json_encode($results);
	break;

	case 'salir':
	   //limpiamos la variables de la secion
	session_unset();

	  //destruimos la sesion
	session_destroy();
		  //redireccionamos al login
	header("Location: ../index.php");
	break;
	
}
?>

