<?php
/**
 * Plugin Name: FAQs Shortcode
 * Plugin URI: https://wordpress.org/plugins/faqs-shortcode/
 * Description: A real no brainer for setting up FAQs.
 * Version: 0.1
 * Author: Yusri Mathews
 * Author URI: http://yusrimathews.co.za/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * FAQs Shortcode Plugin
 * Copyright (C) 2015, Yusri Mathews - yo@yusrimathews.co.za
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

add_action( 'init', 'faqss_cpt' );
function faqss_cpt() {
	$labels = array(
		'name'               => _x( 'FAQs', '', 'faqss' ),
		'singular_name'      => _x( 'FAQ', '', 'faqss' ),
		'menu_name'          => _x( 'FAQs', '', 'faqss' ),
		'name_admin_bar'     => _x( 'FAQ', '', 'faqss' ),
		'add_new'            => _x( 'Add New', '', 'faqss' ),
		'add_new_item'       => __( 'Add New FAQ', 'faqss' ),
		'new_item'           => __( 'New FAQ', 'faqss' ),
		'edit_item'          => __( 'Edit FAQ', 'faqss' ),
		'view_item'          => __( 'View FAQ', 'faqss' ),
		'all_items'          => __( 'All FAQs', 'faqss' ),
		'search_items'       => __( 'Search FAQs', 'faqss' ),
		'parent_item_colon'  => __( 'Parent FAQs:', 'faqss' ),
		'not_found'          => __( 'No FAQs found.', 'faqss' ),
		'not_found_in_trash' => __( 'No FAQs found in Trash.', 'faqss' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'supports'           => array( 'title', 'editor' )
	);

	register_post_type( 'faqss', $args );
}

add_action( 'init', 'faqss_taxonomy' );
function faqss_taxonomy() {
	$labels = array(
		'name'              => _x( 'Categories', '' ),
		'singular_name'     => _x( 'Category', '' ),
		'search_items'      => __( 'Search Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'Categories' ),
	);

	$args = array(
		'hierarchical'     	 	=> true,
		'labels'            	=> $labels,
		'show_ui'           	=> true,
		'show_admin_column'	 	=> true,
		'query_var'         	=> false,
		'public'				=> false,
		'publicly_queryable'	=> false
	);

	register_taxonomy( 'faqss_cat', array( 'faqss' ), $args );
}

add_action( 'media_buttons', 'faqss_cbtn', 99 );
function faqss_cbtn(){
	if( get_post_type( get_the_ID() ) == 'page' ){
		echo '<a href="#" id="faqss_cbtn" class="button"><i class="fa fa-list"></i> Add FAQs</a>';
	}
}

add_action( 'admin_enqueue_scripts', 'faqss_scripts_admin' );
function faqss_scripts_admin(){
	if( get_post_type( get_the_ID() ) == 'page' ){
		wp_enqueue_style( 'faqss-font-awesome', plugin_dir_url( __FILE__ ) . 'vendor/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'faqss-admin-css', plugin_dir_url( __FILE__ ) . 'css/admin.min.css', array( 'faqss-font-awesome' ) );

		wp_enqueue_script( 'faqss-admin-js', plugin_dir_url( __FILE__ ) . 'js/admin.min.js', array( 'jquery' ) );
	}
}

add_shortcode( 'faqss', 'faqss_shortcode' );
function faqss_shortcode( $atts ){

	$page_object = get_post( get_the_ID() ); 
	$page_content = $page_object->post_content;

	if( is_page( get_the_ID() ) && has_shortcode( $page_content, 'faqss' ) ){

		$layoutStyle = $atts['layoutstyle'];
		$hideBanner = isset( $atts['hidebanner'] );

		$emailIcon = isset( $atts['emailicon'] );
		$emailText = isset( $atts['emailtext'] );
		$emailLink = isset( $atts['emaillink'] );

		$telIcon = isset( $atts['telicon'] );
		$telText = isset( $atts['teltext'] );
		$telLink = isset( $atts['tellink'] );

		$docIcon = isset( $atts['docicon'] );
		$docText = isset( $atts['doctext'] );
		$docLink = isset( $atts['doclink'] );

		$forumIcon = isset( $atts['forumicon'] );
		$forumText = isset( $atts['forumtext'] );
		$forumLink = isset( $atts['forumlink'] );

		$bannerLinks = array_filter( array(
			$emailText,
			$telText,
			$docText,
			$forumText
		) );

		$linksCount = count( $bannerLinks );
		if( $linksCount > 0 ){
			$linksWidth = 100 / $linksCount;
		} else {
			$linksWidth = 100;
		}

		$shortcodeOutput = '<div id="faqss-' . $layoutStyle . '">';

			if( $layoutStyle == 'modern' && $hideBanner == 'false' && $linksCount > 0 ){
				$shortcodeOutput .= '<div id="faqss-banner">';
					if( !empty( $emailText ) ){
						$shortcodeOutput .= '<div id="faqss-email" class="faqss-link" style="width: ' . $linksWidth . '%">';
							$shortcodeOutput .= '<i class="fa fa-' . $emailIcon . '"></i>';
							$shortcodeOutput .= '<a href="' . $emailLink . '">' . $emailText . '</a>';
						$shortcodeOutput .= '</div>';
					}

					if( !empty( $telText ) ){
						$shortcodeOutput .= '<div id="faqss-tel" class="faqss-link" style="width: ' . $linksWidth . '%">';
							$shortcodeOutput .= '<i class="fa fa-' . $telIcon . '"></i>';
							$shortcodeOutput .= '<a href="' . $telLink . '">' . $telText . '</a>';
						$shortcodeOutput .= '</div>';
					}

					if( !empty( $docText ) ){
						$shortcodeOutput .= '<div id="faqss-doc" class="faqss-link" style="width: ' . $linksWidth . '%">';
							$shortcodeOutput .= '<i class="fa fa-' . $docIcon . '"></i>';
							$shortcodeOutput .= '<a href="' . $docLink . '">' . $docText . '</a>';
						$shortcodeOutput .= '</div>';
					}

					if( !empty( $forumText ) ){
						$shortcodeOutput .= '<div id="faqss-forum" class="faqss-link" style="width: ' . $linksWidth . '%">';
							$shortcodeOutput .= '<i class="fa fa-' . $forumIcon . '"></i>';
							$shortcodeOutput .= '<a href="' . $forumLink . '">' . $forumText . '</a>';
						$shortcodeOutput .= '</div>';
					}
				$shortcodeOutput .= '</div>';
			}

			if( $layoutStyle == 'simple' ){

				$faqssCounter = 0;

				query_posts( array(
					'post_type' => 'faqss',
					'order'		=> 'ASC',
					'showposts' => -1
				) );

				while( have_posts() ) : the_post();
					$faqssCounter++;
					$shortcodeOutput .= '<div class="faqss-item' . ( $faqssCounter % 2 == 0 ? '' : ' faqss-odd' ) . '">';
						$shortcodeOutput .= '<h6>' . get_the_title() . '</h6>';

						$page_content = apply_filters( 'the_content', get_the_content() );
						$page_content = str_replace( ']]>', ']]&gt;', $page_content );

						$shortcodeOutput .= $page_content;
					$shortcodeOutput .= '</div>';
				endwhile;

			} elseif( $layoutStyle == 'modern' ){
				$faqss_terms = get_terms( 'faqss_cat', array(
					'orderby' => 'id',
					'parent' => 0
				) );

				$shortcodeOutput .= '<div class="faqss-aside">';
					if( $faqss_terms ){
						$faqssTermsCounter = 0;
						$shortcodeOutput .= '<ul>';
						foreach( $faqss_terms as $k => $v ){
							$shortcodeOutput .= '<li><a href="#' . $v->slug . '"' . ( $faqssTermsCounter == 0 ? ' class="active"' : '' ) . '>' . $v->name . '</a></li>';
							$faqssTermsCounter++;
						}
						$shortcodeOutput .= '</ul>';
					}
				$shortcodeOutput .= '</div>';

				if( $faqss_terms ){
					$faqssTermsCounter = 0;
					foreach( $faqss_terms as $k => $v ){
						$shortcodeOutput .= '<div id="' . $v->slug . '" class="faqss-section"' . ( $faqssTermsCounter == 0 ? ' style="display: block;"' : ' style="display: none;"' ) . '>';
							if( !empty( $v->description ) ){
								$shortcodeOutput .= '<p>' . $v->description . '</p>';
							}

							$faqssCounter = 0;

							query_posts( array(
								'post_type' => 'faqss',
								'tax_query' => array(
									array(
										'taxonomy' => 'faqss_cat',
										'field'    => 'slug',
										'terms'    => $v->slug,
									),
								),
								'order'		=> 'ASC',
								'showposts' => -1
							) );

							while( have_posts() ) : the_post();
								$faqssCounter++;
								$shortcodeOutput .= '<div class="faqss-item">';
									$shortcodeOutput .= '<h6><a href="#" class="faqss-acc">' . get_the_title() . '</a></h6>';

									$page_content = apply_filters( 'the_content', get_the_content() );
									$page_content = str_replace( ']]>', ']]&gt;', $page_content );

									$shortcodeOutput .= '<div class="faqss-content"' . ( $faqssCounter == 1 ? '' : ' style="display: none;"' ) . '>' . $page_content . '</div>';
								$shortcodeOutput .= '</div>';
							endwhile;

						$shortcodeOutput .= '</div>';
						$faqssTermsCounter++;
					}
				} else {
					$shortcodeOutput .= '<div class="faqss-section" style="width: 100%;">';

						$faqssCounter = 0;

						query_posts( array(
							'post_type' => 'faqss',
							'order'		=> 'ASC',
							'showposts' => -1
						) );

						while( have_posts() ) : the_post();
							$faqssCounter++;
							$shortcodeOutput .= '<div class="faqss-item">';
								$shortcodeOutput .= '<h6><a href="#" class="faqss-acc faqss-fullitems">' . get_the_title() . '</a></h6>';

								$page_content = apply_filters( 'the_content', get_the_content() );
								$page_content = str_replace( ']]>', ']]&gt;', $page_content );

								$shortcodeOutput .= '<div class="faqss-content"' . ( $faqssCounter == 1 ? '' : ' style="display: none;"' ) . '>' . $page_content . '</div>';
							$shortcodeOutput .= '</div>';
						endwhile;

					$shortcodeOutput .= '</div>';
				}

			} else {
				$shortcodeOutput .= '<p>Something is wrong with your FAQs Shortcode. Please use our built in shortcode generator to assist you.</p>';
			}

		$shortcodeOutput .= '</div>';

		return $shortcodeOutput;

	} else {
		return '<p>These FAQs shortcodes can only be used on pages.</p>';
	}
}

add_action( 'wp_enqueue_scripts', 'faqss_scripts' );
function faqss_scripts(){
	$page_object = get_post( get_the_ID() ); 
	$page_content = $page_object->post_content;

	if( is_page( get_the_ID() ) && has_shortcode( $page_content, 'faqss' ) ){
		wp_enqueue_style( 'faqss-font-awesome', plugin_dir_url( __FILE__ ) . 'vendor/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'faqss-public-css', plugin_dir_url( __FILE__ ) . 'css/public.min.css', array( 'faqss-font-awesome' ) );

		wp_enqueue_script( 'faqss-public-js', plugin_dir_url( __FILE__ ) . 'js/public.min.js', array( 'jquery' ) );
	}
}
