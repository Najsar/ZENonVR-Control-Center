<?php
session_start();
header("charset=utf-8");
include "main.php";
if($_GET['p'] == "login") {
    if(login_by_session()['status'] == 1) {
        $user = login_by_session();
        $json['status'] = 2;
        $json['data'] = "<font color='green'>Użytkownik już zalogowany!</font>";
        $json['user'] = $user['data'];
        echo json_encode( $json ) ;
    }
    else if(isset($_POST['username']) && $_POST['username'] != null && isset($_POST['password']) && $_POST['password'] != null) {
        $user = login($_POST['username'], $_POST['password']);
        if(!$user['status']) {
            $json['status'] = 0;
            $json['data'] = "<font color='red'>Podano złe dane!</font>";
            echo json_encode( $json ) ;
        }
        else {
            $json['status'] = 1;
            $json['data'] = "<font color='green'>Zalogowano!</font>";
            echo json_encode( $json ) ;
        }
    }
    else {
        $json['status'] = 0;
        $json['data'] = "<font color='red'>Nie podano wszystkich danych!</font>";
        echo json_encode( $json ) ;
    }
}
/*else if($_GET['p'] == "gen_pass") {
    $pass_hashed = gen_pass($_POST['pass'], $_POST['salt']);
    $json['status'] = 1;
    $json['data'] = $pass_hashed;
    echo json_encode( $json ) ;
}
else if($_GET['p'] == "check_user") {
    $data = login($_POST['user'], $_POST['pass']);
    echo json_encode( $data ) ;
}*/
else if(login_by_session()['status'] != 1) {
    $json['status'] = 0;
    $json['err_code'] = 1;
    $json['err_message'] = 'User not login';
    echo json_encode( $json );
}
else {
    switch($_GET['p']) {
        case "get_user_name":
            if(login_by_session()['status'] != 1) {
                $json['status'] = 0;
                $json['err_code'] = 1;
                $json['err_message'] = 'User not login';
                echo json_encode( $json );
            }
            else {
                $user = login_by_session();
                $json['status'] = 1;
                $json['data'] = $user['data'];
                echo json_encode( $json );
            }
        break;
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
            if($data[9] == 1) {
                $query = sql_query("INSERT INTO sessions VALUES('','Przychód', '".$data[1]."', '".$data[3]."', '".$data[5]."', '".$data[2]."', '".$data[6]."', '".$data[7]."','".$data[8]."', '".$date."')");
            }
            else {
                for($i=0; $i<$data[9];$i++) {
                    $query = sql_query("INSERT INTO sessions VALUES('','Przychód', '".$data[1]."', '".$data[3]."', '".$data[5]."', '".$data[2]."', '".($data[6]/$data[9])."', '".($data[7]/$data[9])."','".($data[8]/$data[9])."', '".$date."')");
                }
            }
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
            $json['data'] = $data;
            echo json_encode( $json ) ;
        break;
        case "day_profit":
            $query = sql_query("SELECT SUM(price) AS suma FROM sessions WHERE sort = 'Przychód' AND DATE(time) = DATE(NOW()) ORDER BY id DESC");
            $data = $query['data']->fetch_all();
            $json['data'] = $data;
            echo json_encode( $json ) ;
        break;
        case "month_profit":
            $query = sql_query("SELECT SUM(price) AS suma FROM sessions WHERE sort = 'Przychód' AND MONTH(time) = MONTH(NOW()) ORDER BY id DESC");
            $data = $query['data']->fetch_all();
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
            $json['data'] = $data;
            echo json_encode( $json ) ;
        break;
        case "change_date":
            $day = $_GET['day'];
            $sum = get_sum($day);
            $sum['exchange'] = round( ($sum['start_cash']+$sum['cash']-$sum['expense'])-$end_bal, 2);
            $sum['bonus'] = round(($sum['profit']-$sum['partners'])*0.1, 2);
            $sum['end_bal'] = 0;
            echo json_encode( $sum ) ;
        break;
        case "product_list":
            $query = sql_query("SELECT product_list.id, product_type.type, product_list.name, product_list.price, product_list.countable, product_list.amount FROM product_list, product_type WHERE product_list.type = product_type.id ORDER BY product_list.id DESC");
            $data = $query['data']->fetch_all();
            for( $i=0 ; $i < sizeof($data) ; $i++ ) {
                if($data[$i][4] == 0) $data[$i][4] = 'n/a';
                else $data[$i][4] = $data[$i][5];
                $data[$i][5] = "<a href='#' class='btn btn-info btn-icon-split'><span class='icon text-white-50'><i class='fas fa-edit'></i></span><span class='text'>Edytuj</span></a>";
            }
            $json['data'] = $data;
            echo json_encode( $json ) ;
        break;
        case "gen_stats":
            if(isset($_GET['date'])) {
                $date = $_GET['date'];
            }
            else {
                $date = date('Y:m:d');
            }
            echo get_stats($date);
        break;
    }
}
?>