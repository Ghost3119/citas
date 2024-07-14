<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioEmpleado'])) {
    header("Location: ../../index.php");
    exit;
}

$sucursal_id = $_SESSION['sucursal_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_visitante = $_POST['nombre_visitante'];
    $motivo = $_POST['motivo'];
    $hora_llegada = $_POST['hora_llegada'];
    $observaciones = $_POST['observaciones'];

    $query = $conn->prepare('INSERT INTO visitas (sucursal_id, nombre_visitante, motivo, hora_llegada, observaciones) VALUES (:sucursal_id, :nombre_visitante, :motivo, :hora_llegada, :observaciones)');
    $query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
    $query->bindParam(':nombre_visitante', $nombre_visitante);
    $query->bindParam(':motivo', $motivo);
    $query->bindParam(':hora_llegada', $hora_llegada);
    $query->bindParam(':observaciones', $observaciones);
    $query->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/registrar_visita.css">
    <title>Registrar Visita</title>
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
        <form class="form" method="POST" action="registrar_visita.php">
            <label for="nombre_visitante">Nombre del Visitante:</label>
            <input type="text" id="nombre_visitante" name="nombre_visitante" required>

            <label for="motivo">Motivo:</label>
            <input type="text" id="motivo" name="motivo" required>

            <label for="hora_llegada">Hora de Llegada:</label>
            <input type="time" id="hora_llegada" name="hora_llegada" required>

            <label for="observaciones">Observaciones:</label>
            <textarea id="observaciones" name="observaciones"></textarea>

            <input class="btn-registrar" type="submit" value="Registrar Visita">
        </form>
    </main>
</body>

</html>