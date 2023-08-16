<?php

/**
 * @package     local_courseanalytics
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__ . '/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/courseanalytics:courseanalytics', $context);
$p = 'local_courseanalytics';
use local_courseanalytics\lib;
$lib = new lib();

$PAGE->set_url(new moodle_url('/local/courseanalytics/search.php'));
$PAGE->set_context(\context_system::instance());
$title = get_string('search_fl', $p);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'analytics' => get_string('analytics', $p),
    'username' => get_string('username', $p),
    'lastname' => get_string('lastname', $p),
    'firstname' => get_string('firstname', $p),
    'email' => get_string('email', $p),
    'city' => get_string('city', $p),
    'company' => get_string('company', $p),
    'search' => get_string('search', $p),
    'title' => $title
];
echo $OUTPUT->render_from_template('local_courseanalytics/search', $template);

echo $OUTPUT->footer();