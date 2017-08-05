<?php

namespace App;

use GuzzleHttp\Client;

require_once( dirname(__DIR__) . '/../../../wp-admin/includes/media.php' );
require_once( dirname(__DIR__) . '/../../../wp-admin/includes/file.php' );
require_once( dirname(__DIR__) . '/../../../wp-admin/includes/media.php' );


add_action('movie_get_new_movies', function () {
    
    $client = new Client(['base_uri' => 'http://www.rcksld.com/']);
    $res = $client->get('GetNowShowing.json');
    $body = json_decode($res->getBody()); // object
    $movies = $body->Data;
    
    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'movies-' . date('Y-m-d') . '.json';

    \file_put_contents($path, json_encode($movies));

    \error_log('count: ' . count($movies));
    
    foreach($movies as $movie) {
        $post_id = \wp_insert_post(array(
            'post_title'    => $movie->Name,
            'post_content' => $movie->Synopsis,
            'post_status'   => 'publish',
            'post_type'   => 'movies'
        ));
        
        // Add Custom Fields
        \add_post_meta($post_id, 'Director', $movie->Director);
        \add_post_meta($post_id, 'MainCast', $movie->MainCast);
    
        \error_log('media_sideload_image: ' . (function_exists('media_sideload_image') ? 'true' : 'false'));    
        
        // Add Featured Image
        $media = \media_sideload_image($movie->PosterUrl, $post_id, $movie->Name, 'id');
        \error_log('media: ' . $media);    
    }
    
});


if( !wp_next_scheduled( 'movie_get_new_movies' ) ) {
	// Schedule the event
	wp_schedule_event( time(), 'hourly', 'movie_get_new_movies' );
}