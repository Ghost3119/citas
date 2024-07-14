<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioGerente'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID del empleado desde la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener el ID de la sucursal del gerente
$gerente_id = $_SESSION['idUsuarioGerente'];
$query = $conn->prepare('SELECT sucursal_id FROM gerentes WHERE usuario_id = :usuario_id');
$query->bindParam(':usuario_id', $gerente_id, PDO::PARAM_INT);
$query->execute();
$sucursal_id = $query->fetchColumn();

// Preparar y ejecutar la consulta para obtener los detalles del empleado
$query = $conn->prepare('
    SELECT e.id, u.nombre, u.numeroEmpleado, e.sucursal_id 
    FROM empleados e 
    INNER JOIN usuarios u ON e.usuario_id = u.id 
    WHERE e.id = :id AND e.sucursal_id = :sucursal_id
');
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
$query->execute();
$empleado = $query->fetch();

// Si no se encontró el empleado, redirigir a la lista de empleados
if (!$empleado) {
    header("Location: index_gerente.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        // Eliminar el empleado
        $deleteQuery = $conn->prepare('DELETE FROM empleados WHERE id = :id');
        $deleteQuery->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteQuery->execute();
        header("Location: index_gerente.php");
        exit;
    } else if (isset($_POST['update'])) {
        // Actualizar el empleado
        $nombre = $_POST['nombre'];
        $numeroEmpleado = $_POST['numeroEmpleado'];

        $updateUserQuery = $conn->prepare('UPDATE usuarios SET nombre = :nombre, numeroEmpleado  = :numeroEmpleado  WHERE id = (SELECT usuario_id FROM empleados WHERE id = :id)');
        $updateUserQuery->bindParam(':nombre', $nombre);
        $updateUserQuery->bindParam(':numeroEmpleado', $numeroEmpleado);
        $updateUserQuery->bindParam(':id', $id, PDO::PARAM_INT);
        $updateUserQuery->execute();
        header("Location: empleado.php?id=" . $id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/empleado.css">
    <title>Detalles del Empleado</title>
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

        <form class="form" method="POST" action="empleado.php?id=<?php echo $id; ?>">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>
            <label for="numeroEmpleado">Numero Empleado :</label>
            <input type="text" id="numeroEmpleado" name="numeroEmpleado" value="<?php echo htmlspecialchars($empleado['numeroEmpleado']); ?>" required>
            <input class="btn-update" type="submit" name="update" value="Actualizar Empleado">
            <input class="btn-delete" type="submit" name="delete" value="Eliminar Empleado">
        </form>

    </main>
</body>

</html>