<?php
/**
 * Name: building_model.php
 * Author: Dabija Afanasie
 * Created: 02.11.2015
 * At: 17:36
 */

class Banner_Model extends CI_Model {



    function __construct()
    {
        parent::__construct();
    }

    function getAllBanners(){
        return
            $this->db->select('image,link,title')
                ->from('banners')
                ->where('isEnabled',1)
                ->order_by('order')
                ->get()
                ->result();
    }
}
