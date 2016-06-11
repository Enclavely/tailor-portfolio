<?php

/**
 * Portfolio meta template.
 *
 * @package Tailor
 * @subpackage Templates
 * @since 1.0.0
 */

defined( 'ABSPATH' ) or die();

echo '<span class="entry__portfolio">' . get_the_term_list( get_the_ID(), 'portfolio', '', ', ' ) . '</span>';