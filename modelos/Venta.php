<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Venta{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar registro
public function insertar($idcliente,$idusuario,$idempresa,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$subtotal,$total_venta,$idarticulo,$cantidad,$precio_venta,$descuento){
	$sql="INSERT INTO venta (idcliente,idusuario,idempresa,serie_comprobante,num_comprobante,fecha_hora,impuesto,subtotal,total_venta,estado) VALUES ('$idcliente','$idusuario','$idempresa','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$subtotal','$total_venta','Aceptado')";
	//return ejecutarConsulta($sql);
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

public function anular($idventa){
	$sql="UPDATE venta SET estado='Anulado' WHERE idventa='$idventa'";
	return ejecutarConsulta($sql);
}


//implementar un metodopara mostrar los datos de unregistro a modificar
public function mostrar($idventa){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario,v.serie_comprobante,v.num_comprobante,p.email,v.total_venta,
	(SELECT porce_impuesto FROM articulo WHERE idarticulo = d.idarticulo) as impuesto,v.estado 
	FROM venta v 
	INNER JOIN persona p ON v.idcliente = p.idpersona 
	INNER JOIN usuario u ON v.idusuario = u.idusuario 
	INNER JOIN detalle_venta d ON d.idventa = v.idventa 
	WHERE v.idventa ='$idventa'";
	return ejecutarConsultaSimpleFila($sql);
}

public function listarDetalle($idventa){
	$sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal FROM detalle_venta dv INNER JOIN articulo a ON dv.idarticulo=a.idarticulo WHERE dv.idventa='$idventa'";
	return ejecutarConsulta($sql);
}

//listar registros
public function listar(){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,p.email , u.idusuario,u.nombre as usuario,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER BY v.idventa DESC";
	return ejecutarConsulta($sql);
}


public function ventacabecera($idventa){
	$sql= "SELECT v.idventa, v.idcliente, p.nombre AS cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre AS usuario, v.serie_comprobante, v.num_comprobante, v.fecha_hora AS fecha, v.impuesto,v.subtotal, (SELECT SUM(descuento) FROM detalle_venta d WHERE d.idventa = v.idventa) as descuento, v.total_venta FROM venta v 
	INNER JOIN persona p ON v.idcliente=p.idpersona 
	INNER JOIN usuario u ON v.idusuario=u.idusuario
	WHERE v.idventa ='$idventa'";
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



}
