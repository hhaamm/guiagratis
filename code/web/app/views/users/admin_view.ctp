<h3>Datos de usuario</h3>

<dl>
    <?php if (!empty($user['User']['avatar']['small']['url'])) { ?>
        <dt class="altrow">Avatar:</dt>
        <dd><?php echo $this->Html->image($user['User']['avatar']['small']['url']) ?></dd>
    <?php } ?>
    
    <dt class="altrow">Nombre de usuario:</dt>
    <dd><?php echo $user['User']['username'] ?></dd>
    
    <dt class="altrow">Email:</dt>
    <dd><?php echo $user['User']['mail'] ?></dd>
    
    <dt class="altrow">Creado:</dt>
    <dd><?php echo date('Y-m-d', $user['User']['created']->sec) ?></dd>
    
    <dt class="altrow">Modificado:</dt>
    <dd><?php echo date('Y-m-d', $user['User']['modified']->sec) ?></dd>
    
    <dt class="altrow">Admin:</dt>
    <dd><?php echo $user['User']['admin'] ?></dd>
    
    <dt class="altrow">Configuración de cuenta:</dt>
    <dd>
        <p>Notificar mensajes: <?php echo $user['User']['notify_on_message'] ?></p>
        <p>Notificar respuestas: <?php echo $user['User']['notify_on_answer'] ?></p>
        <p>Newletter: <?php echo $user['User']['get_newsletter'] ?></p>
        <p>Mostrar email: <?php echo $user['User']['show_email'] ?></p>
    </dd>
    
    <dt class="altrow">Reset password token:</dt>
    <dd><?php echo $user['User']['reset_password_token'] ?></dd>
</dl>

<h3>Datos personales</h3>

<dl>
    <dt class="altrow">Nombre:</dt>
    <dd><?php echo $user['User']['firstname'] ?></dd>
    
    <dt class="altrow">Apellido:</dt>
    <dd><?php echo $user['User']['lastname'] ?></dd>
    
    <dt class="altrow">Teléfono:</dt>
    <dd><?php echo $user['User']['telephone'] ?></dd>
    
    <dt class="altrow">País:</dt>
    <dd><?php echo $user['User']['country'] ?></dd>
    
    <dt class="altrow">Región:</dt>
    <dd><?php echo $user['User']['region'] ?></dd>
    
    <dt class="altrow">Ciudad:</dt>
    <dd><?php echo $user['User']['city'] ?></dd>
    
    <dt class="altrow">Descripción:</dt>
    <dd><p><?php echo $user['User']['description'] ?></p></dd>
</dl>