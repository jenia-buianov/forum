<?php
/*
=====================================================
=  File description                                 =
= ------------------------------------------------- =
=  Name: user_helper.php                               =
=  Author: Munteanu Sergiu                          =
=  Created on: 01.11.2015                              =
=  At: 21:08                                      =
=                                                   =
=  Last modified on: 01.11.2015 - 21:08              =
=====================================================
*/



if(!function_exists('getLang'))
{
    function getLang()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $lang = $CI->session->userdata('lang');
        return (!$lang)? 'ru' : $lang;
    }
}

if(!function_exists('getUser'))
{
    function getUser()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $userId = $CI->session->userdata('user');
        return (!$userId)? false : $userId;
    }
}

if(!function_exists('translation'))
{
    function translation($key)
    {
        $CI =& get_instance();
        return $CI->db->select('text')
                                ->from('translation')
                                ->where('key',$key)
                                ->where('isEnabled',1)
                                ->get()
                                ->row()->text;

    }
}
if(!function_exists('makeData')) {
    function makeData($mustValues, $replaceTitles, $post, &$date, $add = array())
    {
        $data = array();
        foreach ($post as $k => $val) {
            $value = htmlspecialchars(trim($val['value']), ENT_QUOTES);
            if (in_array($val['name'], $mustValues) && empty($value)) {
                $date['error'] .= ', ' . $val['title'];
            }


            if (array_key_exists($val['name'], $replaceTitles)) $val['name'] = $replaceTitles[$val['name']];
            $data[$val['name']] = $value;
        }
        if (mb_strlen($date['error']) > 0) $date['error'] = mb_substr($date['error'], 2) . ' not entered';
        foreach ($add as $k => $v)
            $data[$k] = $v;
        return $data;
    }
}

if(!function_exists('breadcrumbs')){

    function breadcrumbs(){
        $CI =& get_instance();
        $array = array();

        if(!uri_string()){
            $array[] = array('url'=>base_url(), 'title'=>translation('home'));
        }
        else{

            $getMap = $CI->db->select('mapId,parentId,key,title')
                                ->from('map')
                                ->where('isEnabled',1)
                                ->where('url',uri_string())
                                ->get()->row();
            if($getMap->key) $array[] = array('url'=>uri_string(),'title'=>translation($getMap->title));
            else $array[] = array('url'=>uri_string(),'title'=>$getMap->title);

            while ($getMap->parentId)
            {
                $getMap = $CI->db->select('key,parentId,title')
                                    ->from('map')
                                    ->where('isEnabled',1)
                                    ->where('mapId',$getMap->parentId)
                                    ->get()->row();
                if($getMap->key) $array[] = array('url'=>uri_string(),'title'=>translation($getMap->title));
                else $array[] = array('url'=>uri_string(),'title'=>$getMap->title);
            }
        }
        $array = array_reverse($array);
        return $array;
    }
}

if(!function_exists('getPageTitle')){

    function getPageTitle($crumbs, $titleSite){
        $title = '';
        $crumbs = array_reverse($crumbs);
        for ($k=0;$k<count($crumbs)-1;$k++){
            $title.=$crumbs[$k]['title'].' - ';
        }
        if(count($crumbs)>1) return mb_substr($title,0,mb_strlen($title)-3).' - '.$titleSite;
        else return $titleSite;
    }
}