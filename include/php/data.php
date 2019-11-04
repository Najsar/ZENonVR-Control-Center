<?php
header("charset=utf-8");
include "main.php";
switch($_GET['p']) {
    case "stats_table":
        $query = sql_query("SELECT DATE(time), type, count(id), sum(price) FROM sessions WHERE sort = 'Przychód' GROUP BY type, DATE(time)");
        $data = $query['data']->fetch_all();

        $json['data'] = $data;

        //print_r( $data_new );
        echo json_encode( $json ) ;
    break;
}
?>