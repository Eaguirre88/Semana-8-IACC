<?php
// Iniciar la sesión
session_start();


// Regenerar el ID de sesión periódicamente
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) { // 30 minutos
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// Función para agregar productos al carrito
function agregarProducto($producto, $cantidad, $precio) {
    if (isset($_SESSION['carrito'][$producto])) {
        $_SESSION['carrito'][$producto]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$producto] = array('cantidad' => $cantidad, 'precio' => $precio);
    }
}

// Función para eliminar un producto del carrito
function eliminarProducto($producto) {
    if (isset($_SESSION['carrito'][$producto])) {
        unset($_SESSION['carrito'][$producto]);
    }
}

// Función para vaciar el carrito
function vaciarCarrito() {
    $_SESSION['carrito'] = array();
}

// Función para modificar la cantidad de un producto
function modificarCantidad($producto, $cantidad) {
    if (isset($_SESSION['carrito'][$producto])) {
        $_SESSION['carrito'][$producto]['cantidad'] = $cantidad;
        if ($_SESSION['carrito'][$producto]['cantidad'] <= 0) {
            eliminarProducto($producto);
        }
    }
}

// Manejar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['producto']) && isset($_POST['cantidad']) && isset($_POST['precio'])) {
        agregarProducto($_POST['producto'], $_POST['cantidad'], $_POST['precio']);
    }
    if (isset($_POST['eliminar'])) {
        eliminarProducto($_POST['eliminar']);
    }
    if (isset($_POST['vaciar'])) {
        vaciarCarrito();
    }
    if (isset($_POST['modificar']) && isset($_POST['nuevaCantidad'])) {
        modificarCantidad($_POST['modificar'], $_POST['nuevaCantidad']);
    }
}

// Calcular el total
$total = 0;
foreach ($_SESSION['carrito'] as $producto => $detalles) {
    $total += $detalles['cantidad'] * $detalles['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Carrito de Compras</h1>

    <!-- Formulario para agregar productos -->
    <form method="post">
        <label for="producto">Producto:</label>
        <input type="text" name="producto" id="producto" required>
        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" required>
        <label for="precio">Precio:</label>
        <input type="text" name="precio" id="precio" required>
        <button type="submit">Agregar al Carrito</button>
    </form>

    <!-- Mostrar productos en el carrito -->
    <h2>Productos en el Carrito</h2>
    <?php
    if (!empty($_SESSION['carrito'])) {
        echo "<ul>";
        foreach ($_SESSION['carrito'] as $producto => $detalles) {
            echo "<li>$producto - Cantidad: {$detalles['cantidad']} - Precio: {$detalles['precio']} - Total: " . ($detalles['cantidad'] * $detalles['precio']);
            echo " <form method='post' style='display:inline;'><button type='submit' name='eliminar' value='$producto'>Eliminar</button></form>";
            echo " <form method='post' style='display:inline;'>
                        <input type='number' name='nuevaCantidad' min='1' value='{$detalles['cantidad']}'>
                        <button type='submit' name='modificar' value='$producto'>Modificar</button>
                   </form>
                 </li>";
        }
        echo "</ul>";
        echo "<h3>Total del Carrito: $total</h3>";
        echo "<form method='post'><button type='submit' name='vaciar'>Vaciar Carrito</button></form>";
    } else {
        echo "<p>El carrito está vacío.</p>";
    }
    ?>
</body>
</html>
