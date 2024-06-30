<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioEmpleado'])) {
    header("Location: ../../index.php");
    exit;
}

$empleado_id = $_SESSION['idUsuarioEmpleado'];
$query = $conn->prepare('SELECT sucursal_id FROM empleados WHERE usuario_id = :usuario_id');
$query->bindParam(':usuario_id', $empleado_id, PDO::PARAM_INT);
$query->execute();
$sucursal_id = $query->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $descripcion = $_POST['descripcion'];

    $queryCita = $conn->prepare('INSERT INTO citas (sucursal_id, empleado_id, fecha, hora, descripcion) VALUES (:sucursal_id, :empleado_id, :fecha, :hora, :descripcion)');
    $queryCita->bindParam(':sucursal_id', $sucursal_id);
    $queryCita->bindParam(':empleado_id', $empleado_id);
    $queryCita->bindParam(':fecha', $fecha);
    $queryCita->bindParam(':hora', $hora);
    $queryCita->bindParam(':descripcion', $descripcion);
    $queryCita->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/agendar_cita.css">
    <title>Agendar Cita</title>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="navbar-elements">
                <h1>Bienvenido <?php echo $_SESSION['nombreEmpleado']; ?></h1>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="../../logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="main">
        <form class="form" method="POST" action="agendar_cita.php">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required>
            <br>
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" required>
            <br>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>
            <br>
            <input class="btn-agendar" type="submit" value="Agendar Cita">
        </form>
    </main>
</body>

</html>