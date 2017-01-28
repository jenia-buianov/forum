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



if(!function_exists('formatHtmlConverters'))
{
    function formatHtmlConverters($type,$dataName)
    {
        $name = md5('userData_'.$userId);
        return $name;
    }
}

if(!function_exists('getCacheName'))
{
    function getCacheName($userId)
    {
        $name = md5('userData_'.$userId);
        return $name;
    }
}

if(!function_exists('getUserId'))
{
    function getUserId()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $userId = $CI->session->userdata('user_id');
        return (!$userId)? false : $userId;
    }
}

if(!function_exists('getOponentId'))
{
    function getOponentId()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $opId = $CI->session->userdata('oponentid');
        return (!$opId)? false : $opId;
    }
}

if(!function_exists('getCompetitionId'))
{
    function getCompetitionId()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $comId = $CI->session->userdata('competitionid');
        return (!$comId)? false : $comId;
    }
}

if(!function_exists('CompetitionFinish'))
{
    function CompetitionFinish()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->session->unset_userdata('competitionid');
        $CI->session->unset_userdata('oponentid');
        return true;
    }
}

if(!function_exists('isUser'))
{
    function isUser()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $userId = $CI->session->userdata('user_id');
        return (!$userId)? false : true;
    }
}

if(!function_exists('cacheUser'))
{
    function cacheUser($user)
    {
        $CI =& get_instance();
        $CI->load->driver('cache');
        $user_id = $user['user_id'];
        $user = serialize($user);
        $user = urlencode($user);
        $CI->cache->file->save(getCacheName($user_id), $user, 6000);
    }
}

if(!function_exists('restartUser'))
{
    function restartUser($userId=0)
    {
        $CI =& get_instance();
        if($userId==0)
        {
            $userId = $CI->session->userdata('user_id');
        }
        $CI->load->driver('cache');

        $CI->cache->file->delete(getCacheName($userId));
    }
}

if(!function_exists('getUser'))
{
    function getUser($userId=0)
    {
		$CI =& get_instance();
        if($userId==0)
        {
            $userId = $CI->session->userdata('user_id');
            if(!isUser())
            {
                return renderNoUser();
            }
        }

		$CI->load->driver('cache');

		if ( ! $user = $CI->cache->file->get(getCacheName($userId)))
		{
			$CI->load->library('session');
			$CI->load->model('user_model','user');
			$user = $CI->user->getUserData($userId);
            if(isset($user['user_id']) && $user['user_id'])
            {
                cacheUser($user);
            }
			else
            {
               return renderNoUser();
            }
		}
        else
        {
            $user = urldecode($user);
            $user = unserialize($user);
        }
		return $user;
    }
}

if(!function_exists('user'))
{
    function user(&$var)
    {
        if(isset($var))
        {
            return $var;
        }
        else
        {
            return '';
        }
    }
}

if(!function_exists('isStartActive'))
{
    function isStartActive()
    {
        $user = getUser();
        $start_time = $user['start_time'];
        $restartTime = getGameConstants('start_timer_time_seconds');
        if(time()>$start_time+$restartTime)
        {
            return true;
        }
        return false;
    }
}

if(!function_exists('getStartTimer'))
{
    function getStartTimer()
    {
        if(isStartActive())
        {
            return 0;
        }
        $user = getUser();
        $start_time = $user['start_time'];
        $restartTime = getGameConstants('start_timer_time_seconds');

        return $start_time+$restartTime - time();
    }
}

if(!function_exists('getEnergyTimer'))
{
    function getEnergyTimer()
    {
//        if(isStartActive())
//        {
//            return 0;
//        }
        $user = getUser();
        $start_time = $user['energy_timer'];
        $restartTime = getGameConstants('energy_increse_time_seconds');
//        $restartTime = 2*60;
        $energyResetAfter = $start_time+$restartTime - time();
        if($energyResetAfter<=0)
        {
            $CI =& get_instance();
            $count = intval((time() - $start_time)/$restartTime);
            $timeToSet = time() - (time() - ($start_time + $count * $restartTime));
            $finalEnergy = $user['energy']+$count;
            $finalEnergy = ($finalEnergy<=getGameConstants('max_energy_by_time'))?$finalEnergy : getGameConstants('max_energy_by_time');
            $CI->user_model->update(getUserId(),array('energy'=>$finalEnergy,'energy_timer'=>$timeToSet),TRUE);
            $energyResetAfter = $timeToSet+$restartTime - time();
        }
        return $energyResetAfter;
    }
}

if(!function_exists('renderNoUser'))
{
    function renderNoUser()
    {
        exit('No User Data');
    }
}
if(!function_exists('getCurrentRegionId'))
{
    function getCurrentRegionId()
    {
        $CI =& get_instance();
        $regionId = $CI->session->userdata('region_id');
        if(!$regionId)
        {
            $user = getUser();
            $regionId = $user['default_region_id'];
        }
        return $regionId;
    }
}
if(!function_exists('getHumansDefId'))
{
    function getHumansDefId()
    {
        return 100;
    }
}
if(!function_exists('formatExp'))
{
    function formatExp($increment)
    {
        $CI =& get_instance();
//        $user = getUser();
//        pr($increment);
//        pr($CI->gamer->getData());
        $CI->gamer->updateData('have_exp',$increment);
//        $dataUpdate['need_exp'] = $user['need_exp'];
        if($CI->gamer->getData('have_exp')>=$CI->gamer->getData('need_exp'))
        {
            $nextLevel = $CI->gamer->getData('level') + 1;
            $nextExpNeed = $CI->gamer->getData('need_exp') + ($nextLevel+1) * getGameConstants('exp_multiplication_by_level');
            $newHaveExp = $CI->gamer->getData('have_exp')+$increment-$CI->gamer->getData('need_exp');
            $CI->gamer->setData('have_exp',$newHaveExp);
            $CI->gamer->setData('need_exp',$nextExpNeed);
            $CI->gamer->setData('level',$nextLevel);
            formatLevelBonus($CI->gamer->getData('level'));
        }
    }
}


if(!function_exists('formatLevelBonus'))
{
    function formatLevelBonus($level)
    {
        $CI =& get_instance();
        $level_info = array();
        switch($level){
            case 1:
                $max_energy = getGameConstants('max_energy_by_time');
                $max_energy = ($max_energy<$CI->gamer->getData('energy'))?$CI->gamer->getData('energy'):$max_energy;
                $level_info['text'] = __('Ai primit bonus %s Gold, %s Energie, %s Evo',1,$max_energy,100);
                $CI->gamer->updateData('gold',1);
                $CI->gamer->updateData('money',100);
                $CI->gamer->setData('energy',$max_energy);
                $level_info['buildings'][] = array(
                    'logo' => 'store',
                    'name' => __('Magazin'),
                );
                $level_info['buildings'][] = array(
                    'logo' => 'pharmacy',
                    'name' => __('Farmacie'),
                );
                $buildings_arr = array(6,13);
                $CI->building_model->_database->where('user_id', $CI->gamer->getData('user_id'));
                $CI->building_model->_database->where('user_region_id', $CI->gamer->getData('default_region_id'));
                $CI->building_model->_database->where_in('building_id', $buildings_arr);
                $CI->building_model->_database->update('users_regions_buildings',array('opened'=>1));
                break;
            case 2:
                $max_energy = getGameConstants('max_energy_by_time');
                $max_energy = ($max_energy<$CI->gamer->getData('energy'))?$CI->gamer->getData('energy'):$max_energy;
                $level_info['text'] = __('Ai primit bonus %s Gold, %s Energie, %s Evo',1,$max_energy,100);
                $CI->gamer->updateData('gold',1);
                $CI->gamer->updateData('money',100);
                $CI->gamer->setData('energy',$max_energy);
                $level_info['buildings'][] = array(
                    'logo' => 'villa',
                    'name' => __('Vila'),
                );
                $level_info['buildings'][] = array(
                    'logo' => 'kindergarden',
                    'name' => __('Gradinita'),
                );
                $level_info['buildings'][] = array(
                    'logo' => 'library',
                    'name' => __('Biblioteca'),
                );
                $buildings_arr = array(2,24,36);
                $CI->building_model->_database->where('user_id', $CI->gamer->getData('user_id'));
                $CI->building_model->_database->where('user_region_id', $CI->gamer->getData('default_region_id'));
                $CI->building_model->_database->where_in('building_id', $buildings_arr);
                $CI->building_model->_database->update('users_regions_buildings',array('opened'=>1));
                break;
            case 3:
                $max_energy = getGameConstants('max_energy_by_time');
                $max_energy = ($max_energy<$CI->gamer->getData('energy'))?$CI->gamer->getData('energy'):$max_energy;
                $level_info['text'] = __('Ai primit bonus %s Gold, %s Energie, %s Evo',1,$max_energy,100);
                $CI->gamer->updateData('gold',1);
                $CI->gamer->updateData('money',200);
                $CI->gamer->setData('energy',$max_energy);
                $level_info['buildings'][] = array(
                    'logo' => 'market',
                    'name' => __('Piata'),
                );
                $level_info['buildings'][] = array(
                    'logo' => 'gas-station',
                    'name' => __('Benzinarie'),
                );
                $buildings_arr = array(7,14);
                $CI->building_model->_database->where('user_id', $CI->gamer->getData('user_id'));
                $CI->building_model->_database->where('user_region_id', $CI->gamer->getData('default_region_id'));
                $CI->building_model->_database->where_in('building_id', $buildings_arr);
                $CI->building_model->_database->update('users_regions_buildings',array('opened'=>1));
                break;
            case 4:
                $max_energy = getGameConstants('max_energy_by_time');
                $max_energy = ($max_energy<$CI->gamer->getData('energy'))?$CI->gamer->getData('energy'):$max_energy;
                $level_info['text'] = __('Ai primit bonus %s Gold, %s Energie, %s Evo',1,$max_energy,100);
                $CI->gamer->updateData('gold',1);
                $CI->gamer->updateData('money',200);
                $CI->gamer->setData('energy',$max_energy);
                $level_info['buildings'][] = array(
                    'logo' => 'block_5_floors',
                    'name' => __('Bloc 5 Etaje'),
                );
                $level_info['buildings'][] = array(
                    'logo' => 'school',
                    'name' => __('Scoala'),
                );
                $buildings_arr = array(3,25);
                $CI->building_model->_database->where('user_id', $CI->gamer->getData('user_id'));
                $CI->building_model->_database->where('user_region_id', $CI->gamer->getData('default_region_id'));
                $CI->building_model->_database->where_in('building_id', $buildings_arr);
                $CI->building_model->_database->update('users_regions_buildings',array('opened'=>1));
                break;
        }
        $level_info['new_level'] = $level;
        $data['level_info'] = $level_info;
        $response = array(
            'name' => 'showModal',
            'args' => array(
                'html' => $CI->load->view('layout/levelup',$data,true),
                'options' => array(
                    'title' => __('Nivel Ridicat'),
                    'classes' => array(
                        'box' => 'level_up_box',
                        'prompt' => 'level_up_box_prompt'
                    )
                ),
            )
        );
        $CI->template->addResponse('methods',$response);
    }
}

if(!function_exists('complete_mission'))
{
    function complete_mission($params=array(),$observer=array())
    {
//        var_dump($params);
        if(!empty($params) && isset($params['mission_id']))
        {
            $mission_id = $params['mission_id'];
            if($mission_id)
            {
                $CI =& get_instance();
                $mission = $CI->missions_model->getMission($mission_id);
                if($mission !== false && $mission['status']==0)
                {
                    $allow_complete = true;
                    if(trim($mission['extra_params'])!='')
                    {
                        $extraParams = json_decode($mission['extra_params'],true);
                        if(false !== $extraParams && !empty($extraParams))
                        {
                            foreach($extraParams as $type => $value)
                            {
                                if(!isset($observer[$type]))
                                {
                                    $allow_complete = false;
                                }
                                else
                                {
                                    if($observer[$type] < $value)
                                    {
                                        $allow_complete = false;
                                    }
                                }
                            }
                        }
                    }
                    if($allow_complete)
                    {
                        $update = $CI->missions_model->completeMission($mission_id);
                        if($update!==false)
                        {
                            $CI->missions_model->createNextMission($mission['rule_id'],$mission['category'],getUserId());
                            $prize = json_decode($mission['prize'],true);
                            $text = '';
                            if(isset($prize['money']))
                            {
                                $CI->gamer->updateData('money',$prize['money']);
                                $text = __('Felicitari ai finisat cu succes misiunea. Ai primit bonus %s EVO', $prize['money']);
                            }
                            if(isset($prize['exp']))
                            {
                                formatExp( $prize['exp']);
                                $text = __('Felicitari ai finisat cu succes misiunea. Ai primit bonus %s Experienta', $prize['exp']);
                            }
                            if(isset($prize['money']) && isset($prize['exp']))
                            {
                                $text = __('Felicitari ai finisat cu succes misiunea. Ai primit bonus %s EVO si %s Experienta', $prize['money'],$prize['exp']);
                            }


//                        $CI->user_model->update(getUserId(),$dataUpdate,TRUE);
//
//                    $missions = $this->missions_model->getAllActiveMissions(getUserId(),true);
//                    $data['missions'] = $missions;
//                        $user = getUser();
//
                            $response = array(
                                'id' => 'evo_counter',
                                'actions' => array(
                                    'update' => $CI->gamer->getData('money')
                                )
                            );
                            $CI->template->addResponse('elements',$response);


                            $response = array(
                                'name' => 'showModal',
                                'args' => array(
                                    'html' =>$text,
                                    'options' => array(
                                        'title' => __('Misiune indeplinita'),
                                        'classes' => array(
                                            'box' => 'request_response_error',
                                            'prompt' => 'request_response_error_prompt'
                                        )
                                    ),
                                )
                            );
                            $CI->template->addResponse('methods',$response);

//                    $response['elements'][] = array(
//                        'id' => 'game_tools_left',
//                        'actions' => array(
//                            'update' => $this->template->view('_parts/left_tools_type1',$data,true)
//                        ),
//                    );
                        }
                    }
                }
            }
        }
    }
}

if(!function_exists('consumeEnergy')){
    function consumeEnergy($qty,$msg='')
    {
        if($qty>0)
        {
            $user = getUser();
            if($qty>$user['energy'])
            {
                if(trim($msg)=='')
                {
                    $msg = __('Nu ai suficienta energie!');
                }
                $response['methods'][] = formatErrorResponsePopup($msg);
                header('Content-Type: application/json');
                echo json_encode($response);
                die();
            }
            else
            {
                $restEnergy = ($user['energy']-$qty);
                $CI =& get_instance();
                $response = array(
                    'id' => 'energy_counter',
                    'actions' => array(
                        'update' => $restEnergy
                    )
                );
                $CI->template->addResponse('elements',$response);
                //ToDo: Reset timer
//                $response = array(
//                    'id' => 'energy_counter',
//                    'actions' => array(
//                        'update' => $restEnergy
//                    )
//                );
//                $CI->template->addResponse('methods',$response);
                $CI->user_model->update(getUserId(),array('energy'=>$restEnergy),true);
            }
        }
    }
}

if(!function_exists('collectBonuses')){
    function collectBonuses($json,$msg='')
    {
        if($results = json_decode($json,true))
        {
            if(!empty($results))
            {
//                pr($results);
                $increments = array();
                foreach($results as $key => $result)
                {
                    foreach($result as $type=>$value)
                    {
//                        $increment = 0;
                        switch($key){
                            case 'user':
                                if(!isset($increments['user'][$type]))
                                {
                                    $increments['user'][$type] = $value;
                                }
                                else
                                {
                                    $increments['user'][$type] += $value;
                                }

                                break;
                            case 'region':
                                if(!isset($increments['region'][$type]))
                                {
                                    $increments['region'][$type] = $value;
                                }
                                else
                                {
                                    $increments['region'][$type] += $value;
                                }

                                break;
                        }
                    }
                }
//                pr($increments);
                if(!empty($increments))
                {
                    $CI =& get_instance();

                    if(isset($increments['region']))
                    {
                        $region = $CI->user_model->getUserRegionData(getCurrentRegionId());
                    }

                    $dataRegionUpdate = array();
                    foreach($increments as $key => $result)
                    {
                        foreach($result as $type=>$value)
                        {
                            switch($key){
                                case 'user':
                                    if($value<0 && abs($value)>$CI->gamer->getData($type))
                                    {
                                        if(trim($msg)=='')
                                        {
                                            switch($type)
                                            {
                                                case 'exp':
                                                    $msg = __('Ai nevoie de mai multa experienta!');
                                                    break;
                                                case 'energy':
                                                    $msg = __('Ai nevoie de mai multa energie!');
                                                    break;
                                                case 'money':
                                                    $msg = __('Ai nevoie de mai multi EVO!');
                                                    break;
                                            }

                                        }
                                        $response['methods'][] = formatErrorResponsePopup($msg);
                                        header('Content-Type: application/json');
                                        echo json_encode($response);
                                        die();
                                    }
                                    else
                                    {
                                        if($type == 'exp')
                                        {
                                            formatExp($value);
                                        }
                                        elseif($type == 'energy')
                                        {
                                            if($value<0)
                                            {
                                                consumeEnergy(abs($value));
                                            }
                                            elseif($value>0)
                                            {
                                                $restEnergy = ($CI->gamer->getData('energy')+$value);
                                                $response = array(
                                                    'id' => 'energy_counter',
                                                    'actions' => array(
                                                        'update' => $restEnergy
                                                    )
                                                );
                                                $CI->template->addResponse('elements',$response);
                                            }

                                        }
                                        else
                                        {
                                            $CI->gamer->updateData($type,$value);
                                        }
                                    }
                                    break;
                                case 'region':
                                    if($value<0 && abs($value)>$region[$type])
                                    {
                                        if(trim($msg)=='')
                                        {
                                            switch($type)
                                            {
                                                case 'happiness':
                                                    $msg = __('Ai nevoie de mai multa fericire!');
                                                    break;
                                                case 'education':
                                                    $msg = __('Ai nevoie de mai multa educatie!');
                                                    break;
                                                case 'culture':
                                                    $msg = __('Ai nevoie de mai multa cultura!');
                                                    break;
                                            }
                                        }
                                        $response['methods'][] = formatErrorResponsePopup($msg);
                                        header('Content-Type: application/json');
                                        echo json_encode($response);
                                        die();
                                    }
                                    else
                                    {
                                        $resultQty = $region[$type] + $value;
                                        $response = array(
                                            'id' => $type.'_region_counter',
                                            'actions' => array(
                                                'update' => $resultQty
                                            )
                                        );

                                        $CI->template->addResponse('elements',$response);
                                        $dataRegionUpdate[$type] = $resultQty;
                                    }
                                    break;
                            }
                        }
                    }
//                    if(!empty($dataUserUpdate))
//                        $CI->user_model->update(getUserId(),$dataUserUpdate,true);
//                    pr($dataRegionUpdate);
//                    die();
                    if(!empty($dataRegionUpdate))
                    {
                        $CI->db->where('user_region_id',getCurrentRegionId());
                        $CI->db->where('user_id',getUserId());
                        $CI->db->update('users_regions',$dataRegionUpdate);
                    }
                }
            }
//            $user = getUser();

//            else
//            {
//                $restEnergy = ($user['energy']-$qty);
//                $CI =& get_instance();
//                $response = array(
//                    'id' => 'energy_counter',
//                    'actions' => array(
//                        'update' => $restEnergy
//                    )
//                );
//                $CI->template->addResponse('elements',$response);

//                $CI->user_model->update(getUserId(),array('energy'=>$restEnergy),true);
//            }
        }
    }
}

if(!function_exists('getAbility'))
{
    function getAbility($type,$regionId = 0)
    {
        if($regionId == 0)
        {
            $regionId = getCurrentRegionId();
        }
        $CI =& get_instance();
        $data = array();
        $userId = getUserId();
        $ability = $CI->user_model->getAbility($userId,$type,$regionId);
        if($ability===false)
        {
            $ability = $CI->user_model->addAbility($userId,$type,$regionId);
        }
        if($ability == false)
        {
            return '';
        }
//        $ability allow_upgrade
        $raw_materials = json_decode($ability['raw_materials'],true);
        $products_needed = $CI->user_model->getProductsIncludeDeposit($userId,$regionId,array_keys($raw_materials));
        $ability['allow_upgrade'] = (!$ability['upgrade_started'])?true:false;
        $user = getUser();
        $productsNeed = array();
        foreach($raw_materials as $productId => $qtyNeed)
        {
            if($productId=='money')
            {
                $productsNeed[$productId] = array(
                    'product_logo' => 'money',
                    'product_name' => __('Bani'),
                    'deposit_qty' => $user['money'],
                );
                $productsNeed[$productId]['qty_need'] = $qtyNeed;
                if($productsNeed[$productId]['deposit_qty']<$qtyNeed)
                {
                    $ability['allow_upgrade'] = false;
                }
            }
            else
            {
                $productsNeed[$productId] = $products_needed[$productId];
                $productsNeed[$productId]['qty_need'] = $qtyNeed;
                if($productsNeed[$productId]['deposit_qty']<$qtyNeed)
                {
                    $ability['allow_upgrade'] = false;
                }
            }

        }
        $ability['level_up_needs'] = $productsNeed;
        $ability['post_data'] = array(
            md5('user_ability_id') => encrypt($ability['user_ability_id'],true)
        );
        $data['ability'] = $ability;
        return $CI->load->view('city/ability_item',$data,true);
    }
}

if(!function_exists('setEnergy'))
{
    function setEnergy($Exp){
        $userId = getUserId();
        $CI = & get_instance();
        $CI->user_model->_database->query("UPDATE `users` SET `energy`=`energy`+'".$Exp."' WHERE `user_id`='".$userId."'");

    }
}
if(!function_exists('getEnergy'))
{
    function getEnergy(){
        $userId = getUserId();
        $CI = & get_instance();
        return $CI->user_model->_database->select('energy')->from('users')->where('user_id',$userId)->get()->row()->energy;
    }
}


if(!function_exists('getStars'))
{
    function getStars(){
        $userId = getUserId();
        $CI = & get_instance();
        return $CI->user_model->_database->select('money')->from('users')->where('user_id',$userId)->get()->row()->money;
    }
}
if(!function_exists('setExp'))
{
    function setExp($Exp){
        $userId = getUserId();
        $CI = & get_instance();
        $CI->user_model->_database->query("UPDATE `users` SET `have_exp`=`have_exp`+'".$Exp."' WHERE `user_id`='".$userId."'");

    }
}

if(!function_exists('countMedals'))
{
    function countMedals(){
        $userId = getUserId();
        $CI = & get_instance();
        $CI->user_model->_database->select("medals")->from('users')->where('user_id',$userId);

        return $CI->user_model->_database->get()->row()->medals;
    }
}

if(!function_exists('sendToDeposit'))
{
    function sendToDeposit($type,$count = 1, $item=0){
        $userId = getUserId();
        $CI = & get_instance();
        $CI->user_model->_database->query("INSERT INTO `deposit`(`userId`,`count`,`type`,`itemId`)VALUES('$userId','$count','$type','$item')");
    }
}

if(!function_exists('setStars'))
{
    function setStars($int){
        $userId = getUserId();
        $CI = & get_instance();
        $CI->user_model->_database->query("UPDATE `users` SET `money`=`money`+'".$int."' WHERE `user_id`='".$userId."'");
    }
}

if(!function_exists('setMedal'))
{
    function setMedal($int){
        $userId = getUserId();
        $CI = & get_instance();
        $CI->user_model->_database->query("UPDATE `users` SET `medals`=`medals`+'".$int."' WHERE `user_id`='".$userId."'");

    }
}


if(!function_exists('isVip'))
{
    function isVip(){
        $userId = getUserId();
        $CI = & get_instance();
        return $CI->user_model->_database->select('vip')->from('users')->where('user_id',$userId)->get()->row()->vip;
    }
}