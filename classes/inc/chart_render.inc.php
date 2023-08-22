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
    if(!in_array($type, ['eu'])){
        $returnText->error = get_string('invalid_tp', $p);
    } else {
        if($type === 'eu'){
            $return = '<h4>'.get_string('enrolled_u', $p).'</h4>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>'.get_string('course', $p).'</th>
                            <th>'.get_string('total_el', $p).'</th>
                        </tr>
                    </thead>
                    <tbody>
            ';
            $data = $lib->tracked_course_enrolment();
            $scriptArr = [];
            foreach($data as $dat){
                $return .= "<tr><td><a href='window.location.href=./../../../course/view.php?id=".$dat[2]."' target='_blank'>$dat[1]</a></td><td>$dat[0]</td></tr>";
                array_push($scriptArr, [$dat[0], $dat[1]]);
            }
            $return .= '</tbody></table>';
            $returnText->script = $scriptArr;
            $returnText->return = str_replace("  ","",$return);
        }
    }
}
echo(json_encode($returnText));