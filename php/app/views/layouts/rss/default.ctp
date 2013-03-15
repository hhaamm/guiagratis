<?php
    echo $rss->header();
    
    if (!isset($documentData)) {
        $documentData = array();
    }
    
    if (!isset($channelData)) {
        $channelData = array();
    }
    
    if (!isset($channelData['title'])) {
        $channelData['title'] = $title_for_layout;
    } 
    
    $channel = $rss->channel(array(), $channelData, $content_for_layout);
    
    echo $rss->document($documentData,$channel);