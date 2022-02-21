<?php

namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use Model\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;

class PropiedadController
{

    public static function index(Router $router)
    {

        $propiedades = Propiedad::all();

        $vendedores = Vendedor::all();

        //Muestra un mensaje condicional
        $resultado = $_GET['resultado'] ?? null;

        $router->render('propiedades/admin', [
            'propiedades' => $propiedades,
            'resultado' => $resultado,
            'vendedores' => $vendedores
        ]);
    }

    public static function crear(Router $router)
    {

        $propiedad = new Propiedad();
        $vendedores = Vendedor::all();
        //Arreglo con mensajes de errores
        $errores = Propiedad::getErrores();

        //Ejecutar el codigo despues de que el usuario envia el formulario..
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //crea una nueva instancia

            $propiedad = new Propiedad($_POST['propiedad']);


            //Generar nombre unico para las imagenes
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            //Setear la imagen
            //Realiza un resize a la imagen con intervention
            if ($_FILES['propiedad']['tmp_name']['imagen']) {
                $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
                $propiedad->setImagen($nombreImagen);
            }

            $errores = $propiedad->validar();

            //Revisar que el array de errores este vacio, si esta vacio realiza la insercion en la base de datos
            if (empty($errores)) {

                //Crear la carpeta para subir imagenes
                if (!is_dir(CARPETA_IMAGENES)) {
                    mkdir(CARPETA_IMAGENES);
                }

                //Guarda la imagen en el servidor
                $image->save(CARPETA_IMAGENES . $nombreImagen);

                $propiedad->guardar();
            }
        }

        $router->render('propiedades/crear', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }
    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/admin');
        $propiedad = Propiedad::find($id);
        $vendedores = Vendedor::all();
        $errores = Propiedad::getErrores();
        //Ejecutar el codigo despues de que el usuario envia el formulario..
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Asignar los atributos
            $args = $_POST['propiedad'];


            $propiedad->sincronizar($args);

            //Validaion
            $errores = $propiedad->validar();

            //Generar nombre unico para las imagenes
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            //Subida de archivos
            if ($_FILES['propiedad']['tmp_name']['imagen']) {
                $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
                $propiedad->setImagen($nombreImagen);
            }

            //Revisar que el array de errores este vacio, si esta vacio realiza la insercion en la base de datos
            if (empty($errores)) {
                if ($_FILES['propiedad']['tmp_name']['imagen']) {
                    //almacenar la imagen
                    $image->save(CARPETA_IMAGENES . $nombreImagen);
                }
                $propiedad->guardar();
            }
        }

        $router->render('/propiedades/actualizar', [
            'propiedad' => $propiedad,
            'errores' => $errores,
            'vendedores' => $vendedores
        ]);
    }
    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            //para validar que sea un numero y no se manipule desde la url
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {

                $tipo = $_POST['tipo'];

                if (validarTipoContenido($tipo)) {
                      //buscar la propiedad que se va eliminar
                      $propiedad = Propiedad::find($id);
                      // y eliminarla
                      $propiedad->eliminar();
                   
                }
            }
        }
    }
}
