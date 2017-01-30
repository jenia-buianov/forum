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
        $this->_CI = &get_instance();


        $this->addItem('css', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese');
        $this->addItem('css', 'https://fonts.googleapis.com/css?family=Roboto');
        $this->addItem('css', base_url() . 'assets/bootstrap/css/bootstrap.css');
        $this->addItem('css', base_url() . 'assets/font-awesome/css/font-awesome.css');
        $this->addItem('css', base_url() . 'assets/stylesheet.css');
        $this->addItem('css', base_url() . 'assets/font-awesome/css/font-awesome.css');
        $this->addItem('css', base_url() . 'assets/font-awesome/css/font-awesome.css');
        $this->addItem('js', base_url() . 'assets/jquery-3.1.1.min.js');
        $this->addItem('js', base_url() . 'assets/bootstrap/js/bootstrap.js');
        $this->addItem('js', base_url() . 'assets/lang_'.getLang().'.js');
        $this->addItem('js', base_url() . 'assets/core.js');
        $this->addItem('js', base_url() . 'assets/custom.js');
        $this->addItem('js', base_url() . 'assets/jquery.countdown.min.js');
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

        $data = array_merge($this->_templateData,$data);

        if (empty($_POST)){
            $this->_CI->load->model('Settings_Model','sm');
            $this->_CI->load->model('Menu_Model','mm');
            $this->_CI->load->model('Banner_Model','bm');
            $settings_temp = $this->_CI->sm->get();
            $this->_CI->sm->logs();

            foreach ($settings_temp as $k=>$v){
                $data['settings'][$v->key] = $v->value;
            }
            $data['menu'] = $this->_CI->mm->getAllMenu();
            $data['banners'] = $this->_CI->bm->getAllBanners();
            $data['breadcrumbs'] = breadcrumbs();
            $data['page']['title'] = getPageTitle($data['breadcrumbs'],$data['settings']['title']);
            $this->_CI->load->view('template/header',$data);
            $this->view($this->_folder.'/'.$view, $data);
            $this->_CI->load->view('template/footer',$data);
        }else{
            $this->view($this->_folder.'/'.$view, $data);
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
