<?php
require_once '../../db/database.php';
session_start();

if (!isset($_SESSION['idUsuarioGerente'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID de la sucursal del gerente
$gerente_id = $_SESSION['idUsuarioGerente'];
$query = $conn->prepare('SELECT sucursal_id FROM gerentes WHERE usuario_id = :usuario_id');
$query->bindParam(':usuario_id', $gerente_id, PDO::PARAM_INT);
$query->execute();
$sucursal_id = $query->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/index.css">
    <title>Gerente</title>
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
        <section class="secciones">
            <div class="container">
                <h3>Agendar Cita</h3>
                <a class="agregar-tarjeta" href="agendar_cita.php">Agendar Cita</a>
                <?php
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
        <section class="secciones">
            <div class="container">
                <h3>Empleados</h3>
                <a class="agregar-tarjeta" href="agregar_usuario_empleado.php">Agregar Empleado</a>
                <?php
                // Obtener el ID de la sucursal del gerente
                $gerente_id = $_SESSION['idUsuarioGerente'];
                $query = $conn->prepare('SELECT sucursal_id FROM gerentes WHERE usuario_id = :usuario_id');
                $query->bindParam(':usuario_id', $gerente_id, PDO::PARAM_INT);
                $query->execute();
                $sucursal_id = $query->fetchColumn();

                // Consultar los empleados de la sucursal
                $query = $conn->prepare('
            SELECT e.id, u.nombre, u.email, u.id AS usuario_id 
            FROM empleados e 
            INNER JOIN usuarios u ON e.usuario_id = u.id 
            WHERE e.sucursal_id = :sucursal_id
        ');
                $query->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
                $query->execute();
                $empleados = $query->fetchAll();

                foreach ($empleados as $empleado) {
                ?>
                    <div class="tarjeta">
                        <h3>Nombre: <?php echo htmlspecialchars($empleado['nombre']); ?></h3>
                        <p>Correo: <?php echo htmlspecialchars($empleado['email']); ?></p>
                        <a href="empleado.php?id=<?php echo $empleado['id']; ?>">Ver detalles</a>
                    </div>
                <?php
                }
                ?>
            </div>
        </section>



        </section>
    </main>
</body>

</html>