<?php

$this->set('documentData', array(
    'xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));

$this->set('channelData', array(
    'title' => __("Posts mas recientes.", true),
    'link' => $this->Html->url('/', true),
    'description' => __("Guia Gratis es una página para regalar y pedir cualquier cosa.", true),
    'language' => 'es-ar'));

foreach ($exchanges as $entry) {
    $postTime = strtotime($entry['Exchange']['created']);

    $entryLink = array(
        'controller' => 'exchanges',
        'action' => 'view',
        $entry['Exchange']['_id']);
    // deberías importar Sanitize
    App::import('Sanitize');
    // Acá es donde se limpia el cuerpo del texto para la salida como la descripción
    // de los items rss, esto necesita tener solo texto para asegurarnos de que valide el feed
    /*$bodyText = preg_replace('=\(.*?)\=is', '', $entry['Exchange']['detail']);
    $bodyText = $this->Text->stripLinks($bodyText);
    $bodyText = Sanitize::stripAll($bodyText);
    $bodyText = $this->Text->truncate($bodyText, 400, '...', true, true);*/
    $bodyText = $entry['Exchange']['detail'];

    echo $this->Rss->item(array(), array(
        'title' => $this->Exchange->type($entry).': '.$entry['Exchange']['title'],
        'link' => $entryLink,
        'guid' => array('url' => $entryLink, 'isPermaLink' => 'true'),
        'description' => $bodyText,
        'dc:creator' => $entry['Exchange']['username'],
        'pubDate' => $entry['Exchange']['created']));
}