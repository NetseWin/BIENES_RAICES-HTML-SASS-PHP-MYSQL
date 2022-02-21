<?php

namespace Model;

class Vendedor extends ActiveRecord
{
	//este arreglo nos permitira identificar que forma debe tener los datos
	protected static $columnasDB = ['id', 'nombre', 'apellido', 'telefono'];
	protected static $tabla = 'vendedores';

	public $id;
	public $nombre;
	public $apellido;
	public $telefono;

	public function __construct($args = [])
	{
		$this->id = $args['id'] ?? null;
		$this->nombre = $args['nombre'] ?? '';
		$this->apellido = $args['apellido'] ?? '';
		$this->telefono = $args['telefono'] ?? '';
	
	}

	public function validar()
		{
	
			if (!$this->nombre) {
				self::$errores[] = "El nombre es obligatorio";
			}
			if (!$this->apellido) {
				self::$errores[] = "El apellido es obligatorio";
			}
			if(!preg_match('/[0-9]{10}/', $this->telefono)){
				self::$errores[] = "El telefono debe ser numerico";
			}
			if (!$this->telefono) {
				self::$errores[] = "El telefono es obligatorio";
			}
			return self::$errores;
		}
}
