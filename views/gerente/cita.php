<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioGerente'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID de la cita desde la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener el ID de la sucursal del gerente
$gerente_id = $_SESSION['idUsuarioGerente'];
$query = $conn->prepare('SELECT sucursal_id FROM gerentes WHERE usuario_id = :usuario_id');
$query->bindParam(':usuario_id', $gerente_id, PDO::PARAM_INT);
$query->execute();
$sucursal_id = $query->fetchColumn();

// Preparar y ejecutar la consulta para obtener los detalles de la cita
$query = $conn->prepare('SELECT * FROM citas WHERE id = :id AND sucursal_id = :sucursal_id');
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
$query->execute();
$cita = $query->fetch();

// Si no se encontró la cita, redirigir a la lista de citas
if (!$cita) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        // Eliminar la cita
        $deleteQuery = $conn->prepare('DELETE FROM citas WHERE id = :id');
        $deleteQuery->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteQuery->execute();
        header("Location: index.php");
        exit;
    } else if (isset($_POST['update'])) {
        // Actualizar la cita
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $descripcion = $_POST['descripcion'];

        $updateQuery = $conn->prepare('UPDATE citas SET fecha = :fecha, hora = :hora, descripcion = :descripcion WHERE id = :id');
        $updateQuery->bindParam(':fecha', $fecha);
        $updateQuery->bindParam(':hora', $hora);
        $updateQuery->bindParam(':descripcion', $descripcion);
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
    <link rel="stylesheet" href="./styles/cita.css">
    <title>Detalles de la Cita</title>
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
        <section>
            <form class="form" class="main" method="POST" action="cita.php?id=<?php echo $id; ?>">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo $cita['fecha']; ?>" required>
                <br>
                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" value="<?php echo $cita['hora']; ?>" required>
                <br>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?php echo $cita['descripcion']; ?></textarea>
                <br>
                <input class="btn-update" type="submit" name="update" value="Actualizar Cita">
                <input class="btn-delete" type="submit" name="delete" value="Eliminar Cita">
            </form>
        </section>
    </main>
</body>

</html>