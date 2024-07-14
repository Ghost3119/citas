<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioAdmin'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $numeroSucursal = $_POST['numeroSucursal'];
    $direccion = $_POST['direccion'];

    $query = $conn->prepare('INSERT INTO sucursales (numeroSucursal, direccion) VALUES (:numeroSucursal, :direccion)');
    $query->bindParam(':numeroSucursal', $numeroSucursal);
    $query->bindParam(':direccion', $direccion);
    $query->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/agregar_sucursal.css">
    <title>Agregar Sucursal</title>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="navbar-elements">
                <h1>Bienvenido <?php echo $_SESSION['nombreAdmin']; ?></h1>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="../../logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="main">
        <form class="form" method="POST" action="agregar_sucursal.php">
            <label for="numeroSucursal">Numero Sucursal:</label>
            <input type="text" id="numeroSucursal" name="numeroSucursal" required>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>
            <input class="btn-add" type="submit" name="add" value="Agregar Sucursal">
        </form>
    </main>
</body>

</html>