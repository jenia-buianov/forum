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

if(!function_exists('getUserName'))
{
    function getUserName($id)
    {
        $CI =& get_instance();
        return $CI->db->select('name')->from('users')->where('userId',$id)->where('isEnabled',1)->get()->row()->name;

    }
}

if(!function_exists('getLastPage'))
{
    function getLastPage()
    {
        $CI =& get_instance();
        if(!getUser()) $user = 0; else $user = getUser();
        return $CI->db->select('page')
            ->from('logs')
            ->where('userId',$user)
            ->where('ip',$_SERVER['REMOTE_ADDR'])
            ->order_by('logId','desc')
            ->limit(1)
            ->get()
            ->row()->page;
    }
}

if(!function_exists('insertIntoMap'))
{
    function insertIntoMap($title,$key,$url,$parent = 0)
    {
        $CI =& get_instance();
        $CI->db->query("INSERT INTO `map`(`title`,`key`,`url`,`parentId`) VALUES ('".htmlspecialchars($title,ENT_QUOTES)."','".$key."','".htmlspecialchars($url,ENT_QUOTES)."','".$parent."')");
    }
}

if(!function_exists('getListLangs'))
{
    function getListLangs()
    {
        $CI =& get_instance();
        return $CI->db->select('shortTitle as abb')->from('languages')->where('isEnabled',1)->get()->result_array();

    }
}


if(!function_exists('addTranslation'))
{
    function addTranslation($key,$text,$lang = array('ru'))
    {
        $CI =& get_instance();
        for($k=0;$k<count($lang);$k++){
            $CI->db->query("INSERT INTO `translation`(`key`,`text`,`lang`) VALUES ('".htmlspecialchars($key,ENT_QUOTES)."','".htmlspecialchars($text,ENT_QUOTES)."','".$lang[$k]['abb']."')");
        }
    }
}

if(!function_exists('translation'))
{
    function translation($key)
    {
        $CI =& get_instance();
        return htmlspecialchars_decode($CI->db->select('text')
                                ->from('translation')
                                ->where('key',$key)
                                ->where('isEnabled',1)
                                ->get()
                                ->row()->text);

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
            if(!count($getMap)){
                $data['heading'] = 'ERROR 404';
                $data['message'] = translation('404');
                echo translation('404');
                exit(0);
            }
            if($getMap->key) $array[] = array('url'=>base_url(uri_string()),'title'=>translation($getMap->title));
            else $array[] = array('url'=>base_url(uri_string()),'title'=>$getMap->title);

            while ($getMap->parentId)
            {
                $getMap = $CI->db->select('key,parentId,title,url')
                                    ->from('map')
                                    ->where('isEnabled',1)
                                    ->where('mapId',$getMap->parentId)
                                    ->get()->row();
                if($getMap->key) $array[] = array('url'=>base_url($getMap->url),'title'=>translation($getMap->title));
                else $array[] = array('url'=>base_url($getMap->url),'title'=>$getMap->title);
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

if(!function_exists('pagination')){

    function pagination($url, $pages, $currentPage){
        echo'<div class="col-md-12 paginataion_all">';
        if($currentPage-5 > 10) echo '<a href="'.$url.'?page=1">1</a> ... ';
        for($k=$currentPage-5;$k<=$currentPage+10;$k++)
        {
            if($k>0 and $k<=$pages)
                if($k==$currentPage) echo ' <a href="'.$url.'?page='.$k.'" class="button active"><b>'.$k.'</b></a> ';
                else echo ' <a href="'.$url.'?page='.$k.'" class="button">'.$k.'</a> ';

        }
       if($k<$pages){
            echo ' ... <a href="'.$url.'?page='.$pages.'" class="button">'.$pages.'</a>';
        }
        echo'</div>';
    }
}

if(!function_exists('send_email')){

    function send($HOST,$PORT,$TYPE,$AUTH,$MAIL_FROM,$PASSWORD,$TITLE,$MAIL_TO,$NAME,$BODY,$SUBJECT)
    {
        $CI =& get_instance();
        $CI->load->library('PHPMailer');
        $mail = new PHPMailer;
        $mail->isSMTP();

        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = $HOST;
        $mail->Port = $PORT;
        $mail->SMTPSecure = $TYPE;
        $mail->SMTPAuth = $AUTH;
        $mail->Username = $MAIL_FROM;
        $mail->Password = $PASSWORD;
        $mail->setFrom($MAIL_FROM, $TITLE);
        $mail->addReplyTo($MAIL_TO, $NAME);
        $mail->addAddress($MAIL_TO, $NAME);
        $mail->CharSet = "UTF-8";
        $mail->Subject = $SUBJECT;
        $body = mb_convert_encoding($BODY, mb_detect_encoding($BODY), 'UTF-8');
        $mail->msgHTML($body);
        $mail->AltBody = 'This is a plain-text message body';
        if (!$mail->send()) {
            return "Mailer Error: " . $mail->ErrorInfo;
        }else{
            return true;
        }
    }
}