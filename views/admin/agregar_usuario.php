<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioAdmin'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener lista de sucursales para seleccionar
$querySucursales = $conn->prepare('SELECT id, nombre FROM sucursales');
$querySucursales->execute();
$sucursales = $querySucursales->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $sucursal_id = $_POST['sucursal_id'];

    // Insertar usuario en la tabla usuarios
    $queryUsuario = $conn->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)');
    $queryUsuario->bindParam(':nombre', $nombre);
    $queryUsuario->bindParam(':email', $email);
    $queryUsuario->bindParam(':password', $password); // Aquí deberías cifrar la contraseña antes de guardarla en la base de datos
    $queryUsuario->bindParam(':rol', $rol);
    $queryUsuario->execute();

    // Obtener el ID del usuario insertado
    $usuario_id = $conn->lastInsertId();

    // Asignar la sucursal al usuario según el rol
    if ($rol === 'gerente') {
        $queryGerente = $conn->prepare('INSERT INTO gerentes (usuario_id, sucursal_id) VALUES (:usuario_id, :sucursal_id)');
        $queryGerente->bindParam(':usuario_id', $usuario_id);
        $queryGerente->bindParam(':sucursal_id', $sucursal_id);
        $queryGerente->execute();
    } elseif ($rol === 'empleado') {
        $queryEmpleado = $conn->prepare('INSERT INTO empleados (usuario_id, sucursal_id) VALUES (:usuario_id, :sucursal_id)');
        $queryEmpleado->bindParam(':usuario_id', $usuario_id);
        $queryEmpleado->bindParam(':sucursal_id', $sucursal_id);
        $queryEmpleado->execute();
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/agregar_usuario.css">
    <title>Agregar Usuario</title>
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
        <form class="form" method="POST" action="agregar_usuario.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="admin">Administrador</option>
                <option value="gerente">Gerente</option>
                <option value="empleado">Empleado</option>
            </select>
            <label for="sucursal_id">Sucursal:</label>
            <select id="sucursal_id" name="sucursal_id" required>
                <?php foreach ($sucursales as $sucursal) : ?>
                    <option value="<?php echo $sucursal['id']; ?>"><?php echo htmlspecialchars($sucursal['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <input class="btn-add" type="submit" name="add" value="Agregar Usuario">
        </form>
    </main>
</body>

</html>