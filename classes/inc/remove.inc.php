<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_courseanalytics\lib;
$lib = new lib();

$returnText = new stdClass();
$array = [];
$error = '';
$p = 'local_courseanalytics';
$type = '';
$context = context_system::instance();
if(!has_capability('local/courseanalytics:courseanalytics', $context)){
    $error = get_string('you_dhrc', $p);
} else {
    require_capability('local/courseanalytics:courseanalytics', $context);
    if(!isset($_POST['type'])){
        $error = get_string('no_tp', $p);
    } else {
        $type = $_POST['type'];
        if(!in_array($type, ['all', 'select']) || empty($type)){
            $error = get_string('invalid_tp', $p);
        } else {
            if($type != 'all'){
                if(!isset($_POST['total'])){
                    $error = get_string('no_top', $p);
                } else {
                    $numMatch = "/^[0-9]*$/";
                    $total = $_POST['total'];
                    if(empty($total)){
                        $error = get_string('please_sao', $p);
                    } elseif(!preg_match($numMatch, $total)){
                        $error = get_string('invalid_top', $p);
                    } else {
                        for($i = 0; $i < $total; $i++){
                            if(!isset($_POST["c$i"])){
                                $error .= get_string('missing_rv', $p);
                            } else {
                                $val = $_POST["c$i"];
                                if(!preg_match($numMatch, $val) || empty($val)){
                                    $error .= get_string('invalid_vp', $p);
                                } else {
                                    array_push($array, $val);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

if($error != ''){
    $returnText->error = $error;
} else {
    if($type === 'all'){
        $returnText->return = $lib->delete_all_tracked_courses();
        \local_courseanalytics\event\deleted_tracked_course_records::create(array('context' => \context_system::instance()))->trigger();
    } elseif($type === 'select'){
        $returnText->return = $lib->delete_tracked_courses($array);
        \local_courseanalytics\event\deleted_tracked_course_records::create(array('context' => \context_system::instance()))->trigger();
    }
}
echo(json_encode($returnText));