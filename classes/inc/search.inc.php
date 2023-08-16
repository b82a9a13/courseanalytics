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
if(!isset($_POST['username']) || !isset($_POST['lastname']) || !isset($_POST['firstname']) || !isset($_POST['email']) || !isset($_POST['city']) || !isset($_POST['company'])){
    $error = 'Missing required values';
} else {
    $error = 'Invalid:';
    $username = $_POST['username'];
    if(!preg_match("/^[a-zA-Z@. \-]*$/", $username) && !empty($username)){
        $error .= ' username='.preg_replace("/[a-zA-Z@. \-]/", "",$username).',';
    }
    $lastname = $_POST['lastname'];
    if(!preg_match("/^[a-zA-Z \-]*$/", $lastname) && !empty($lastname)){
        $error .= ' lastname='.preg_replace("/[a-zA-Z \-]/","",$lastname).',';
    }
    $firstname = $_POST['firstname'];
    if(!preg_match("/^[a-zA-Z \-]*$/", $firstname) && !empty($firstname)){
        $error .= ' firstname='.preg_replace("/[a-zA-Z \-]/","",$firstname).',';
    }
    $email = $_POST['email'];
    if(!preg_match("/^[a-zA-Z0-9@\-_.]*$/", $email) && !empty($email)){
        $error .= ' email='.preg_replace("/[a-zA-Z0-9@\-\_\.]/","",$email).',';
    }
    $city = $_POST['city'];
    if(!preg_match("/^[a-zA-Z \-]*$/", $city) && !empty($city)){
        $error .= ' city='.preg_replace("/[a-zA-Z \-]/","",$city).',';
    }
    $company = $_POST['company'];
    if(!preg_match("/^[a-zA-Z\-()]*$/", $company) && !empty($company)){
        $error .= ' company='.preg_replace("/[a-zA-Z\-()]/","",$company).',';
    }
}

if($error !== '' && $error !== 'Invalid:'){
    $returnText->error = $error;
} else {
    $array = $lib->get_learner_search_result([$username, $lastname, $firstname, $email, $city, $company]);
    if(empty($array)){
        $returnText->error = 'No search results';
    } else {
        $returnText->return = $array;
    }
}
echo(json_encode($returnText));