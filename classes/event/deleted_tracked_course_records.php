<?php
// This file is part of Moodle Course Analytics Plugin
/**
 * @package     local_courseanalytics
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

namespace local_courseanalytics\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class deleted_tracked_course_records extends base {
    protected function init(){
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return 'Tracked course record(s) deleted';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' deleted tracked course record(s)";
    }
    public function get_url(){
        return new \moodle_url('/local/courseanalytics/remove.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}