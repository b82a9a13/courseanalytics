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
}
