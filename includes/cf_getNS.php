<?php

include('cloudflare.php');

foreach ( $sites as $site ) {
	$ns = $cf->getNS( $site['id'] );
  if ( !empty( $ns ) ) {
    echo json_encode( $ns );
  }
}

die();
