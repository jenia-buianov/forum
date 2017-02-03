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

    function getMessages($id,$start,$count)
    {
        return $this->db->select('m.userId, m.text, m.datetime, m.messageId, m.verifyed,  u.name, u.registration, t.text as group, g.titleKey')
            ->join('users as u', 'm.userId=u.userId')
            ->join('groups as g', 'u.groupId=g.groupId')
            ->join('translation as t', 'g.titleKey=t.key')
            ->from('messages as m')
            ->where('m.parentId', $id)
            ->where('m.isEnabled', 1)
            ->order_by('m.datetime')
            ->limit($count, $start)
            ->get()->result();
    }

    function getCountResponds($id){
        return $this->db->select('COUNT(messageId) as count')->from('messages')->where('isEnabled',1)->where('parentId',$id)->get()->row()->count;
    }


}
