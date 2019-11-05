<?php
    function sql_main($query_text) {
        $conn = new mysqli('localhost', 'zen_cmd', 'Qwerty1@3', 'zen_cmd');
        $conn->query("SET NAMES 'utf8'");
            if(!$conn->query($query_text) ) {
                $status = '0';
                $return['status'] = $status;
                $return['data'] = "Error while procesing you request";
                return  $return;
            }
            else {
                $query = $conn->query($query_text);
                $query_fetch = $query->fetch_array();
                if( !$query_fetch ) {
                    $return['status'] = '0';
                    $return['data'] = "No data has been find";
                    return  $return;
                }
                else {
                    $return['status'] = '1';
                    $return['data'] = $query_fetch;
                    return  $return;
                }
            }
            $conn->close();
    }
    function sql_query($query_text) {
        $conn = new mysqli('localhost', 'zen_cmd', 'Qwerty1@3', 'zen_cmd');
        $conn->query("SET NAMES 'utf8'");
        $query = $conn->query($query_text);
            if( !$query ) {
                $return['status'] = '0';
                $return['data'] = "Error while procesing you request";
                return  $return;
            }
            else {
                $return['status'] = '1';
                $return['data'] = $query;
                return  $return;
                
            }
            $conn->close();
    }
    function gen_pass($pass, $salt) {
        $pass_hashed = hash('sha512', $pass);
        $pass_hashed = hash('MD5', $pass_hashed.$salt);
        $pass_hashed = hash('sha256', $pass_hashed);
        $pass_hashed = hash('sha512', $pass_hashed.$salt);
        $pass_hashed = hash('MD5', $pass_hashed);
        $pass_hashed = hash('sha256', $salt.$pass_hashed);
        $pass_hashed = hash('SHA512', $pass_hashed);
        return $pass_hashed;
    }
    function gen($method, $size) {
        switch($method) {
            case 1:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
            case 2:
                $characters = '0123456789';
            break;
            case 3:
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
            case 4:
                $characters = 'abcdefghijklmnopqrstuvwxyz';
            break;
            case 5:
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
            case 6:
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
            default:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        }
        $randstring = '';
        for ($i = 0; $i < $size; $i++) {
            $randstring .= $characters[ rand( 0, strlen($characters)-1 ) ];
        }
        return $randstring;
    }
    function login($user, $pass) {
        $user = htmlspecialchars($user);
        $pass = htmlspecialchars($pass);
        if($user == '' || $user == null || $pass == '' || $pass = null) {
            $return['status'] = 0;
            $return['data'] = "No data";
            return  $return;
        }
        else if(isset($_SESSION['user_session'])) {
            $return['status'] = 0;
            $return['data'] = "User already logged";
            return  $return;
        }
        else {
            $salt = sql_main("SELECT Salt FROM users WHERE Name = '".$user."'");
            if($salt['status'] == 0) {
                $return['status'] = 0;
                $return['data'] = "User not exist";
                return  $return;
            }
            else {
                $pass_hashed = gen_pass($pass, $salt['data']['Salt']);
                $user = sql_main("SELECT * FROM users WHERE Name = '".$user."' AND Pass = '".$pass_hashed."'");
                if($user['status'] == 0) {
                    $return['status'] = 0;
                    $return['data'] = "Pass not exist";
                    return  $return;
                }
                else {
                    $session = gen(1, 16);
                    $date = date('Y:m:d H:i:s');
                    sql_query("INSERT INTO login_session VALUES ('', '".$user['data']['ID']."', '".$session."', '".$date."')");
                    $return['status'] = 1;
                    $return['data'] = $user['data'];
                    $return['session'] = $session;
                    $_SESSION['user_session'] = $session;
                    return  $return;
                }
            }
        }
    }
    function login_by_session() {
        if(!isset($_SESSION['user_session'])) {
            $return['status'] = 0;
            $return['data'] = "User not login!";
            return  $return;
        }
        else {
            $session_query = sql_main("SELECT * FROM login_session WHERE session = '".$_SESSION['user_session']."'");
            if(!$session_query['status']) {
                $return['status'] = 0;
                $return['data'] = "User not found!";
                return  $return;
            }
            else {
                $user = sql_main("SELECT `ID`,`Name`,`isAdmin` FROM users WHERE ID = '".$session_query['data']['user_id']."'");
                $date = date('Y:m:d H:i:s', strtotime(date('Y:m:d H:i:s') . ' +1 day'));
                if(!$user['status']) {
                    $return['status'] = 0;
                    $return['data'] = "User not found!";
                    return  $return;
                }
                else if(strtotime($date)  < strtotime($session_query['data']['date']) ) {
                    $return['status'] = 0;
                    $return['data'] = "Session expired!";
                    return  $return;
                }
                else {
                    $return['status'] = 1;
                    $return['data'] = $user['data'];
                    return  $return;
                }
            }
        }
    }
    function get_stats($date) {
        $query = sql_query("SELECT DATE(time), type, count(id), sum(price), category FROM sessions WHERE sort = 'Przychód' AND MONTH(time) = MONTH('".$date."') GROUP BY type, DATE(time) ORDER BY category");
        $data = $query['data']->fetch_all();
    
        $days = [];
        $thead_array = [];
        $thead_array_cat = [];
        $tbody_array = [];
        $sum = [];
        for($i=0; $i<sizeof($data); $i++) {
            if(!in_array($data[$i][0], $days)) {
                $days[] = $data[$i][0];
            }
            if(!in_array($data[$i][1], $thead_array)) {
                $thead_array[] = $data[$i][1];
                $thead_array_cat[] = $data[$i][4];
            }
            $find = array_search($data[$i][0], $days);
            $find2 = array_search($data[$i][1], $thead_array);
            $tbody_array[$find][$find2*2] = $data[$i][2];
            $tbody_array[$find][($find2*2)+1] = $data[$i][3];
        }
        for($e=0; $e<sizeof($days); $e++) {
            for($j=0; $j<sizeof($thead_array)*2; $j++) {
                if(!isset($tbody_array[$e][$j])) {
                    $tbody_array[$e][$j] = 0;
                }
                if( !isset($sum[$j]) ) {
                    $sum[$j] = $tbody_array[$e][$j];
                }
                else {
                    $sum[$j] += $tbody_array[$e][$j];
                }
                
            }
        }
        $sum_szt = [];
        $sum_zl = [];
        for($i=0; $i<sizeof($tbody_array); $i++) {
            for($e=0; $e<sizeof($tbody_array[$i]); $e++) {
                if ($e % 2 == 0) {
                    if(!isset($sum_szt[$i])){
                        $sum_szt[$i] = $tbody_array[$i][$e];
                    }
                    else {
                        $sum_szt[$i] = $sum_szt[$i] + $tbody_array[$i][$e];
                    }
                }
                else {
                    if(!isset($sum_zl[$i])){
                        $sum_zl[$i] = $tbody_array[$i][$e];
                    }
                    else {
                        $sum_zl[$i] = $sum_zl[$i] + $tbody_array[$i][$e];
                    }
                }
            }   
        }
    
        $thead = "<thead><tr><th>Data</th>";
        for($e=0; $e<sizeof($thead_array); $e++) {
            $thead .= "<th>$thead_array_cat[$e] <br> $thead_array[$e] ( szt )</th>";
            $thead .= "<th>$thead_array_cat[$e] <br> $thead_array[$e] ( zł )</th>";
        }
        $thead .= "<th>SUMA ( szt )</th>";
        $thead .= "<th>SUMA ( zł )</th>";
        $thead .= "</tr></thead>";
    
        $tbody = "<tbody>";
        for($e=0; $e<sizeof($days); $e++) {
            $tbody .= "<tr><td>$days[$e]</td>";
            for($j=0; $j<sizeof($thead_array)*2; $j++) {
                $tbody .= "<td>".$tbody_array[$e][$j]."</td>";
                
            }
            $tbody .= "<td><b>$sum_szt[$e]</b></td>";
            $tbody .= "<td><b>$sum_zl[$e]</b></td>";
            $tbody .= "</tr>";
        }
        $tbody .= "</tbody>";
    
        $tfoot = "<tfoot><tr><th>Suma:</th>";
        for($e=0; $e<sizeof($sum); $e++) {
            if ($e % 2 == 0) {
                $tfoot .= "<th>".$sum[$e]." szt</th>";
            }
            else {
                $tfoot .= "<th>".$sum[$e]." zł</th>";
            }
        }
        $tfoot .= "<th>".array_sum($sum_szt)."</th>";
        $tfoot .= "<th>".array_sum($sum_zl)."</th>";
        $tfoot .= "</tr></tfoot>";
    
        return $thead.$tbody.$tfoot;
    }
?>