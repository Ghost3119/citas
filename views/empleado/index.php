<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioEmpleado'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID de la sucursal del empleado
$empleado_id = $_SESSION['idUsuarioEmpleado'];
$query = $conn->prepare('SELECT sucursal_id FROM empleados WHERE usuario_id = :usuario_id');
$query->bindParam(':usuario_id', $empleado_id, PDO::PARAM_INT);
$query->execute();
$sucursal_id = $query->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/index.css">
    <title>Empleado</title>
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

        <section class="secciones">
            <h2>Agendar Cita</h2>
            <div class="container">
                <a href="agendar_cita.php" class="agregar-tarjeta">Agendar Cita</a>
                <?php
                // Obtener el ID de la sucursal del gerente
                $empleado_id = $_SESSION['idUsuarioEmpleado'];
                $query = $conn->prepare('SELECT sucursal_id FROM empleados WHERE usuario_id = :usuario_id');
                $query->bindParam(':usuario_id', $empleado_id, PDO::PARAM_INT);
                $query->execute();
                $sucursal_id = $query->fetchColumn();

                // Consultar las citas de la sucursal
                $query = $conn->prepare('SELECT * FROM citas WHERE sucursal_id = :sucursal_id');
                $query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
                $query->execute();
                $citas = $query->fetchAll();

                foreach ($citas as $cita) {
                ?>
                    <div class="tarjeta">
                        <h3>Fecha: <?php echo $cita['fecha']; ?></h3>
                        <p>Hora: <?php echo $cita['hora']; ?></p>
                        <p>Descripción: <?php echo $cita['descripcion']; ?></p>
                        <a href="cita.php?id=<?php echo $cita['id']; ?>">Ver detalles</a>
                    </div>
                <?php
                }
                ?>
            </div>
        </section>

    </main>
</body>

</html>