<h2>Usuarios</h2>
<h3>Estadísticas</h3>
<p>Número de usuarios: <?php echo $count ?></p>
<p>Número de usuarios activos: <?php echo $countActive." (".$activePercentage."%)" ?></p>
<p>Número de usuarios inactivos: <?php echo $countInactive." (".$inactivePercentage."%)" ?></p>
<p>Número de usuarios administradores: <?php echo $countAdmin ?></p>
<br/>
<h3>Listado</h3>
<table class="users">
    <thead>
        <tr>
            <td>Nombre de usuario</td>
            <td>Email</td>
            <td>Activo</td>
            <td>Admin</td>
            <td>Fecha creación</td>
            <td>Fecha modificación</td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user) { ?>
            <tr>
                <td><?php echo $user['User']['username'] ?></td>
                <td><?php echo $user['User']['mail'] ?></td>
                <td><?php echo $user['User']['active'] ? "Si" : "No" ?></td>
                <td><?php echo $user['User']['admin'] ? "Si" : "No" ?></td>
                <td><?php echo date('Y-m-d', $user['User']['created']->sec) ?></td>
                <td><?php echo date('Y-m-d', $user['User']['modified']->sec) ?></td>
                <td>No hay acciones disponibles</td>
            </tr>
        <?php } ?>
    </tbody>
</table>