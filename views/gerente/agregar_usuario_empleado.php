<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioGerente'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sucursal_id = $_SESSION['sucursal_id'];  // La sucursal del gerente

    $query = $conn->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, "empleado")');
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);
    $query->execute();
    $usuario_id = $conn->lastInsertId();

    $query = $conn->prepare('INSERT INTO empleados (usuario_id, sucursal_id) VALUES (:usuario_id, :sucursal_id)');
    $query->bindParam(':usuario_id', $usuario_id);
    $query->bindParam(':sucursal_id', $sucursal_id);
    $query->execute();

    header("Location: index_gerente.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/agregar_usuario_empleado.css">
    <title>Agregar Empleado</title>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="navbar-elements">
                <h1>Bienvenido <?php echo $_SESSION['nombreGerente']; ?></h1>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="../../logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="main">
        <form class="form" method="POST" action="agregar_usuario_empleado.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input class="btn-agregar" type="submit" value="Agregar Empleado">
        </form>
    </main>
</body>

</html>