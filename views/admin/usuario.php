<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioAdmin'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID de la sucursal desde la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $nombre = $_POST['nombre'];
    $numeroEmpleado = $_POST['numeroEmpleado'];
    $rol = $_POST['rol'];

    $query = $conn->prepare('UPDATE usuarios SET nombre = :nombre, numeroEmpleado = :numeroEmpleado, rol = :rol WHERE id = :id');
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':numeroEmpleado', $numeroEmpleado);
    $query->bindParam(':rol', $rol);
    $query->bindParam(':id', $id);
    $query->execute();

    header("Location: usuario.php?id=$id");
    exit;
}

// Procesar la solicitud de eliminación
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $query = $conn->prepare('DELETE FROM usuarios WHERE id = :id');
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: index.php");
    exit;
}

// Preparar y ejecutar la consulta para obtener los detalles del usuario
$query = $conn->prepare('SELECT * FROM usuarios WHERE id = :id');
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$usuario = $query->fetch();

// Si no se encontró el usuario, redirigir al index
if (!$usuario) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/usuario.css">
    <title>Usuarios</title>
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

        <section class="detalles">
            <h2>Detalles</h2>
            <h3>Nombre: <?php echo htmlspecialchars($usuario['nombre']); ?></h3>
            <p><strong>Numero de empleado:</strong> <?php echo htmlspecialchars($usuario['numeroEmpleado']); ?></p>
            <p><strong>Rol:</strong> <?php echo $usuario['rol']; ?></p>
            <div>
                <button class="btn-update" onclick="openModal()">Editar</button>
                <a class="btn-delete" href="usuario.php?id=<?php echo $id; ?>&action=delete" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sucursal?');">Eliminar</a>
            </div>

        </section>

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <form method="POST" action="usuario.php?id=<?php echo $id; ?>">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                    <label for="numeroEmpleado">Numero Empleado:</label>
                    <input type="text" id="numeroEmpleado" name="numeroEmpleado" value="<?php echo htmlspecialchars($usuario['numeroEmpleado']); ?>" required>
                    <label for="password">Rol:</label>
                    <select name="rol" id="rol">
                        <option value="admin">Administrador</option>
                        <option value="gerente">Gerente</option>
                        <option value="empleado">Empleado</option>
                    </select>
                    <input type="submit" class="btn-update" name="update" value="Actualizar">
                </form>
            </div>
        </div>
    </main>
    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>
</body>

</html>