<?php

namespace App;

// Register Custom Post Type for Movie
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'Movies', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'Movie', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Movies', 'sage' ),
		'name_admin_bar'        => __( 'Movies', 'sage' ),
		'archives'              => __( 'Movie Archives', 'sage' ),
		'attributes'            => __( 'Movie Attributes', 'sage' ),
		'parent_item_colon'     => __( 'Parent Item:', 'sage' ),
		'all_items'             => __( 'All movies', 'sage' ),
		'add_new_item'          => __( 'Add New Movie', 'sage' ),
		'add_new'               => __( 'Add New', 'sage' ),
		'new_item'              => __( 'New Movie', 'sage' ),
		'edit_item'             => __( 'Edit Movie', 'sage' ),
		'update_item'           => __( 'Update Movie', 'sage' ),
		'view_item'             => __( 'View Movie', 'sage' ),
		'view_items'            => __( 'View Movies', 'sage' ),
		'search_items'          => __( 'Search Movie', 'sage' ),
		'not_found'             => __( 'Not found', 'sage' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'sage' ),
		'featured_image'        => __( 'Featured Image', 'sage' ),
		'set_featured_image'    => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image'    => __( 'Use as featured image', 'sage' ),
		'insert_into_item'      => __( 'Insert into movie', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'sage' ),
		'items_list'            => __( 'Movies list', 'sage' ),
		'items_list_navigation' => __( 'Movies list navigation', 'sage' ),
		'filter_items_list'     => __( 'Filter movies list', 'sage' ),
	);
	$args = array(
		'label'                 => __( 'Movie', 'sage' ),
		'description'           => __( 'Movies', 'sage' ),
		'labels'                => $labels,
		'supports'              => array( ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'movies', $args );

}
add_action( 'init', 'custom_post_type', 0 );