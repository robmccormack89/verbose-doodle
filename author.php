<?php
/**
* The template for displaying author archive pages
*
* @package Verbose_Doodle
*/

global $wp_query;

$context = Timber::context();
$context['posts'] = new Timber\PostQuery();

if ( isset( $wp_query->query_vars['author'] ) ) {
  $author = new Timber\User( $wp_query->query_vars['author'] );
  $context['author'] = $author;
  $context['title']  = esc_html__('Author Archives:', 'verbose-doodle').' '. $author->name();
}

Timber::render( array( 'author.twig', 'archive.twig' ), $context );