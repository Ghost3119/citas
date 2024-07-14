<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioEmpleado']) && !isset($_SESSION['idUsuarioGerente'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID de la visita desde la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener el ID de la sucursal del empleado o gerente
$usuario_id = $_SESSION['idUsuarioEmpleado'] ?? $_SESSION['idUsuarioGerente'];
$query = $conn->prepare('SELECT sucursal_id FROM empleados WHERE usuario_id = :usuario_id UNION SELECT sucursal_id FROM gerentes WHERE usuario_id = :usuario_id');
$query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$query->execute();
$sucursal_id = $query->fetchColumn();

// Preparar y ejecutar la consulta para obtener los detalles de la visita
$query = $conn->prepare('SELECT * FROM visitas WHERE id = :id AND sucursal_id = :sucursal_id');
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
$query->execute();
$visita = $query->fetch();

// Si no se encontró la visita, redirigir a la lista de visitas
if (!$visita) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        // Eliminar la visita
        $deleteQuery = $conn->prepare('DELETE FROM visitas WHERE id = :id');
        $deleteQuery->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteQuery->execute();
        header("Location: index.php");
        exit;
    } else if (isset($_POST['update'])) {
        // Actualizar la visita
        $nombre_visitante = $_POST['nombre_visitante'];
        $motivo = $_POST['motivo'];
        $hora_llegada = $_POST['hora_llegada'];
        $observaciones = $_POST['observaciones'];

        $updateQuery = $conn->prepare('UPDATE visitas SET nombre_visitante = :nombre_visitante, motivo = :motivo, hora_llegada = :hora_llegada, observaciones = :observaciones WHERE id = :id');
        $updateQuery->bindParam(':nombre_visitante', $nombre_visitante);
        $updateQuery->bindParam(':motivo', $motivo);
        $updateQuery->bindParam(':hora_llegada', $hora_llegada);
        $updateQuery->bindParam(':observaciones', $observaciones);
        $updateQuery->bindParam(':id', $id, PDO::PARAM_INT);
        $updateQuery->execute();
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/visita.css">
    <title>Detalles de la Visita</title>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="navbar-elements">
                <h1>Bienvenido <?php echo $_SESSION['nombreEmpleado'] ?? $_SESSION['nombreGerente']; ?></h1>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="../../logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="main">
        <section>
            <form class="form" method="POST" action="visita.php?id=<?php echo $id; ?>">
                <label for="nombre_visitante">Nombre del Visitante:</label>
                <input type="text" id="nombre_visitante" name="nombre_visitante" value="<?php echo htmlspecialchars($visita['nombre_visitante']); ?>" required>
                <label for="motivo">Motivo:</label>
                <input type="text" id="motivo" name="motivo" value="<?php echo htmlspecialchars($visita['motivo']); ?>" required>
                <label for="hora_llegada">Hora de Llegada:</label>
                <input type="time" id="hora_llegada" name="hora_llegada" value="<?php echo $visita['hora_llegada']; ?>" required>
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" required><?php echo htmlspecialchars($visita['observaciones']); ?></textarea>
                <input class="btn-update" type="submit" name="update" value="Actualizar Visita">
                <input class="btn-delete" type="submit" name="delete" value="Eliminar Visita">
            </form>
        </section>
    </main>
</body>

</html>