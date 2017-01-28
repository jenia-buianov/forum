<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Settings_model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

   function get(){
       return
       $this->db->select('key,value')
                ->from('settings')
                ->where('isEnabled',1)
                ->get()
                ->result();
   }

   function getPageTitle()
   {
       if(uri_string())
           $res = $this->db->query("SELECT (CASE `m`.`key` WHEN 0 THEN `m`.`title` ELSE (SELECT `text` FROM `translation` WHERE `key`=`m`.`key`) END) as `title` FROM `map` as `m` WHERE `m`.`isEnabled`=1 AND `m`.`url`='".uri_string()."'")->row();
       else $res = $this->db->select('t.text as title')->from('map as m')->join('translation as t','m.key=t.key')->where('mapId',1)->get()->row();
       return $res->title;
   }

}
