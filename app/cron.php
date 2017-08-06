<?php

namespace App;

use GuzzleHttp\Client;

require_once( dirname(__DIR__) . '/../../../wp-admin/includes/media.php' );
require_once( dirname(__DIR__) . '/../../../wp-admin/includes/file.php' );
require_once( dirname(__DIR__) . '/../../../wp-admin/includes/image.php' );


add_action('movie_get_new_movies', function () {
    
    $client = new Client(['base_uri' => 'http://www.rcksld.com/']);
    $res = $client->get('GetNowShowing.json');
    
    $movies = (json_decode($res->getBody()))->Data;
    
    // save json data to data directory
    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'movies-' . date('Y-m-d') . '.json';
    \file_put_contents($path, json_encode($movies));
    
    foreach($movies as $movie) {
        // check if movie with title is equal movie->name
        $exists = get_page_by_title($movie->Name, OBJECT, 'movies');
        
        if(is_null($exists)) {
            $post_id = \wp_insert_post(array(
                'post_title'    => $movie->Name,
                'post_content' => $movie->Synopsis,
                'post_status'   => 'publish',
                'post_type'   => 'movies'
            ));    
            
            // Add Custom Fields
            \add_post_meta($post_id, 'Director', $movie->Director);
            \add_post_meta($post_id, 'MainCast', $movie->MainCast);
        
            // Add Featured Image
            $media = \media_sideload_image($movie->PosterUrl, $post_id, $movie->Name, 'id');
            set_post_thumbnail($post_id, $media);
        }
        
    }
    
});


if( !wp_next_scheduled( 'movie_get_new_movies' ) ) {
	// Schedule the event
	wp_schedule_event( time(), 'hourly', 'movie_get_new_movies' );
}

