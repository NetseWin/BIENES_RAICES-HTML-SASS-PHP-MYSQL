<main class="contenedor seccion">
    <h1>Registrar Vendedores</h1>

    <a href="/admin" class="boton boton-verde">
        volver
    </a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form action="/vendedores/crear" class="formulario" method="POST" enctype="multipart/form-data">

		<?php include 'formulario.php'; ?>
        <input type="submit" value="Registrar Vendedor/a" class="boton boton-verde">
    </form>
</main>