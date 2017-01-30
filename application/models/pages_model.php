<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Pages_Model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

   function getPage($id){
       return $this->db->select('p.pageId, t.text as title, tr.text as text')
                       ->from('pages as p')
                       ->join('translation as t','p.titleKey=t.key')
                       ->join('translation as tr','p.textKey=tr.key')
                       ->where('p.url',$id)
                       ->where('p.isEnabled',1)
                       ->where('t.lang',getLang())
                       ->where('tr.lang',getLang())
                       ->get()->row();


   }

}
