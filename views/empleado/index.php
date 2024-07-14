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
            <h2>Registrar visitas</h2>
            <div class="container">
                <a href="registrar_visita.php" class="agregar-tarjeta">Registrar visita</a>
                <?php
                // Obtener el ID de la sucursal del gerente
                $empleado_id = $_SESSION['idUsuarioEmpleado'];
                $query = $conn->prepare('SELECT sucursal_id FROM empleados WHERE usuario_id = :usuario_id');
                $query->bindParam(':usuario_id', $empleado_id, PDO::PARAM_INT);
                $query->execute();
                $sucursal_id = $query->fetchColumn();

                // Consultar las visitas de la sucursal
                $query = $conn->prepare('SELECT * FROM visitas WHERE sucursal_id = :sucursal_id');
                $query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
                $query->execute();
                $citas = $query->fetchAll();

                foreach ($citas as $cita) {
                    /*
                    campos sucursal_id	nombre_visitante	motivo	hora_llegada	observaciones	
                    */
                ?>
                    <div class="tarjeta">
                        <h3>Nombre del visitante: <?php echo $cita['nombre_visitante']; ?></h3>
                        <p>Motivo: <?php echo $cita['motivo']; ?></p>
                        <p>Hora de llegada: <?php echo $cita['hora_llegada']; ?></p>
                        <p>Observaciones: <?php echo $cita['observaciones']; ?></p>
                        <a href="visita.php?id=<?php echo $cita['id']; ?>">Ver detalles</a>

                    </div>
                <?php
                }
                ?>
            </div>
        </section>
    </main>
</body>

</html>