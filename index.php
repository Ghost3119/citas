<?php
session_start();
require_once './db/database.php';

if (isset($_POST['iniciar'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $conn->prepare('SELECT * FROM usuarios where email=:email AND password=:password');

    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);

    $query->execute();

    $count = $query->rowCount();
    $campo = $query->fetch();

    if ($count) {
        if ($campo['rol'] == 'admin') {
            $_SESSION['idUsuarioAdmin'] = $campo['id'];
            $_SESSION['emailAdmin'] = $email;
            $_SESSION['nombreAdmin'] = $campo['nombre'];
            header("location: ./views/admin/index.php");
            exit();
        }
        if ($campo['rol'] == 'gerente') {
            $_SESSION['idUsuarioGerente'] = $campo['id'];
            $_SESSION['emailGerente'] = $email;
            $_SESSION['nombreGerente'] = $campo['nombre'];
            header("location: ./views/gerente/index.php");
            exit();
        }
        if ($campo['rol'] == 'empleado') {
            $_SESSION['idUsuarioEmpleado'] = $campo['id'];
            $_SESSION['emailEmpleado'] = $email;
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
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input class="btn-login" type="submit" name="iniciar" value="Iniciar sesión">
        </form>
    </main>

</body>

</html>