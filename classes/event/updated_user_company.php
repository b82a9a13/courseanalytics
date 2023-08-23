<?php
// This file is part of Moodle Activity Record Plugin
/**
 * @package     local_courseanalytics
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

namespace local_courseanalytics\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class updated_user_company extends base {
    protected function init(){
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return 'User company updated';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' updated the company for the user with id '".$this->relateduserid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/courseanalytics/search.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}