<?php

namespace App;

use GuzzleHttp\Client;

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
        
        // Add Featured Image
        $media = \media_sideload_image($movie->PosterUrl, $post_id);
        
        if(!empty($media) && !\is_wp_error($media)){
            $args = array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_status' => 'any',
                'post_parent' => $post_id
            );
            
            // reference new image to set as featured
            $attachments = \get_posts($args);
        
            if(isset($attachments) && is_array($attachments)){
                foreach($attachments as $attachment){
                    // grab source of full size images (so no 300x150 nonsense in path)
                    $image = \wp_get_attachment_image_src($attachment->ID, 'full');
                    // determine if in the $media image we created, the string of the URL exists
                    if(strpos($media, $image[0]) !== false){
                        // if so, we found our image. set it as thumbnail
                        \set_post_thumbnail($post_id, $attachment->ID);
                        // only want one image
                        break;
                    }
                }
            }
        }
    }
    
});


if( !wp_next_scheduled( 'movie_get_new_movies' ) ) {
	// Schedule the event
	wp_schedule_event( time(), 'hourly', 'movie_get_new_movies' );
}