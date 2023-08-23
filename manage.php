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

$PAGE->set_url(new moodle_url('/local/courseanalytics/manage.php'));
$PAGE->set_context(\context_system::instance());
$title = get_string('manage_ca', $p);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'title' => $title,
    'title_tcl' => get_string('tracked_cl', $p),
    'show_l' => get_string('show_l', $p),
    'add_btn' => get_string('add_c', $p),
    'del_btn' => get_string('remove_c', $p),
    'enrolled_u' => get_string('enrolled_u', $p),
    'charts' => get_string('charts', $p),
    'tables' => get_string('tables', $p),
    'course_d' => get_string('course_d', $p),
    'all_l' => get_string('all_l', $p),
    'never_au' => get_string('never_au', $p),
    'enrolment_h' => get_string('enrolment_h', $p),
    'new_uh' => get_string('new_uh', $p),
    'print_fr' => get_string('print_fr', $p),
    'search_fl' => get_string('search_fl', $p),
    'show_cd' => get_string('show_cd', $p),
    'courses' => array_values($lib->get_tracked_courses())
];
echo $OUTPUT->render_from_template('local_courseanalytics/manage', $template);

echo $OUTPUT->footer();
$_SESSION['ca_manage'] = true;
\local_courseanalytics\event\viewed_analytics::create(array('context' => \context_system::instance()))->trigger();