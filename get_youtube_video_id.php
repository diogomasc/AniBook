<?php
function get_youtube_video_id($url) {
    $parts = parse_url($url);
    if ($parts['host'] == 'youtu.be') {
        return ltrim($parts['path'],'/');
    } else if (strpos($parts['path'], 'embed') === 1) {
        return str_replace('/embed/', '', $parts['path']);
    } else {
        parse_str($parts['query'], $query);
        return $query['v'];
    }
}
?>
