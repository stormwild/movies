<?php

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