<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_courseanalytics\lib;
$lib = new lib();

$returnText = new stdClass();
$error = '';
$p = 'local_courseanalytics';
$username = '';
$lastname = '';
$firstname = '';
$email = '';
$city = '';
$company = '';
if(!isset($_POST['username']) || !isset($_POST['lastname']) || !isset($_POST['firstname']) || !isset($_POST['email']) || !isset($_POST['city']) || !isset($_POST['company']) || !isset($_SESSION['ca_search'])){
    $error = get_string('missing_rv', $p);
} else {
    $error = 'Invalid:';
    $username = $_POST['username'];
    if(!preg_match("/^[a-zA-Z@. \-]*$/", $username) && !empty($username)){
        $error .= ' '.get_string('username', $p).'='.preg_replace("/[a-zA-Z@. \-]/", "",$username).',';
    }
    $lastname = $_POST['lastname'];
    if(!preg_match("/^[a-zA-Z \-]*$/", $lastname) && !empty($lastname)){
        $error .= ' '.get_string('lastname', $p).'='.preg_replace("/[a-zA-Z \-]/","",$lastname).',';
    }
    $firstname = $_POST['firstname'];
    if(!preg_match("/^[a-zA-Z \-]*$/", $firstname) && !empty($firstname)){
        $error .= ' '.get_string('firstname', $p).'='.preg_replace("/[a-zA-Z \-]/","",$firstname).',';
    }
    $email = $_POST['email'];
    if(!preg_match("/^[a-zA-Z0-9@\-_.]*$/", $email) && !empty($email)){
        $error .= ' '.get_string('email', $p).'='.preg_replace("/[a-zA-Z0-9@\-_.]/","",$email).',';
    }
    $city = $_POST['city'];
    if(!preg_match("/^[a-zA-Z \-]*$/", $city) && !empty($city)){
        $error .= ' '.get_string('city', $p).'='.preg_replace("/[a-zA-Z \-]/","",$city).',';
    }
    $company = $_POST['company'];
    if(!preg_match("/^[a-zA-Z\-()]*$/", $company) && !empty($company)){
        $error .= ' '.get_string('company', $p).'='.preg_replace("/[a-zA-Z\-()]/","",$company).',';
    }
}

if($error !== '' && $error !== 'Invalid:'){
    $returnText->error = $error;
} else {
    $array = $lib->get_learner_search_result([$username, $lastname, $firstname, $email, $city, $company]);
    if(empty($array)){
        $returnText->error = 'No search results';
    } else {
        if(empty($array)){
            $returnText->return = $array;
        } else {
            $return = '';
            foreach($array as $arr){
                $return .= "
                    <tr>
                        <td>$arr[0]</td>
                        <td><a href='window.location.href=./../../../user/profile.php?id=$arr[7]'>$arr[1]</a></td>
                        <td>$arr[2]</td>
                        <td>$arr[3]</td>
                        <td>$arr[4]</td>
                        <td>$arr[5]</td>
                        <td><input class='update-company' value='$arr[6]' changed uid='$arr[7]' onchange='changed_company(this)'></td>
                    </tr>
                ";
            }
            $returnText->return = str_replace("  ","",$return);
            \local_courseanalytics\event\viewed_search_results::create(array('context' => \context_system::instance()))->trigger();
        }
    }
}
echo(json_encode($returnText));