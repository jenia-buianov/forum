<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Banner_model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

    function getAllBanners(){
        return
            $this->db->select('image,link')
                ->from('banners')
                ->where('isEnabled',1)
                ->get()
                ->result();
    }
}
