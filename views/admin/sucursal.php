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
    $direccion = $_POST['direccion'];

    $query = $conn->prepare('UPDATE sucursales SET nombre = :nombre, direccion = :direccion WHERE id = :id');
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':direccion', $direccion);
    $query->bindParam(':id', $id);
    $query->execute();

    header("Location: sucursal.php?id=$id");
    exit;
}

// Procesar la solicitud de eliminación
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $query = $conn->prepare('DELETE FROM sucursales WHERE id = :id');
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: index.php");
    exit;
}

// Preparar y ejecutar la consulta para obtener los detalles de la sucursal
$query = $conn->prepare('SELECT * FROM sucursales WHERE id = :id');
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$sucursal = $query->fetch();

// Si no se encontró la sucursal, redirigir a la lista de sucursales
if (!$sucursal) {
    header("Location: index.php");
    exit;
}

// Obtener citas de la sucursal
$queryCitas = $conn->prepare('SELECT * FROM citas WHERE sucursal_id = :id');
$queryCitas->bindParam(':id', $id, PDO::PARAM_INT);
$queryCitas->execute();
$citas = $queryCitas->fetchAll();

// Obtener gerentes de la sucursal
$queryGerentes = $conn->prepare('
    SELECT u.id, u.nombre, u.email 
    FROM gerentes g 
    JOIN usuarios u ON g.usuario_id = u.id 
    WHERE g.sucursal_id = :id
');
$queryGerentes->bindParam(':id', $id, PDO::PARAM_INT);
$queryGerentes->execute();
$gerentes = $queryGerentes->fetchAll();

// Obtener empleados de la sucursal
$queryEmpleados = $conn->prepare('
    SELECT u.id, u.nombre, u.email 
    FROM empleados e 
    JOIN usuarios u ON e.usuario_id = u.id 
    WHERE e.sucursal_id = :id
');
$queryEmpleados->bindParam(':id', $id, PDO::PARAM_INT);
$queryEmpleados->execute();
$empleados = $queryEmpleados->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/sucursal.css">
    <title>Sucursal</title>
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
            <h3><?php echo htmlspecialchars($sucursal['nombre']); ?></h3>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($sucursal['direccion']); ?></p>
            <div>
                <button class="btn-update" onclick="openModal()">Editar</button>
                <a class="btn-delete" href="sucursal.php?id=<?php echo $id; ?>&action=delete" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sucursal?');">Eliminar</a>
            </div>

        </section>

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <form method="POST" action="sucursal.php?id=<?php echo $id; ?>">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($sucursal['nombre']); ?>" required>
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($sucursal['direccion']); ?>" required>
                    <input type="submit" class="btn-update" name="update" value="Actualizar">
                </form>
            </div>
        </div>

        <section class="citas">
            <h2>Citas</h2>
            <?php if ($citas) : ?>
                <a class="add-cita" href="agregar_cita.php?sucursal_id=<?php echo $id; ?>">Agregar Cita</a>
                <ul>
                    <?php foreach ($citas as $cita) : ?>
                        <li>
                            Fecha: <?php echo htmlspecialchars($cita['fecha']) ?></li>
                        <li>
                            Hora: <?php echo htmlspecialchars($cita['hora']); ?>
                        </li>
                        <li>
                            Descripción: <?php echo htmlspecialchars($cita['descripcion']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No hay citas para esta sucursal.</p>
                <a class="add-cita" href="agregar_cita.php?sucursal_id=<?php echo $id; ?>">Agregar Cita</a>
            <?php endif; ?>
        </section>

        <section class="gerentes">
            <h2>Gerentes</h2>
            <?php if ($gerentes) : ?>
                <a class="add-cita" href="agregar_gerente.php?sucursal_id=<?php echo $id; ?>">Agregar Gerente</a>
                <ul>
                    <?php foreach ($gerentes as $gerente) : ?>
                        <li><?php echo htmlspecialchars($gerente['nombre'] . ' (' . $gerente['email'] . ')'); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No hay gerentes para esta sucursal.</p>
                <a class="add-cita" href="agregar_gerente.php?sucursal_id=<?php echo $id; ?>">Agregar Gerente</a>
            <?php endif; ?>
        </section>

        <section class="empleados">
            <h2>Empleados</h2>
            <?php if ($empleados) : ?>
                <a class="add-cita" href="agregar_empleado.php?sucursal_id=<?php echo $id; ?>">Agregar Empleado</a>
                <ul>
                    <?php foreach ($empleados as $empleado) : ?>
                        <li><?php echo htmlspecialchars($empleado['nombre'] . ' (' . $empleado['email'] . ')'); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No hay empleados para esta sucursal.</p>
                <a class="add-cita" href="agregar_empleado.php?sucursal_id=<?php echo $id; ?>">Agregar Empleado</a>
            <?php endif; ?>
        </section>
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