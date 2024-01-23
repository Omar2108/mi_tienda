<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Venta{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar registro
public function insertar($idcliente,$idusuario,$idempresa,$codigo_venta,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$subtotal,$total_venta,$idarticulo,$cantidad,$precio_venta,$descuento, $forma_pago){
	$sql="INSERT INTO venta (idcliente,idusuario,idempresa,codigo_venta,serie_comprobante,num_comprobante,fecha_hora,impuesto,subtotal,total_venta,forma_pago,estado) VALUES ('$idcliente','$idusuario','$idempresa','$codigo_venta','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$subtotal','$total_venta','$forma_pago','Aceptado')";
	
	 $idventanew=ejecutarConsulta_retornarID($sql);
	 $num_elementos=0;
	 $sw=true;
	 while ($num_elementos < count($idarticulo)) {

	 	$sql_detalle="INSERT INTO detalle_venta (idventa,idarticulo,cantidad,precio_venta,descuento) VALUES('$idventanew','$idarticulo[$num_elementos]','$cantidad[$num_elementos]','$precio_venta[$num_elementos]','$descuento[$num_elementos]')";

	 	ejecutarConsulta($sql_detalle) or $sw=false;

	 	$num_elementos=$num_elementos+1;
	 }
	 return $sw;
}

public function anular($idventa, $idempresa){
	$sql="UPDATE venta SET estado='Anulado' WHERE idventa='$idventa' AND idempresa = '$idempresa'";
	return ejecutarConsulta($sql);
}


//implementar un metodopara mostrar los datos de unregistro a modificar
public function mostrar($idventa, $idempresa){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario,v.serie_comprobante,v.num_comprobante,p.email,v.total_venta,
	(SELECT porce_impuesto FROM articulo WHERE idarticulo = d.idarticulo) as impuesto,v.forma_pago, v.estado 
	FROM venta v 
	INNER JOIN persona p ON v.idcliente = p.idpersona 
	INNER JOIN usuario u ON v.idusuario = u.idusuario 
	INNER JOIN detalle_venta d ON d.idventa = v.idventa 
	WHERE v.idventa ='$idventa' AND v.idempresa = '$idempresa'";
	return ejecutarConsultaSimpleFila($sql);
}

public function listarDetalle($idventa){
	$sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal FROM detalle_venta dv INNER JOIN articulo a ON dv.idarticulo=a.idarticulo WHERE dv.idventa='$idventa'";
	return ejecutarConsulta($sql);
}

//listar registros
public function listar($idempresa){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,p.email , u.idusuario,u.nombre as usuario,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idempresa = '$idempresa' ORDER BY v.idventa DESC";
	return ejecutarConsulta($sql);
}


public function ventacabecera($idventa, $idempresa){
	$sql= "SELECT v.idventa, v.idcliente,v.codigo_venta, p.nombre AS cliente, p.direccion,p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre AS usuario, v.serie_comprobante, v.num_comprobante, v.fecha_hora AS fecha, v.impuesto,v.subtotal, (SELECT SUM(descuento) FROM detalle_venta d WHERE d.idventa = v.idventa) as descuento, v.total_venta, v.forma_pago FROM venta v 
	INNER JOIN persona p ON v.idcliente=p.idpersona 
	INNER JOIN usuario u ON v.idusuario=u.idusuario
	WHERE v.idventa ='$idventa' AND v.idempresa = '$idempresa' ";
	return ejecutarConsulta($sql);
}

public function ventadetalles($idventa){
	$sql="SELECT a.nombre AS articulo, a.codigo, d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal FROM detalle_venta d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idventa='$idventa'";
         return ejecutarConsulta($sql);
}

//consecutivo de ventas

function getConsecutivoVentas($idempresa) {
	$sql= "CALL SP_CONSECUTIVO_VENTAS('$idempresa')";
	return ejecutarConsulta($sql);
	
}

function getStock($idarticulo) {
	$sql= "SELECT stock FROM articulo WHERE idarticulo = '$idarticulo'";
	return ejecutarConsulta($sql);
	
}

public function getVentas(){
	$sql = "SELECT COUNT(idventa) as num FROM venta";
	return ejecutarConsulta($sql);
}

public function generarCodigoAleatorio($longitud,$correlativo){
	$codigo="";
	$caracter="Letra";
	for($i=1; $i<=$longitud; $i++){
		if($caracter=="Letra"){
			$letra_aleatoria=chr(rand(ord("a"),ord("z")));
			$letra_aleatoria=strtoupper($letra_aleatoria);
			$codigo.=$letra_aleatoria;
			$caracter="Numero";
		}else{
			$numero_aleatorio=rand(0,9);
			$codigo.=$numero_aleatorio;
			$caracter="Letra";
		}
	}
	return $codigo."-".$correlativo;
}



}
