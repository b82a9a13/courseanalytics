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
    if(!in_array($type, ['cd', 'al', 'nau', 'eh', 'nuh'])){
        $returnText->error = get_string('invalid_tp', $p);
    } else {
        $tableclass = 'class="table table-bordered table-striped table-hover"';
        $title = '';
        $return = '';
        $head = '';
        $filter = '';
        $headings = [];
        if($type === 'cd'){
            $title = get_string('course_d', $p);
            $headings = ['#', get_string('course', $p), get_string('total_el', $p)];
            $data = $lib->tracked_course_enrolment();
            for($i = 1; $i < (count($data) + 1); $i++){
                $return .= "<tr>
                    <td>$i</td>
                    <td><a href='window.location.href=./../../../course/view.php?id=".$data[$i-1][2]."' target='_blank'>".$data[$i-1][1]."</a></td>
                    <td>".$data[$i-1][0]."</td>
                </tr>";
            }
            $return .= "</tbody></table>";
        } elseif($type === 'al'){
            $title = get_string('all_l', $p);
            $headings = ['#', get_string('learner', $p), get_string('company', $p), get_string('start_d', $p), get_string('last_a', $p)];
            $data = $lib->get_all_tracked_learners();
            for($i = 1; $i < (count($data)+1); $i++){
                $return .= "<tr>
                    <td>$i</td>
                    <td><a href='window.location.href=./../../../user/profile.php?id=".$data[$i-1][0]."' target='_blank'>".$data[$i-1][1]."</a></td>
                    <td>".$data[$i-1][2]."</td>
                    <td>".$data[$i-1][3]."</td>
                    <td>".$data[$i-1][4]."</td>
                </tr>";
            }
            $return .= "</tbody></table>";
        } elseif($type === 'nau'){
            $title = get_string('never_au', $p);
            $headings = ['#', get_string('fullname', $p), get_string('account_cd', $p)];
            $data = $lib->get_all_innactive_users();
            for($i = 1; $i < (count($data)+1); $i++){
                $return .= "<tr>
                    <td>$i</td>
                    <td><a href='window.location.href=./../../../user/profile.php?id=".$data[$i-1][0]."' target='_blank'>".$data[$i-1][1]."</a></td>
                    <td>".$data[$i-1][2]."</td>
                </tr>";
            }
            $return .= "</tbody></table>";
        } elseif($type === 'eh'){
            $startdate = strtotime('- 8 days');
            $enddate = date('U');
            $filter = '<div class="d-flex mb-2">
                <p class="mr-2 ml-2 pt-2">Start Date: </p>
                <input type="date" id="startdate" value="'.date('Y-m-d',$startdate).'">
                <p class="mr-2 ml-2 pt-2">End Date: </p>
                <input type="date" id="enddate" value="'.date('Y-m-d',$enddate).'">
                <button class="btn btn-primary ml-1" onclick="history_filter(`'.$type.'`)">Filter</button>
                <h4 id="hf_error" style="display:none;" class="text-danger ml-1"></h4>
            </div>';
            $title = get_string('enrolment_h', $p);
            $headings = ['#', get_string('course', $p), get_string('learner', $p), get_string('start_d', $p)];
            $data = $lib->get_learner_enrolment_history($startdate, $enddate);
            for($i = 1; $i < (count($data)+1); $i++){
                $return .= "<tr>
                    <td>$i</td>
                    <td><a href='window.location.href=./../../../course/view.php?id=".$data[$i-1][0]."' target='_blank'>".$data[$i-1][1]."</a></td>
                    <td><a href='window.location.href=./../../../user/profile.php?id=".$data[$i-1][2]."' target='_blank'>".$data[$i-1][3]."</a></td>
                    <td>".$data[$i-1][4]."</td>
                </tr>";
            }
            $return .= "</tbody></table>";
        } elseif($type === 'nuh'){
            $startdate = strtotime('- 8 days');
            $enddate = date('U');
            $filter = '<div class="d-flex mb-2">
                <p class="mr-2 ml-2 pt-2">Start Date: </p>
                <input type="date" id="startdate" value="'.date('Y-m-d',$startdate).'">
                <p class="mr-2 ml-2 pt-2">End Date: </p>
                <input type="date" id="enddate" value="'.date('Y-m-d',$enddate).'">
                <button class="btn btn-primary ml-1" onclick="history_filter(`'.$type.'`)">Filter</button>
                <h4 id="hf_error" style="display:none;" class="text-danger ml-1"></h4>
            </div>';
            $title = get_string('new_uh', $p);
            $headings = ['#', get_string('learner', $p), get_string('start_d', $p)];
            $data = $lib->get_new_users($startdate, $enddate);
            for($i = 1; $i < (count($data)+1); $i++){
                $return .= "<tr>
                    <td>$i</td>
                    <td><a href='window.location.href=./../../../user/profile.php?id=".$data[$i-1][0]."' target='_blank'>".$data[$i-1][1]."</a></td>
                    <td>".$data[$i-1][2]."</td>
                </tr>";
            }
            $return .= "</tbody></table>";
        }
        $head .= "<h4>$title</h4> $filter
            <table $tableclass id='".$type."_thead'>
                <thead>
                    <tr>
        ";
        for($i = 0; $i < count($headings); $i++){
            $head .= ($i === 0) ? 
            "<th class='c-pointer' onclick='headerClicked(`$type`, $i)' sort='asc'>$headings[$i]<span>&uarr;</span></th>" : 
            "<th class='c-pointer' onclick='headerClicked(`$type`, $i)' sort>$headings[$i]<span></span></th>";
        }
        $head .= "</tr>
                </thead>
            <tbody id='".$type."_tbody'>
        ";
        $returnText->return = str_replace("  ","",($head.$return));
        \local_courseanalytics\event\viewed_table::create(array('context' => \context_system::instance(), 'other' => $title))->trigger();
    }
}
echo(json_encode($returnText));