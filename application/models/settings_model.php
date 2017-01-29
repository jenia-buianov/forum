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

   function logs(){
       if(getUser()) $user = getUser(); else $user = 0;
       $this->db->query("INSERT INTO `logs`(`ip`,`userId`,`page`,`lang`,`time`)VALUES('".$_SERVER['REMOTE_ADDR']."','".$user."','".uri_string()."','".getLang()."','".time()."')");
   }


}
