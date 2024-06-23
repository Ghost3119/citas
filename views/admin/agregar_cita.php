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
    $empleado_id = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $descripcion = $_POST['descripcion'];

    $query = $conn->prepare('INSERT INTO citas (sucursal_id, empleado_id, fecha, hora, descripcion) VALUES (:sucursal_id, :empleado_id, :fecha, :hora, :descripcion)');
    $query->bindParam(':sucursal_id', $sucursal_id);
    $query->bindParam(':empleado_id', $empleado_id);
    $query->bindParam(':fecha', $fecha);
    $query->bindParam(':hora', $hora);
    $query->bindParam(':descripcion', $descripcion);
    $query->execute();

    header("Location: sucursal.php?id=$sucursal_id");
    exit;
}

// Obtener lista de empleados para seleccionar
$queryEmpleados = $conn->prepare('SELECT u.id, u.nombre FROM empleados e JOIN usuarios u ON e.usuario_id = u.id WHERE e.sucursal_id = :sucursal_id');
$queryEmpleados->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
$queryEmpleados->execute();
$empleados = $queryEmpleados->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/agregar_cita.css">
    <title>Agregar Cita</title>
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
        <form class="form" method="POST" action="agregar_cita.php?sucursal_id=<?php echo $sucursal_id; ?>">
            <label for="empleado_id">Empleado:</label>
            <select id="empleado_id" name="empleado_id" required>
                <?php foreach ($empleados as $empleado) : ?>
                    <option value="<?php echo $empleado['id']; ?>"><?php echo htmlspecialchars($empleado['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required>
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" required>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"></textarea>
            <input class="add-cita" type="submit" name="add" value="Agregar Cita">
        </form>
    </main>
</body>

</html>