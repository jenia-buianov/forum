<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Topic_Model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

    function topicViews($url){
        return $this->db->select('COUNT(DISTINCT(`ip`)) as count')
                        ->from('logs')
                        ->like('page',$url)
                        ->get()->row()->count;
    }

    function getTopic($url){
        return $this->db->select('m.*, u.name')
                        ->join('users as u','u.userId=m.userId')
                        ->from('messages as m')
                        ->where('m.isEnabled',1)
                        ->where('m.url',$url)
                        ->get()->row();
    }

    function getMessages($id,$start,$count){
        return $this->db->select('m.userId, m.text, m.datetime, ')
    }


}
