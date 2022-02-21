<?php

namespace Model;


class ActiveRecord {
		//BD
		protected static $db;
		//este arreglo nos permitira identificar que forma debe tener los datos
		protected static $columnasDB = [];

	    protected static $tabla = '';

		//Errores
		protected static $errores = [];
	
		//Definir la conexion a la BD
		public static function setDB($database)
		{
			self::$db = $database;
		}
	
		public function guardar()
		{
			if (!is_null($this->id)) {
				//actualizar
				$this->actualizar();
			} else {
				//Creando un nueo registro
				$this->crear();
			}
		}
	
		public function crear()
		{
			//Sanitizar la entrada de los datos
			$atributos = $this->sanitizarAtributos();
	
			//Insertar en la base de datos
			$query = "INSERT INTO " . static::$tabla . " ( ";
			$query .= join(', ', array_keys($atributos));
			$query .= " ) VALUES (' ";
			$query .= join("', '", array_values($atributos));
			$query .= " ' ) ";
	
			$resultado = self::$db->query($query);
	
			//Mensaje de exito o error
			if ($resultado) {
				// echo "Insertado Correctamente";
				//Redireccionar al usuario una vez que ingresan 
				header('Location:/admin?resultado=1');
			}
		}
		public function actualizar()
		{
			//Sanitizar la entrada de los datos
			$atributos = $this->sanitizarAtributos();
	
			$valores = [];
	
			foreach ($atributos as $key => $value) {
				$valores[] = "{$key}='{$value}'";
			}
	
			$query = "UPDATE ". static::$tabla ." SET ";
			$query .=  join(', ', $valores);
			$query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
			$query .= " LIMIT 1 ";
	
			$resultado = self::$db->query($query);
	
			if ($resultado) {
				// echo "Insertado Correctamente";
				//Redireccionar al usuario una vez que ingresan 
				header('Location:/admin?resultado=2');
			}
		}
	
		//Eliminar un registro
		public function eliminar()
		{
			//Eliminar la propiedad
			$query = "DELETE FROM ". static::$tabla ." WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
			$resultado = self::$db->query($query);
		
			if ($resultado) {
				$this->borrarImagen();
	
				header('Location:/admin?resultado=3');
			}
			return $resultado;
		}
	
		//Identificar y unir los atributos de la BD
		public function atributos()
		{
			$atributos = [];
			foreach (static::$columnasDB as $columna) {
				if ($columna === 'id') continue;
				$atributos[$columna] = $this->$columna;
			}
			return $atributos;
		}
	
		public function sanitizarAtributos()
		{
			$atributos = $this->atributos();
			$sanitizado = [];
	
			foreach ($atributos as $key => $value) {
				$sanitizado[$key] = self::$db->escape_string($value);
			}
			return $sanitizado;
		}
	
		//Subida de archivo
		public function setImagen($imagen)
		{
			//Eliminar la imagen previa
			if (!is_null($this->id)) {
				//Comprobar si existe el archivo
				$this->borrarImagen();
			}
	
			//Asignar al atributo de imagen el nombre de la imagen
			if ($imagen) {
				$this->imagen = $imagen;
			}
		}
		//Elimina el archivo
		public function borrarImagen()
		{
			//Comprobar si existe el archivo
			$existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
			if ($existeArchivo) {
				unlink(CARPETA_IMAGENES . $this->imagen);
			}
		}
	
		//Validacion
		public static function getErrores()
		{
			return static::$errores;
		}
	
		public function validar()
		{
			static::$errores = [];
			return static::$errores;
		}
		//Lista todas las propiedades 
		public static function all()
		{
			$query = "SELECT * FROM " . static::$tabla;
	
			$resultado = self::consultarSQL($query);
	
			return $resultado;
		}
		//Obtiene determinado numero de registros
		public static function get($cantidad)
		{
			$query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
	
			$resultado = self::consultarSQL($query);
	
			return $resultado;
		}
	
		//Buscar un registro por su id
		public static function find($id)
		{
			$query = "SELECT * FROM ". static::$tabla ." WHERE id = ${id}";
	
			$resultado = self::consultarSQL($query);
	
			return reset($resultado);
		}
	
		public static function consultarSQL($query)
		{
			//Consultar la base de datos 
			$resultado = self::$db->query($query);
	
			//iterar los resultados y cargar en un arreglo vacio
			$array = [];
			while ($registro = $resultado->fetch_assoc()) {
				$array[] = static::crearObjeto($registro);
			}
	
			//liberar la memoria
			$resultado->free();
	
			//retornar los resultados
			return $array;
		}
	
		protected static function crearObjeto($registro)
		{
			//esto significa que se va a crear un nuevo objeto de la clase padre osea propiedad
			$objeto = new static;
	
			foreach ($registro as $key => $value) {
				if (property_exists($objeto, $key)) {
					$objeto->$key = $value;
				}
			}
			return $objeto;
		}
	
		//Sincroniza el objeto en meoria con los cambios realizados por el usuario
		public function  sincronizar($args = [])
		{
			foreach ($args as $key => $value) {
				if (property_exists($this, $key) && !is_null($value)) {
					$this->$key = $value;
				}
			}
		}
}