<?php
session_start();
require_once './db/database.php';

if (isset($_POST['iniciar'])) {
    $numeroEmpleado = $_POST['numeroEmpleado'];
    $password = $_POST['password'];

    $query = $conn->prepare('SELECT * FROM usuarios where numeroEmpleado=:numeroEmpleado AND password=:password');

    $query->bindParam(':numeroEmpleado', $numeroEmpleado);
    $query->bindParam(':password', $password);

    $query->execute();

    $count = $query->rowCount();
    $campo = $query->fetch();

    if ($count) {
        if ($campo['rol'] == 'admin') {
            $_SESSION['idUsuarioAdmin'] = $campo['id'];
            $_SESSION['numeroEmpleadoAdmin'] = $numeroEmpleado;
            $_SESSION['nombreAdmin'] = $campo['nombre'];
            header("location: ./views/admin/index.php");
            exit();
        }
        if ($campo['rol'] == 'gerente') {
            // Obtener el ID de la sucursal del gerente
            $query = $conn->prepare('SELECT sucursal_id FROM gerentes WHERE usuario_id = :usuario_id');
            $query->bindParam(':usuario_id', $campo['id'], PDO::PARAM_INT);
            $query->execute();
            $sucursal_id = $query->fetchColumn();

            $_SESSION['sucursal_id'] = $sucursal_id;
            $_SESSION['idUsuarioGerente'] = $campo['id'];
            $_SESSION['numeroEmpleadoGerente'] = $numeroEmpleado;
            $_SESSION['nombreGerente'] = $campo['nombre'];
            header("location: ./views/gerente/index.php");
            exit();
        }
        if ($campo['rol'] == 'empleado') {
            // Obtener el ID de la sucursal del empleado
            $query = $conn->prepare('SELECT sucursal_id FROM empleados WHERE usuario_id = :usuario_id');
            $query->bindParam(':usuario_id', $campo['id'], PDO::PARAM_INT);
            $query->execute();
            $sucursal_id = $query->fetchColumn();

            $_SESSION['sucursal_id'] = $sucursal_id;
            $_SESSION['idUsuarioEmpleado'] = $campo['id'];
            $_SESSION['numeroEmpleadoEmpleado'] = $numeroEmpleado;
            $_SESSION['nombreEmpleado'] = $campo['nombre'];
            header("location: ./views/empleado/index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Iniciar sesión</title>
</head>

<body>
    <main class="main">
        <h2 class="titulo">Iniciar sesión</h2>
        <form class="form" action="" method="POST">
            <label for="numeroEmpleado">Numero Empleado:</label>
            <input type="text" id="numeroEmpleado" name="numeroEmpleado" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input class="btn-login" type="submit" name="iniciar" value="Iniciar sesión">
        </form>
    </main>

</body>

</html>