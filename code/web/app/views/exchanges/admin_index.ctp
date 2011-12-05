<h2>Ofertas y pedidos</h2>
<h3>Estadísticas</h3>
<p>Número de ofertas y pedidos: <?php echo $count ?></p>
<p>Número de ofertas: <?php echo $countOffer ?></p>
<p>Número de pedidos: <?php echo $countRequest ?></p>
<br/>
<h3>Listado</h3>
<table class="exchanges">
    <thead>
        <tr>
            <td>Título</td>
            <td>Creador</td>
            <td>Tipo</td>
            <td>Estado</td>
            <td>Comentarios</td>
            <td>Fecha creación</td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($exchanges as $exchange) { ?>
            <tr>
                <td><?php echo $exchange['Exchange']['title'] ?></td>
                <td><?php echo $exchange['Exchange']['username'] ?></td>
                <td><?php echo $exchange['Exchange']['exchange_type_id'] == Configure::read('ExchangeType.Request') ? 'Pedido' : 'Oferta' ?></td>
                <td><?php echo $exchange['Exchange']['state'] ?></td>
                <td><?php echo isset($exchange['Exchange']['comments']) ? count($exchange['Exchange']['comments']) : 0 ?></td>
                <td><?php echo date('Y-m-d', $exchange['Exchange']['created']); ?></td>
                <td><?php echo $this->Html->link('Ver', array('controller'=>'exchanges', 'action'=>'view', 'admin'=>false, $exchange['Exchange']['_id'])); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>