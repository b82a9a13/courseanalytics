<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_courseanalytics\lib;
$lib = new lib();

$returnText = new stdClass();
$array = [];
$p = 'local_courseanalytics';
if(!isset($_SESSION['ca_manage'])){
    $returnText->error = get_string('missing_rv', $p);
} elseif($_SESSION['ca_manage'] != true){
    $returnText->error = get_string('missing_rv', $p);
} elseif(!isset($_POST['type'])){
    $returnText->error = get_string('no_tp', $p);
} else{
    $type = $_POST['type'];
    if(!in_array($type, ['eh', 'nuh'])){
        $returnText->error = get_string('invalid_tp', $p);
    } else {
        if(!isset($_POST['sd']) || !isset($_POST['ed'])){
            $returnText->error = get_string('missing_rv', $p);
        } else {
            $sd = $_POST['sd'];
            $ed = $_POST['ed'];
            if(!preg_match("/^[0-9\-]*$/", $sd) || empty($sd)){
                $returnText->error = get_string('invalid_sdp', $p);
            } else if(!preg_match("/^[0-9\-]*$/", $ed) || empty($ed)){
                $returnText->error = get_string('invalid_edp', $p);
            } else {
                $return = '';
                $sd = (new DateTime($sd))->format('U');
                $ed = (new DateTime($ed))->format('U');
                if($type == 'eh'){
                    $data = $lib->get_learner_enrolment_history($sd, $ed);
                    for($i = 1; $i < (count($data)+1); $i++){
                        $return .= "<tr>
                            <td>$i</td>
                            <td><a href='window.location.href=./../../../course/view.php?id=".$data[$i-1][0]."' target='_blank'>".$data[$i-1][1]."</a></td>
                            <td><a href='window.location.href=./../../../user/profile.php?id=".$data[$i-1][2]."' target='_blank'>".$data[$i-1][3]."</a></td>
                            <td>".$data[$i-1][4]."</td>
                        </tr>";
                    }
                } else if($type == 'nuh'){
                    $data = $lib->get_new_users($sd, $ed);
                    for($i = 1; $i < (count($data)+1); $i++){
                        $return .= "<tr>
                            <td>$i</td>
                            <td><a href='window.location.href=./../../../user/profile.php?id=".$data[$i-1][0]."' target='_blank'>".$data[$i-1][1]."</a></td>
                            <td>".$data[$i-1][2]."</td>
                        </tr>";
                    }
                }
                $returnText->return = str_replace("  ","",$return);
            }
        }
    }
}
echo(json_encode($returnText));