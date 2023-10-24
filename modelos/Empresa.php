<?php
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Empresa
{


	//implementamos nuestro constructor
	public function __construct()
	{
	}

	//metodo insertar regiustro
	public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $imagen)
	{
		$sql = "INSERT INTO empresa (nombre,tipo_documento,num_documento,direccion,telefono,email,imagen,condicion) VALUES ('$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$imagen','1')";
		ejecutarConsulta_retornarID($sql);
		return true;
	}

	public function editar($idempresa, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $imagen)
	{
		$sql = "UPDATE empresa SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',imagen='$imagen' 
	WHERE idempresa='$idempresa'";
		ejecutarConsulta_retornarID($sql);
		return true;
	}
	public function desactivar($idempresa)
	{
		$sql = "UPDATE empresa SET condicion='0' WHERE idempresa='$idempresa'";
		return ejecutarConsulta($sql);
	}
	public function activar($idempresa)
	{
		$sql = "UPDATE empresa SET condicion='1' WHERE idempresa='$idempresa'";
		return ejecutarConsulta($sql);
	}

	//metodo para mostrar registros
	public function mostrar($idempresa)
	{
		$sql = "SELECT * FROM empresa WHERE idempresa='$idempresa'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//listar registros
	public function listar()
	{
		$sql = "SELECT * FROM empresa";
		return ejecutarConsulta($sql);
	}
}
