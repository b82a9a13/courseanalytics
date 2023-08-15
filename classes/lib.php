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
        $records = $DB->get_records_sql('SELECT c.id as id, c.fullname as fullname FROM {course} c LEFT JOIN {course_tracked} ct ON c.id = ct.courseid WHERE ct.courseid IS NULL AND c.id != 1');
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
        $records = $DB->get_records_sql('SELECT ct.id as id, c.fullname as fullname, ct.courseid as courseid FROM {course_tracked} ct LEFT JOIN {course} c ON c.id = ct.courseid WHERE ct.userid = ?',[$userid]);
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

    private function get_all_enrolled_learners(): array{
        global $DB;
        return $DB->get_records_sql('SELECT ra.id as id, c.id as courseid, c.fullname as fullname, eu.userid as userid FROM {course} c
            INNER JOIN {context} ctx ON c.id = ctx.instanceid
            INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.roleid = 5
            INNER JOIN (
                SELECT e.courseid, ue.userid FROM {enrol} e
                INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status != 1
            ) eu ON c.id = eu.courseid AND ra.userid = eu.userid'
        );
    }

    //Get all enrolled users in the current users tracked courses list
    public function tracked_course_enrolment(): array{
        global $DB;
        $records = $this->get_all_enrolled_learners();
        $array = [];
        foreach ($records as $record) {
            array_push($array, [$record->courseid, $record->userid]);
        }
        $records = $DB->get_records_sql('SELECT id, courseid FROM {course_tracked} WHERE userid = ?',[$this->get_userid()]);
        $array4 = [];
        foreach($array as $arr){
            foreach($records as $record){
                if($arr[0] === $record->courseid){
                    array_push($array4, $arr[0]);
                }
            }
        }
        $result = array_count_values($array4);
        $records = $DB->get_records_sql('SELECT id, fullname FROM {course}');
        $endArray = [];
        foreach($records as $record){
            if(isset($result[$record->id])){
                array_push($endArray, [$record->fullname, $result[$record->id], $record->id]);
            }
        }
        asort($endArray);
        return $endArray;
    }

    //Get all learners currently enrolled
    public function get_all_tracked_learners(): array{
        global $DB;
        $records = $this->get_all_enrolled_learners();
        $array = [];
        $tracked = $DB->get_records_sql('SELECT id, courseid FROM {course_tracked} WHERE userid = ?',[$this->get_userid()]);
        foreach($records as $record) {
            foreach($tracked as $track){
                if($track->courseid === $record->courseid){
                    $user = $DB->get_record_sql('SELECT firstname, lastname, institution, firstaccess, lastaccess FROM {user} WHERE id = ?',[$record->userid]);
                    if(!in_array([$record->userid, $user->firstname.' '.$user->lastname, $user->institution, date('d/m/Y',$user->firstaccess), date('d/m/Y',$user->lastaccess)], $array)){
                        array_push($array, [$record->userid, $user->firstname.' '.$user->lastname, $user->institution, date('d/m/Y',$user->firstaccess), date('d/m/Y',$user->lastaccess)]);
                    }
                }
            }
        }
        return $array;
    }

    //Get all users that have not accessed their account
    public function get_all_innactive_users(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT id, firstname, lastname, timecreated FROM {user} WHERE firstaccess = 0 AND id != 1 AND deleted = 0 AND suspended = 0');
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->id, $record->firstname.' '.$record->lastname, date('d/m/Y',$record->timecreated)]);
        }
        return $array;
    }

    //Get enrolment history for the tracked courses for the current user
    public function get_learner_enrolment_history(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT ra.id as id, c.id as courseid, c.fullname as fullname, eu.userid as userid, eu.timecreated as timecreated FROM {course} c
        INNER JOIN {context} ctx ON c.id = ctx.instanceid
        INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.roleid = 5
        INNER JOIN (
            SELECT e.courseid, ue.userid, ue.timecreated FROM {enrol} e
            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status != 1
        ) eu ON c.id = eu.courseid AND ra.userid = eu.userid'
        );
        $array = [];
        $tracked = $DB->get_records_sql('SELECT id, courseid FROM {course_tracked} WHERE userid = ?',[$this->get_userid()]);
        foreach($records as $record) {
            foreach($tracked as $track){
                if($track->courseid === $record->courseid){
                    $user = $DB->get_record_sql('SELECT firstname, lastname FROM {user} WHERE id = ?',[$record->userid]);
                    array_push($array, [$record->courseid, $record->fullname, $record->userid, $user->firstname.' '.$user->lastname, date('d/m/Y',$record->timecreated)]);
                }
            }
        }
        return $array;
    }

    //Get all new users
    public function get_new_users($start, $end): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT id, firstaccess, firstname, lastname FROM {user} WHERE suspended = 0 AND firstaccess != 0 AND firstaccess < ? AND firstaccess > ?',[$end, $start]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->firstaccess, $record->firstname.' '.$record->lastname, $record->id]);
        }
        asort($array);
        $result = [];
        foreach($array as $arr){
            array_push($result, [$arr[2], $arr[1], date('d/m/Y',$arr[0])]);
        }
        return $result;
    }
}