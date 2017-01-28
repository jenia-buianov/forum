<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template
{
    private $_CI = false;
    private $_folder = '';
    private $_toolsType = array();
    private $_items = array();
    private $_user = false;
    private $_templateData = array();
    private $_response = array();

    public function __construct()
    {
        $this->_CI = & get_instance();


        $this->addItem('js',base_url().'assets/js/functions.js');
        $this->addItem('js',base_url().'assets/js/library/uploader/croppic.js');
        //$this->addItem('js',base_url().'assets/js/library/JqueryNoConflict.js');
        $this->addItem('css',base_url().'assets/js/library/tinyscrollbar/tinyscrollbar.css');
        $this->addItem('css',base_url().'assets/styles/jquery-impromptu_NE.css');
//        $this->addItem('js',base_url().'assets/js/library/impromptu/jquery-impromptu.min.js');
        $this->addItem('js',base_url().'assets/js/library/impromptu/jquery-impromptu.js');
        $this->addItem('js',base_url().'assets/js/library/draggabilly.pkgd.min.js');

        $this->addItem('js',base_url().'assets/js/library/dragdealer/dragdealer.js');
        $this->addItem('css',base_url().'assets/js/library/dragdealer/dragdealer.css');
        $this->addItem('css',base_url().'assets/js/library/modal/animate.min.css');
        $this->addItem('css',base_url().'assets/js/library/modal/normalize.min.css');
        $this->addItem('js',base_url().'assets/js/library/modal/AnimatedModal.js');

        $this->addItem('css',base_url().'assets/js/library/uploader/croppic.css');
//        $this->addItem('css',base_url().'assets/styles/city_detail.css');
        $this->addItem('css',base_url().'assets/styles/template.css?v='.time());
        $this->addItem('css',base_url().'assets/styles/responsive.css');
//   Бутстрап, удалить после
       //$this->addItem('css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');

        /* ToolTip */
        $this->addItem('css',base_url().'assets/js/library/tooltip/opentip.css');
        $this->addItem('js',base_url().'assets/js/library/tooltip/opentip-prototype.js');
        $this->addItem('js',base_url().'assets/js/library/socket.io/socket.io.js');
        $this->addItem('js',base_url().'assets/js/chat.js');
        $this->addItem('css',base_url().'assets/styles/top-bar.css');

    }

    public function addResponse($key,$data)
    {
        $this->_response[$key][] = $data;
        return $this;
    }

    public function setFolder($str)
    {
        $this->_folder = $str;
        return $this;
    }

    public function setToolsTypes($types){
        if(count($types)>0)
        {
            $this->_toolsType = array_merge($this->_toolsType,$types);
        }
        return $this;
    }

    public function addItem($type,$link)
    {
        if(!isset($this->_items[$type]))
        {
            $this->_items[$type] = array();
        }
        if(!in_array($link,$this->_items[$type]))
        {
            $this->_items[$type][] = $link;
        }
        return $this;
    }

    private function _renderJs()
    {
        $html = '';
        if(!empty($this->_items['js']))
            foreach($this->_items['js'] as $link)
            {
                $html .= '<script src="';
                $html .= $link;
                $html .= '"></script>';
            }
        $html .= '';

        return $html;
    }

    private function _renderCss()
    {
        $html = '';
        if(!empty($this->_items['css']))
            foreach($this->_items['css'] as $link)
            {
                $html .= '<link rel="stylesheet" type="text/css" href="';
                $html .= $link;
                $html .= '" />';
            }
        $html .= '';

        return $html;
    }
    public function setData($key,$value)
    {
        $this->_templateData[$key] = $value;
        return $this;
    }
    public function getData($key)
    {
        if(isset($this->_templateData[$key]))
        {
            return $this->_templateData[$key];
        }
        return null;
    }

    public function view($view, $vars = array(), $return = FALSE)
    {
        $vars = array_merge($this->_templateData,$vars);
        return $this->_CI->load->view($view, $vars,$return);
    }

    public function render($view = '',$data=array(), $return = FALSE)
    {
        if($view == '')
        {
            $view = strtolower($this->_CI->router->fetch_method());
        }


        $this->_templateData['included_css'] = $this->_renderCss();
        $this->_templateData['included_js'] = $this->_renderJs();
        $data['user'] = $this->_user;
//        pr($this->_user);

        $data = array_merge($this->_templateData,$data);

        //$this->_templateData['other_tool'] = $this->_CI->load->view('_parts/other_tool',$data,true);
        $this->_templateData['energy_block'] = $this->_CI->load->view('_parts/energy_block',$data,true);
        //$this->_templateData['gold_block'] = $this->_CI->load->view('_parts/gold_block',$data,true);
        $data['campaigns'] = $this->_campaings;

        $this->_templateData['campaigns_block'] = '<div id="campaignId"> </div><div id="campaigns_block">'.$this->_CI->load->view('_parts/campaigns_block',$this->_campaings,true).'</div>';


        $data = array_merge($this->_templateData,$data);

        $this->_templateData['tools_top'] = $this->_CI->load->view('_parts/top_tools_type'.$this->_toolsType['top'],$data,true);
        $this->_templateData['tools_left'] = $this->_CI->load->view('_parts/left_tools_type'.$this->_toolsType['left'],$data,true);
        //$this->_CI->load->model('user_model', 'um');
        $data['robots'] = $this->_CI->um->getRobots();
        $data['cards'] = $this->_CI->um->getCards();
        $this->_templateData['tools_bottom'] = $this->_CI->load->view('_parts/bottom_tools_type'.$this->_toolsType['bottom'],$data,true);

        if($this->_folder != 'city')
        {
            $this->_CI->session->unset_userdata('region_id');
        }
        $data = array_merge($this->_templateData,$data);
        if(!isAjax())
        {
            $this->_CI->load->view('layout/html/head',$data);
            $this->view($this->_folder.'/'.$view, $data);
//            $this->_CI->load->view($this->_folder.'/'.$view,$data);
            $this->_CI->load->view('layout/html/footer',$data);
        }
        else
        {
            $response = (isset($data['return']))?$data['return']:array();
            $response['html'] = $this->view($this->_folder.'/'.$view,$data,TRUE);
            echo json_encode($response);
        }

    }

    public function renderJson($response)
    {
        if($this->_CI->gamer->isUpdated('money'))
        {
            $minR = array(
                'id' => 'evo_counter',
                'actions' => array(
                    'update' => $this->_CI->gamer->getData('money')
                )
            );
            $this->addResponse('elements',$minR);
        }
        if($this->_CI->gamer->isUpdated('gold'))
        {
            $minR = array(
                'id' => 'gold_counter',
                'actions' => array(
                    'update' => $this->_CI->gamer->getData('gold')
                )
            );
            $this->addResponse('elements',$minR);
        }
        if($this->_CI->gamer->isUpdated('energy'))
        {
            $minR = array(
                'id' => 'energy_counter',
                'actions' => array(
                    'update' => $this->_CI->gamer->getData('energy')
                )
            );
            $this->addResponse('elements',$minR);
        }
        if($this->_CI->gamer->isUpdated('life_humans'))
        {
            $minR = array(
                'id' => 'life_humans_counter',
                'actions' => array(
                    'update' => $this->_CI->gamer->getData('life_humans')
                )
            );
            $this->addResponse('elements',$minR);
        }

        if($this->_CI->gamer->isExpUpdate())
        {
            $minR = array(
                'id' => 'level_number',
                'actions' => array(
                    'update' => $this->_CI->gamer->getData('level')
                )
            );
            $this->addResponse('elements',$minR);
            $minR = array(
                'id' => 'level_left_number',
                'actions' => array(
                    'update' => $this->_CI->gamer->getData('need_exp')-$this->_CI->gamer->getData('have_exp')
                )
            );
            $this->addResponse('elements',$minR);
            $minR = array(
                'id' => 'level_left_progress_percent',
                'actions' => array(
                    'setStyle' => array('width'=>(int)($this->_CI->gamer->getData('have_exp')/$this->_CI->gamer->getData('need_exp')*100).'%')
                )
            );
            $this->addResponse('elements',$minR);
        }


        if(!empty($this->_response))
        {
            if(isset($this->_response['elements']))
            {
                $response['elements'] = array_merge(((isset($response['elements']))? $response['elements'] : array()),$this->_response['elements']);
            }
            if(isset($this->_response['methods']))
            {
                $response['methods'] = array_merge(((isset($response['methods']))? $response['methods'] : array()),$this->_response['methods']);
            }
        }
//        $CI =& get_instance();
        $missions = $this->_CI->missions_model->getAllActiveMissions(getUserId());
        $data['missions'] = $missions;
        $response['elements'][] = array(
            'id' => 'game_tools_left',
            'actions' => array(
                'update' => $this->view('_parts/left_tools_type1',$data,true)
            ),
        );
        if($missions!==false && !empty($missions))
        {

            //Todo: need to make this as bellow
//            foreach($missions as $category => $mission)
//            $data['missions'] = $missions;
//            $response['elements'][] = array(
//                'id' => 'mission_people_'.$category,
//                'actions' => array(
//                    'addClassName' => ''
//                ),
//            );
            foreach($missions as $mission)
            {

                if($mission['type'] == "js")
                {
//                   var_dump($mission['type']);
                    $script_html = '<script>';
                    $script_html .= 'varienGlobalEvents.clearEventHandlers(\''.$mission['event'].'\');';
                    $script_html .= 'varienGlobalEvents.attachEventHandler(\''.$mission['event'].'\', function(observer){';
                    $script_html .= 'GAMEOBJ.completeMission(observer,\''.encrypt($mission['mission_id'],TRUE).'\');';
//                   $script_html .= 'GAMEOBJ.completeMission(observer,\''.$mission['mission_id'].'\');';
                    $script_html .= '});';
                    $script_html .= '</script>';

                    $response['elements'][]= array(
                        "id" => 'missions_script',
                        'actions' => array(
                            'update' => $script_html
//                           'update' => 'test'
                        )
                    );
                }
            }
        }

        $this->_CI->gamer->save();
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
