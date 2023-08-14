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

$PAGE->set_url(new moodle_url('/local/courseanalytics/add.php'));
$PAGE->set_context(\context_system::instance());
$title = get_string('add_c', $p);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'save_c' => get_string('save_c', $p),
    'cancel' => get_string('cancel', $p),
    'choose_title' => get_string('choose_cta', $p),
    'all_title' => get_string('select_ac', $p),
    'yes' => get_string('yes', $p),
    'title' => $title,
    'type' => 'add',
    'courses' => $lib->get_remaining_courses(),
];
echo $OUTPUT->render_from_template('local_courseanalytics/manage_courses', $template);

echo $OUTPUT->footer();