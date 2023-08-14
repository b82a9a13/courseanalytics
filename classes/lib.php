<?php
/**
 * @package   local_courseanalytics
 * @author    Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_courseanalytics;
use dml_exception;
use stdClass;

class lib{
    private function get_userid(): int{
        global $USER;
        return $USER->id;
    }

    //Get all courses that aren't already in the tracked_courses table
    public function get_remaining_courses(): array{
        global $DB;
        $array = [];
        $records = $DB->get_records_sql('SELECT {course}.id as id, {course}.fullname as fullname FROM {course} LEFT JOIN {course_tracked} ON {course}.id = {course_tracked}.courseid WHERE {course_tracked}.courseid IS NULL AND {course}.id != 1');
        foreach($records as $record){
            array_push($array, [$record->fullname, $record->id]);
        }
        asort($array);
        return $array;
    }

    //Add all courses to the current users list
    public function add_all_courses(): bool{
        global $DB;
        $userid = $this->get_userid();
        $array = $this->get_remaining_courses();
        foreach($array as $arr){
            if(!$DB->record_exists('course_tracked', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $arr[1]])){
                $record = new stdClass();
                $record->courseid = $arr[1];
                $record->userid = $userid;
                if($DB->insert_record('course_tracked', $record) === false){
                    return false;
                }
            }
        }
        return true;
    }

    //Add selected courses to the current users list
    public function add_courses($array): bool{
        global $DB;
        $userid = $this->get_userid();
        foreach($array as $arr){
            if(!$DB->record_exists('course_tracked', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $arr])){
                $record = new stdClass();
                $record->courseid = $arr;
                $record->userid = $userid;
                if($DB->insert_record('course_tracked', $record) === false){
                    return false;
                }
            }
        }
        return true;
    }

    //Get tracked courses list for the current user
    public function get_tracked_courses(): array{
        global $DB;
        $userid = $this->get_userid();
        $records = $DB->get_records_sql('SELECT {course_tracked}.id as id, {course}.fullname as fullname, {course_tracked}.courseid as courseid FROM {course_tracked} LEFT JOIN {course} ON {course}.id = {course_tracked}.courseid WHERE {course_tracked}.userid = ?',[$userid]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->fullname, $record->courseid]);
        }
        asort($array);
        return $array;
    }

    //Remove all tracked courses for a the current user
    public function delete_all_tracked_courses(): bool{
        global $DB;
        $userid = $this->get_userid();
        if($DB->delete_records('course_tracked', [$DB->sql_compare_text('userid') => $userid]) === false){
            return false;
        } else {
            return true;
        }
    }

    //Remove selected courses for the current users list
    public function delete_tracked_courses($array): bool{
        global $DB;
        $userid = $this->get_userid();
        foreach($array as $arr){
            if($DB->delete_records('course_tracked', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $arr]) === false){
                return false;
            }
        }
        return true;
    }
}