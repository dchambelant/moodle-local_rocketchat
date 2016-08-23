<?php

namespace local_rocketchat\events\observers;

class group_member_added {
    public static function call($event) {
        $data = \local_rocketchat\utilities::access_protected($event, 'data');

        if(self::_is_event_based_sync($data['courseid'])) {
            self::_add_subscription($data);
        }        
    } 

    private static function _is_event_based_sync($courseid) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course'=>$courseid));
        return $rocketchatcourse->eventbasedsync;
    }

    private static function _add_subscription($data) {
        global $DB;

        $course = $DB->get_record('course', array('id'=>$data['courseid']));
        $group = $DB->get_record('groups', array('id'=>$data['objectid']));
        $user = $DB->get_record('user', array('id'=>$data['relateduserid']));

        $client = new \local_rocketchat\client();

        if($client->authenticated) {        
            $subscriptionapi = new \local_rocketchat\integration\subscriptions($client);
            $subscriptionapi->add_subscription_for_user($user, $group); 
        }
    }
}