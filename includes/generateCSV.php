<?php

$cols = !empty( $_POST['cols'] ) && is_array( $_POST['cols']) ? $_POST['cols'] : [];
$data = !empty( $_POST['data'] ) && is_array( $_POST['data']) ? $_POST['data'] : [];

$csv = implode( ',', $cols ) . "\n";
foreach ( $data as $row ) {
  $csv .= implode( ',', $row ) . "\n";
}

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="nsrecords.csv"');

echo $csv;