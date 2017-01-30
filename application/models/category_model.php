<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Category_Model extends CI_Model {



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
    function getTopicsFromCategory($id){
        return $this->db->select('m.messageId, m.url, m.image, m.title, m.vip, u.name, m.datetime, m.userId, (SELECT COUNT(`messageId`) FROM `messages` WHERE `parentId`=`m`.`messageId` AND `isEnabled`=1) as messages')
                        ->from('messages as m')
                        ->join('users as u','u.userId=m.userId')
                        ->where('m.isEnabled',1)
                        ->where('m.parentId',0)
                        ->where('m.categoryId',$id)
                        ->order_by('m.vip','desc')
                        ->order_by('m.datetime','desc')
                        ->get()
                        ->result();
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

    function lastTopicPost($topic){
        return $this->db->select('CASE COUNT(`m`.`messageId`)
	WHEN 0 THEN (SELECT CONCAT_WS(\'|\', `text`, `datetime`, `userId`, `messageId`) FROM `messages` WHERE `messageId`='.$topic.')
	ELSE (SELECT CONCAT_WS(\'|\',`ms`.`text`,`ms`.`datetime`,`ms`.`userId`,`ms`.`messageId`) FROM `messages` as `ms`  WHERE `ms`.`parentId`=2 AND `ms`.`isEnabled`=1 ORDER BY `ms`.`datetime` DESC LIMIT 1)
	END as `text`')
            ->from('messages as m')
            ->where('m.isEnabled',1)
            ->where('m.parentId',$topic)
            ->limit(1)
            ->get()
            ->row()->text;

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
            ->order_by('m.messageId','desc')
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
