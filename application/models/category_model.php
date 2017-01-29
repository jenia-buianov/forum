<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Category_model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

   function getTopCategories(){
       return
       $this->db->select('categoryId, url, titleKey')
                ->from('categories')
                ->where('isEnabled',1)
                ->where('parentId',0)
                ->get()
                ->result();
   }
    function getCategoryInfo($id){
        return
            $this->db->select('categoryId, url, titleKey, parentId')
                ->from('categories')
                ->where('isEnabled',1)
                ->where('url',$id)
                ->get()
                ->row();
    }

    function getChildCategories($id){
        return
            $this->db->select('categoryId, url, titleKey, descriptionKey, locked')
                ->from('categories')
                ->where('isEnabled',1)
                ->where('parentId',$id)
                ->get()
                ->result();
    }

    function countTopics($id){

        return $this->db->select('COUNT(`messageId`) as count')
                        ->from('messages')
                        ->where('isEnabled',1)
                        ->where('verifyed',1)
                        ->where('categoryId',$id)
                        ->where('parentId',0)
                        ->get()
                        ->row()->count;
    }
    function countMessages($id){

        return $this->db->select('COUNT(`messageId`) as count')
                        ->from('messages')
                        ->where('isEnabled',1)
                        ->where('verifyed',1)
                        ->where('categoryId',$id)
                        ->where('parentId !=',0)
                        ->get()
                        ->row()->count;
    }

    function lastPost($cat){
        return $this->db->select('m.userId, m.messageId, u.name, m.datetime, m.userId,
(CASE `m`.`parentId`
	WHEN 0 THEN `m`.`title`
	ELSE (SELECT `title` FROM `messages` WHERE `messageId`=`m`.`parentId`)
	END
) as `title`, (CASE `m`.`parentId`
	WHEN 0 THEN `m`.`url`
	ELSE (SELECT `url` FROM `messages` WHERE `messageId`=`m`.`parentId`)
	END
) as `url`')
            ->from('messages as m')
            ->join('users as u','u.userId=m.userId')
            ->where('m.isEnabled',1)
            ->where('m.categoryId',$cat)
            ->order_by('m.datetime','desc')
            ->limit(1)
            ->get()
            ->row_array();
    }

    function unreadCategory($cat){
        return $this->db->select('COUNT(`messageId`) as count')
                        ->from('messages')
                        ->where('isEnabled',1)
                        ->where('categoryId',$cat)
                        ->like('datetime',date('Y-m-d'))
                        ->get()->row()->count;
    }

    function countCategories(){
        return $this->db->select('COUNT(categoryId) as count')
                        ->from('categories')
                        ->where('isEnabled',1)
                        ->get()->row()->count;

    }
    function countAllTopics(){
        return $this->db->select('COUNT(messageId) as count')
            ->from('messages')
            ->where('isEnabled',1)
            ->where('parentId',0)
            ->where('verifyed',1)
            ->get()->row()->count;
    }

    function countAllMessages(){
        return $this->db->select('COUNT(messageId) as count')
            ->from('messages')
            ->where('isEnabled',1)
            ->where('parentId !=',0)
            ->where('verifyed',1)
            ->get()->row()->count;
    }

}
