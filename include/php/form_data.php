<?php
header("charset=utf-8");
include "main.php";

function get_sum($day) {
    $query = sql_query("SELECT sort,category,type,payment,price,paid_price,exchange FROM sessions WHERE DAY(time) = DAY('".$day."') ORDER BY id DESC");
    $data = $query['data']->fetch_all();
    $start_cash = sql_main("SELECT end_balance FROM reports ORDER BY id DESC LIMIT 1");
    $sum = [];
    if(!$start_cash['status']) {
        $sum['start_cash'] = 0;
    }
    else {
        $sum['start_cash'] = $start_cash['data'][0];
    }
    $sum['cash'] = 0; 
    $sum['card'] = 0;
    $sum['expense'] = 0;
    $sum['pcstore'] = 0;
    $sum['grupon'] = 0;
    $sum['s_prezenty'] = 0;
    $sum['profit'] = 0;
    $sum['partners'] = 0;
    for( $i=0 ; $i < sizeof($data) ; $i++) {
        if($data[$i][3] == "Gotówka" && $data[$i][0] == "Przychód") $sum['cash'] += $data[$i][4];
        if($data[$i][1] == "Grupon") $sum['grupon'] += $data[$i][4];
        if($data[$i][3] == "Karta") $sum['card'] += $data[$i][4];
        if($data[$i][1] == "PC Store") $sum['pcstore'] += $data[$i][4];
        if($data[$i][1] == "Super Prezenty") $sum['s_prezenty'] += $data[$i][4];
        if($data[$i][0] == "Rozchód") $sum['expense'] += (0-$data[$i][4]);
        if($data[$i][0] == "Przychód") $sum['profit'] += $data[$i][4];
        if($data[$i][3] == "Sieć Partnerska") $sum['partners'] += $data[$i][4];
    }
    $sum['cash'] = round($sum['cash'], 2); 
    $sum['card'] = round($sum['card'], 2);
    $sum['expense'] = round($sum['expense'], 2);
    $sum['pcstore'] = round($sum['pcstore'], 2);
    $sum['grupon'] = round($sum['grupon'], 2);
    $sum['s_prezenty'] = round($sum['s_prezenty'], 2);
    $sum['profit'] = round($sum['profit'], 2);
    $sum['partners'] = round($sum['partners'], 2);
    $sum['partners_card'] = $sum['partners']+$sum['card'];
    return $sum;
}

switch($_GET['p']) {
    case "sell_box_form":
        $query = sql_query("SELECT product_type.id, product_type.type, product_list.name,product_list.price,product_type.cash,product_type.use_in_bonus,product_type.fixed_price FROM product_list, product_type WHERE product_list.type=product_type.id ORDER BY product_list.type,product_list.id");
        echo json_encode( $query['data']->fetch_all() );
    break;
    case "payment_method":
        $query = sql_query("SELECT * FROM payment_method");
        $data = $query['data']->fetch_all();
        echo json_encode( $data ) ;
    break;
    case "expense_box_form":
        $query = sql_query("SELECT * FROM expense_type");
        $data = $query['data']->fetch_all();
        echo json_encode( $data ) ;
    break;
    case "new_payment":
        $data = $_POST['data'];
        $date = date('Y:m:d H:i:s');
        $query = sql_query("INSERT INTO sessions VALUES('','Przychód', '".$data[1]."', '".$data[3]."', '".$data[5]."', '".$data[2]."', '".$data[6]."', '".$data[7]."','".$data[8]."', '".$date."')");
        if(!$query['status']) {
            $json['status'] = "0";
            $json['data'] = "Data not saved";
            echo json_encode($json);
        }
        else {
            $json['status'] = "1";
            $json['data'] = "Data saved";
            echo json_encode($json);
        }
    break;
    case "new_expense":
        $data = $_POST['data'];
        $date = date('Y:m:d H:i:s');
        $query = sql_query("INSERT INTO sessions VALUES('','Rozchód', '".$data[0]."', '".$data[1]."', 'Gotówka', '-".$data[2]."', '-".$data[2]."', '-".$data[2]."','0', '".$date."')");
        if(!$query['status']) {
            $json['status'] = "0";
            $json['data'] = "Data not saved";
            echo json_encode($json);
        }
        else {
            $json['status'] = "1";
            $json['data'] = "Data saved";
            echo json_encode($json);
        }
    break;
    case "day_payment":
        if(!isset($_GET['date'])) {
            $date = date('Y:m:d');
        }
        else {
            $date = $_GET['date'];
        }
        $query = sql_query("SELECT id, category, type, payment, main_price, price, paid_price, exchange, TIME(time) as time FROM sessions WHERE DATE(time) = DATE('".$date."') ORDER BY id DESC");
        $data = $query['data']->fetch_all();
        //print_r( $data );
        $json['data'] = $data;
        echo json_encode( $json ) ;
    break;
    case "day_profit":
        $query = sql_query("SELECT SUM(price) AS suma FROM sessions WHERE sort = 'Przychód' AND DATE(time) = DATE(NOW()) ORDER BY id DESC");
        $data = $query['data']->fetch_all();
        //print_r( $data );
        $json['data'] = $data;
        echo json_encode( $json ) ;
    break;
    case "month_profit":
        $query = sql_query("SELECT SUM(price) AS suma FROM sessions WHERE sort = 'Przychód' AND MONTH(time) = MONTH(NOW()) ORDER BY id DESC");
        $data = $query['data']->fetch_all();
        //print_r( $data );
        $json['data'] = $data;
        echo json_encode( $json ) ;
    break;
    case "day_report":
        $day = date('Y:m:d H:i:s');
        $sum = get_sum($day);
        
        echo json_encode( $sum ) ;
    break;
    case "new_day_report":
        $end_bal = $_POST['data'];
        //$end_bal = 1049;
        $sum = get_sum(date('Y:m:d H:i:s'));
        $sum['exchange'] = round( ($sum['start_cash']+$sum['cash']-$sum['expense'])-$end_bal, 2);
        $sum['bonus'] = round(($sum['profit']-$sum['partners'])*0.1, 2);
        $sum['end_bal'] = $end_bal;
        $sum['date'] = date('Y:m:d H:i:s');

        $query = sql_query("INSERT INTO reports VALUES('','".$sum['start_cash']."', '".$sum['cash']."', '".$sum['card']."', '".$sum['expense']."', '".$sum['pcstore']."', '".$sum['grupon']."', '".$sum['s_prezenty']."','".$sum['profit']."', '".$sum['partners']."', '".$sum['exchange']."', '".$sum['bonus']."', '".$sum['end_bal']."', '".$sum['date']."')");
        if(!$query['status']) {
            $json['status'] = "0";
            $json['data'] = "Data not saved";
            echo json_encode($json);
        }
        else {
            $json['status'] = "1";
            $json['data'] = "Data saved";
            echo json_encode($json);
        }
    break;
    case "day_reports":
        if(!isset($_GET['date'])) {
            $date = date('Y:m:d');
        }
        else {
            $date = $_GET['date'];
        }
        $query = sql_query("SELECT DATE(date), start_cash, cash, card, expense, pcstore, grupon, s_prezenty, partners, exchange, end_balance, profit, bonus FROM reports WHERE MONTH(date) = MONTH('".$date."') ORDER BY id DESC");
        $data = $query['data']->fetch_all();
        //print_r( $data );
        $json['data'] = $data;
        echo json_encode( $json ) ;
    break;
    case "change-date":
        $day = $_GET['day'];
        $sum = get_sum($day);
        $sum['exchange'] = round( ($sum['start_cash']+$sum['cash']-$sum['expense'])-$end_bal, 2);
        $sum['bonus'] = round(($sum['profit']-$sum['partners'])*0.1, 2);
        $sum['end_bal'] = 0;
        echo json_encode( $sum ) ;
    break;
}
?>