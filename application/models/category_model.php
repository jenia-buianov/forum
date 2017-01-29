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

    function getChildCategories($id){
        return
            $this->db->select('categoryId, url, titleKey, descriptionKey')
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

}
