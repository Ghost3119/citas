<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioAdmin'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID de la sucursal desde la URL
$sucursal_id = isset($_GET['sucursal_id']) ? (int)$_GET['sucursal_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $usuario_id = $_POST['usuario_id'];

    $query = $conn->prepare('INSERT INTO gerentes (usuario_id, sucursal_id) VALUES (:usuario_id, :sucursal_id)');
    $query->bindParam(':usuario_id', $usuario_id);
    $query->bindParam(':sucursal_id', $sucursal_id);
    $query->execute();

    header("Location: sucursal.php?id=$sucursal_id");
    exit;
}

// Obtener lista de usuarios para seleccionar
$queryUsuarios = $conn->prepare('SELECT id, nombre FROM usuarios WHERE rol = "gerente"');
$queryUsuarios->execute();
$usuarios = $queryUsuarios->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/agregar_gerente.css">
    <title>Agregar Gerente</title>
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
        <form class="form" method="POST" action="agregar_gerente.php?sucursal_id=<?php echo $sucursal_id; ?>">
            <label for="usuario_id">Usuario:</label>
            <select id="usuario_id" name="usuario_id" required>
                <?php foreach ($usuarios as $usuario) : ?>
                    <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <input class="add-gerente" type="submit" name="add" value="Agregar Gerente">
        </form>
    </main>
</body>

</html>