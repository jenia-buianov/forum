<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class User_Model extends CI_Model {



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

   function getStatus($id){
       return $this->db->select('time')->from('logs')->where('userId',$id)->order_by('logId','desc')->limit(1)->get()->row()->time;
   }

   function getUserByEmail($mail){
       return
           $this->db->select('password,userId')
               ->from('users')
               ->where('isEnabled',1)
               ->where('email',$mail)
               ->get()
               ->row();
   }

   function onlineList(){
       return $this->db->select('DISTINCT(l.userId), u.name')
                        ->from('logs as l')
                        ->join('users as u','l.userId=u.userId')
                        ->where('l.time >',time()-(15*60+1))
                        ->where('l.userId !=',0)
                        ->get()
                        ->result();
   }

   function countOnline(){
       return $this->db->select('COUNT(DISTINCT ip) as count')
                        ->from('logs')
                        ->where('time >',time()-(15*60+1))
                        ->get()
                        ->row()
                        ->count;
   }

}
