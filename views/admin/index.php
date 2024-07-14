<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioAdmin'])) {
    header("Location: ../../index.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/index.css">
    <title>Admin</title>
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

        <section class="secciones">
            <h2>Sucursales</h2>
            <div class="container">
                <a href="agregar_sucursal.php" class="agregar-tarjeta">Agregar Sucursal</a>
                <?php
                $query = $conn->prepare('SELECT * FROM sucursales');
                $query->execute();
                $sucursales = $query->fetchAll();
                foreach ($sucursales as $sucursal) {
                ?>
                    <div class="tarjeta">
                        <h3>Numero de tienda: <?php echo $sucursal['numeroSucursal']; ?></h3>
                        <p>Dirección: <?php echo $sucursal['direccion']; ?></p>
                        <a href="sucursal.php?id=<?php echo $sucursal['id']; ?>">Ver detalles</a>
                    </div>
                <?php
                }
                ?>

            </div>
        </section>

        <section class="secciones">
            <h2>Usuarios</h2>
            <div class="container">
                <a href="agregar_usuario.php" class="agregar-tarjeta">Agregar Usuario</a>
                <?php
                $query = $conn->prepare('SELECT u.id, u.nombre, u.numeroEmpleado, u.rol, s.numeroSucursal as sucursal FROM usuarios u LEFT JOIN gerentes g ON u.id = g.usuario_id LEFT JOIN empleados e ON u.id = e.usuario_id LEFT JOIN sucursales s ON g.sucursal_id = s.id OR e.sucursal_id = s.id');
                $query->execute();
                $usuarios = $query->fetchAll();
                foreach ($usuarios as $usuario) {
                ?>
                    <div class="tarjeta">
                        <h3>Nombre: <?php echo $usuario['nombre']; ?></h3>
                        <p>Numero Empleado: <?php echo $usuario['numeroEmpleado']; ?></p>
                        <p>Rol: <?php echo $usuario['rol']; ?></p>
                        <p>Numero Sucursal: <?php echo $usuario['sucursal']; ?></p>
                    </div>
                <?php
                }
                ?>
            </div>
        </section>
    </main>
</body>

</html>