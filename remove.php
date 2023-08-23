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

$PAGE->set_url(new moodle_url('/local/courseanalytics/remove.php'));
$PAGE->set_context(\context_system::instance());
$title = get_string('remove_c', $p);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'title' => $title,
    'choose_title' => get_string('choose_ctr', $p),
    'all_title' => get_string('remove_ac', $p),
    'yes' => get_string('yes', $p),
    'save_c' => get_string('save_c', $p),
    'cancel' => get_string('cancel', $p),
    'type' => 'remove',
    'courses' => array_values($lib->get_tracked_courses())
];
echo $OUTPUT->render_from_template('local_courseanalytics/manage_courses', $template);

echo $OUTPUT->footer();
\local_courseanalytics\event\viewed_remove_courses::create(array('context' => \context_system::instance()))->trigger();