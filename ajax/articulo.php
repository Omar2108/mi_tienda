<?php

use Baulphp\DBClass;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
require_once "../modelos/Articulo.php";
require_once ('../vendor/autoload.php');

$articulo = new Articulo();

$idarticulo = isset($_POST["idarticulo"]) ? limpiarCadena($_POST["idarticulo"]) : "";
$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$stock = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$impuesto = isset($_POST["porc_impuesto"]) ? limpiarCadena($_POST["porc_impuesto"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':

		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
			$imagen = $_POST["imagenactual"];
		} else {
			$ext = explode(".", $_FILES["imagen"]["name"]);
			if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {
				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/articulos/" . $imagen);
			}
		}
		if (empty($idarticulo)) {
			$rspta = $articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $impuesto, $imagen);
			echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
		} else {
			$rspta = $articulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $stock, $descripcion, $impuesto, $imagen);
			echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
		}
		break;


	case 'desactivar':
		$rspta = $articulo->desactivar($idarticulo);
		echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
		break;
	case 'activar':
		$rspta = $articulo->activar($idarticulo);
		echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
		break;

	case 'mostrar':
		$rspta = $articulo->mostrar($idarticulo);
		echo json_encode($rspta);
		break;

	case 'listar':
		$rspta = $articulo->listar();
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => ($reg->condicion) ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idarticulo . ')"><i class="fa fa-pencil"></i></button>' . ' ' . '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idarticulo . ')"><i class="fa fa-close"></i></button>' : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idarticulo . ')"><i class="fa fa-pencil"></i></button>' . ' ' . '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idarticulo . ')"><i class="fa fa-check"></i></button>',
				"1" => $reg->nombre,
				"2" => $reg->categoria,
				"3" => $reg->codigo,
				"4" => $reg->stock,
				"5" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>",
				"6" => $reg->descripcion,
				"7" => $reg->impuesto,
				"8" => ($reg->condicion) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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

	case 'listarPorAgotar':
		$rspta = $articulo->listarPorAgotarse();
		$data = array();
		$num = 1;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => $num,
				"1" => $reg->nombre,
				"2" => $reg->categoria,
				"3" => $reg->codigo,
				"4" => $reg->stock,
				"5" => $reg->descripcion,
				"6" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>"
			);
			$num++;
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);
		break;

	case 'selectCategoria':
		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();

		$rspta = $categoria->select();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'upload':

		$allowedFileType = [
			'application/vnd.ms-excel',
			'text/xls',
			'text/xlsx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		];

		if (in_array($_FILES["archivo"]["type"], $allowedFileType)) {

			$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

			$spreadSheet = $Reader->load($_FILES['archivo']['name']);

			$excelSheet = $spreadSheet->getActiveSheet();
			$spreadSheetAry = $excelSheet->toArray();
			$sheetCount = count($spreadSheetAry);

			for ($i = 0; $i < $sheetCount; $i ++) {

				$rs = $articulo->getproducto($spreadSheetAry[$i][2]);
				$reg = $rs->fetch_object();

				if ($reg == null) {

					$articulo->insertar($spreadSheetAry[$i][0], $spreadSheetAry[$i][1], $spreadSheetAry[$i][2], $spreadSheetAry[$i][3], $spreadSheetAry[$i][4], $spreadSheetAry[$i][5], $spreadSheetAry[$i][6]);

				}

			}

			echo  "Datos registrados correctamente";

		}

	break;
}