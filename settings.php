<?php
/**
* Adds Admin settings for the plugin
* @package     local_courseanalytics
* @author      Robert Tyrone Cullen
*/

defined('MOODLE_INTERNAL') || die();

if($hassiteconfig){
    //Adds new category to local_plugins
    $ADMIN->add('localplugins', new admin_category('local_courseanalytics', get_string('pluginname', 'local_courseanalytics')));
    //Adds a hyperlink to the manage page
    $ADMIN->add('local_courseanalytics', new admin_externalpage('local_lessonanalytic_manage', get_string('manage', 'local_courseanalytics'), $CFG->wwwroot.'/local/courseanalytics/manage.php'));
    //Adds a hyperlink to the search for learner page
    $ADMIN->add('local_courseanalytics', new admin_externalpage('local_lessonanalytics_sfl', get_string('search_fl', 'local_courseanalytics'), $CFG->wwwroot.'/local/courseanalytics/search.php'));
}
