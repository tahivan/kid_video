<?php

	if ( ! isset( $content_width ) ) $content_width = 896;

	require_once (TEMPLATEPATH . '/core/core.php');

	$core = new cwsPrime();
	$core->init(array(
	    'name' => 'Happy Kids',
	    'slug' => 'happykids',
	    'version' => '1.1',
	));

?>