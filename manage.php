<?php

/**
 * @package     local_courseanalytics
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__ . '/../../config.php');
use local_courseanalytics\lib;
$lib = new lib();
require_login();
$context = context_system::instance();
require_capability('local/courseanalytics:courseanalytics', $context);
$p = 'local_courseanalytics';

$PAGE->set_url(new moodle_url('/local/courseanalytics/manage.php'));
$PAGE->set_context(\context_system::instance());
$title = get_string('manage_ca', $p);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'title' => get_string('manage_ca', $p),
    'title_tcl' => get_string('tracked_cl', $p),
    'show_l' => get_string('show_l', $p),
    'add_btn' => get_string('add_c', $p),
    'del_btn' => get_string('remove_c', $p),
    'courses' => array_values($lib->get_tracked_courses())
];
echo $OUTPUT->render_from_template('local_courseanalytics/manage', $template);

echo $OUTPUT->footer();