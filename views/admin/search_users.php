<?php
require_once '../../db/database.php';

$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Preparar la consulta SQL
$query = $conn->prepare('
    SELECT u.id, u.nombre, u.numeroEmpleado, u.rol, s.numeroSucursal as sucursal
    FROM usuarios u
    LEFT JOIN gerentes g ON u.id = g.usuario_id
    LEFT JOIN empleados e ON u.id = e.usuario_id
    LEFT JOIN sucursales s ON g.sucursal_id = s.id OR e.sucursal_id = s.id
    WHERE u.numeroEmpleado LIKE :buscar
');
$query->bindValue(':buscar', '%' . $buscar . '%');
$query->execute();
$usuarios = $query->fetchAll();

// Imprimir los resultados
foreach ($usuarios as $usuario) {
?>
    <a href="agregar_usuario.php" id="agregar_user" class="agregar-tarjeta">Agregar Usuario</a>
    <div class="tarjeta">
        <h3>Nombre: <?php echo htmlspecialchars($usuario['nombre']); ?></h3>
        <p>Numero Empleado: <?php echo htmlspecialchars($usuario['numeroEmpleado']); ?></p>
        <p>Rol: <?php echo htmlspecialchars($usuario['rol']); ?></p>
        <p>Numero Sucursal: <?php echo htmlspecialchars($usuario['sucursal']); ?></p>
        <a href="usuario.php?id=<?php echo htmlspecialchars($usuario['id']); ?>">Ver detalles</a>
    </div>
<?php
}
?>