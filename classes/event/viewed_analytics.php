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

class viewed_analytics extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return 'Manage course analytics page viewed';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed manage course analytics page.";
    }
    public function get_url(){
        return new \moodle_url('/local/courseanalytics/manage.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}