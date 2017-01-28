<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Menu_model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

    function getAllMenu(){
        return
            $this->db->select('m.link, m.icon, t.text')
                ->from('menu as m')
                ->join('translation as t','m.titleKey = t.key')
                ->where('m.isEnabled',1)
                ->where('t.lang',getLang())
                ->get()
                ->result();
    }

}
