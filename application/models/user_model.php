<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class User_model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

   function verifyEmail($mail){
       return
       $this->db->select('COUNT(`userId`) as count')
                ->from('users')
                ->where('isEnabled',1)
                ->where('email',$mail)
                ->get()
                ->row()->count;
   }


}
