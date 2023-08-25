<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_courseanalytics\lib;
$lib = new lib();

$returnText = new stdClass();
$error = '';
$p = 'local_courseanalytics';

if(!isset($_POST['total']) || !isset($_SESSION['ca_search'])){
    $returnText->error = get_string('missing_rv', $p);
} else {
    $total = $_POST['total'];
    if(!preg_match("/^[0-9]*$/", $total) || empty($total)){
        $returnText->error = get_string('invalid_tp', $p);
    } else {
        $array = [];
        for($i = 0; $i < $total; $i++){
            if(!isset($_POST["c$i"]) || !isset($_POST["i$i"])){
                $returnText->error = get_string('missing_rv', $p);
                echo(json_encode($returnText));
                exit();
            } else {
                $c = $_POST["c$i"];
                $id = $_POST["i$i"];
                if(!preg_match("/^[a-zA-Z \-()]*$/", $c) || empty($c)){
                    $returnText->error = [$id, "company for record ".$lib->get_username($id)." contains invalid values: ".preg_replace('/[a-zA-Z \-()]/','',$c)];
                    echo(json_encode($returnText));
                    exit();
                }
                if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
                    $returnText->error = get_string('invalid_vp', $p);
                    echo(json_encode($returnText));
                    exit();
                }
                array_push($array, [$c, $id]);
            }
        }
        if($array !== []){
            $returnText->return = ($lib->update_users_company($array)) ? true : false;
        }
    }
}

echo(json_encode($returnText));