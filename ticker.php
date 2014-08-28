<?php

require_once( "config.php" );

$ajax = isset( $_GET['ajax'] ) ? 1 : 0;

new Ticker( $ajax );

