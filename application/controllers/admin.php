<?php
/*
Author: Denis Bujnita
Date: 9/18/12
Version: 1.0
*/

class Admin extends MY_Controller{

    private function admin_url()
    {
        return $GLOBALS['CFG']->config['base_url'].'admin/';
    }

    public function __construct()
    {
        parent::__construct();

		if (empty($this->session->userdata("admin_enter")) and empty($this->input->post('pass'))) {
            $this->render('home');
            return;
        }
    }
	public function index()
	{
		if (!empty($this->session->userdata("admin_enter")))
		{
			$this->load->model("admin_model", "am");
			$this->render('index');
			
		}
	}

	public function search(){
        $query = htmlspecialchars($_GET['q'],ENT_QUOTES);
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");

        $data['editArray'] = array('questions'=>'question','themes'=>'theme','users'=>'user','campaigns'=>'campaign');
        $data['search']['questions'] = $this->am->searchQuestions($query);
        $data['search']['themes'] = $this->am->searchThemes($query);
        //$data['search']['users'] = $this->am->searchUsers($query);
        $data['search']['campaigns'] = $this->am->searchCampaigns($query);

        $this->render('search',$data);

    }

    public function deletecampaign($id)
    {
        $id = htmlspecialchars($id,ENT_QUOTES);
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        if(!empty($this->input->post('post'))){
            $this->db->query("DELETE FROM `campaigns` WHERE `id`='".$id."'");
            $this->db->query("DELETE FROM `campaigns_lang` WHERE `campaignId`='".$id."'");
            echo 'Deleted';
        }

    }
	public function editcampaign($id){
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        if(empty($this->input->post('values'))) {
            $data['questions'] = $this->am->getQuestions();
            $data['steps'] = $this->am->getStepsAndQuestions($id);
            $data['hints'] = $this->am->getStepsHints($data['steps']);
            $data['campaign'] = $this->am->getCamapaignTitles($id);
            $data['id'] = $id;
            $this->render('campaign_form',$data);
            return 0;
        }
        $date = array('html'=>'','error'=>'');
        $data = $this->makeData(array('title_en','title_ru','title_ro'),array('title_en'=>'en','title_ru'=>'ru','title_ro'=>'ro','title_es'=>'es'),$this->input->post('values'),$date);


        if (empty($date['error'])) {
            $langs = array('en', 'ro','ru');
            $campID = $id;
            $this->db->select('steps');
            $this->db->from('campaigns');
            $this->db->where('id',$id);
            $stepsID = $this->db->get()->row();
            $stepsID = explode(',',$stepsID->steps);
            $countSteps = count($stepsID);

            for($i=0;$i<count($langs);$i++){
                $this->db->query("UPDATE `campaign_lang` SET `title`='".$data[$langs[$i]]."' WHERE `campaignId`='".$campID."' AND `lang`='".$langs[$i]."'");
            }
            if (count($stepsID)>$data['steps']) $stepsID = array();

            for($i=0;$i<$data['steps'];$i++){
                $k=0;
                $questionTaskA = array();
                $questionTaskB = array();
                $questionTaskC = array();
                while(isset($data['step_'.$i.'_'.$k])&&!empty($data['step_'.$i.'_'.$k])){
                    $id_ = (int) $data['step_'.$i.'_'.$k];
                    $type = $this->am->getQuestionType($id_);
                    if ($type['type']=='selectText') $questionTaskA[] = $id_;
                    if ($type['type']=='selectImage') $questionTaskC[] = $id_;
                    if ($type['type']=='enterAnswer') $questionTaskB[] = $id_;

                    $k++;
                }
                $stringTaskA = implode(',',$questionTaskA);
                $stringTaskB = implode(',',$questionTaskB);
                $stringTaskC = implode(',',$questionTaskC);

                $tasks = array();
                if (!empty($stringTaskA)) $tasks[] = 'taskA';
                if (!empty($stringTaskB)) $tasks[] = 'taskB';
                if (!empty($stringTaskC)) $tasks[] = 'taskC';
                $tasks = implode(',',$tasks);

                if ($i<count($stepsID)) $this->db->query("UPDATE `steps` SET `tasks`='".$tasks."',`taskA`='".$stringTaskA."',`taskB`='".$stringTaskB."',`taskC`='".$stringTaskC."' WHERE `id`='".$stepsID[$i]."'");
                else {
                    $this->db->insert('steps',array('tasks'=>$tasks,'taskA'=>$stringTaskA,'taskB'=>$stringTaskB,'taskC'=>$stringTaskC));
                    $stepsID[] = $this->db->insert_id();
                }


            }

            for($k=0;$k<count($stepsID);$k++) {
                for ($i = 0; $i < count($langs); $i++) {
                    if($k<$countSteps) $this->db->query("UPDATE `stepsHint` SET `text`='".$data['hint'.$k.'_'.$langs[$i]]."' WHERE `stepId`='".$stepsID[$k]."' AND `lang`='".$langs[$i]."'");
                    else  $this->db->insert('stepsHint',array('stepId'=>$stepsID[$k],'lang'=>$langs[$i],'text'=>$data['hint'.$k.'_'.$langs[$i]]));
                }
            }
            $stringSteps = implode(',',$stepsID);
            $this->db->query("UPDATE `campaigns` SET `steps`='".$stringSteps."' WHERE `id`='".$campID."'");

        }
        $date['html']= 'Succesfull edited';

        echo json_encode($date);
    }

	public function addcampaign(){
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        if(empty($this->input->post('values'))) {
            $data['questions'] = $this->am->getQuestions();
            $this->render('campaign_form',$data);
            return 0;
        }
        $date = array('html'=>'','error'=>'');
        $data = $this->makeData(array('title_en','title_ru','title_ro'),array('title_en'=>'en','title_ru'=>'ru','title_ro'=>'ro','title_es'=>'es'),$this->input->post('values'),$date);

        if (empty($date['error'])) {
            $langs = array('en', 'ro','ru');
            $this->db->insert('campaigns', array('status' => 1,'name'=>$data['en']));
            $campID = $this->db->insert_id();
            $stepsID = array();

             for($i=0;$i<count($langs);$i++){
                 $this->db->insert('campaign_lang', array('campaignId' => $campID, 'lang'=>$langs[$i], 'title'=> $data[$langs[$i]]));
             }

             for($i=0;$i<$data['steps'];$i++){
                 $k=0;
                 $questionTaskA = array();
                 $questionTaskB = array();
                 $questionTaskC = array();
                 while(isset($data['step_'.$i.'_'.$k])&&!empty($data['step_'.$i.'_'.$k])){
                     $id_ = (int) $data['step_'.$i.'_'.$k];
                     $type = $this->am->getQuestionType($id_);
                     if ($type['type']=='selectText') $questionTaskA[] = $id_;
                     if ($type['type']=='selectImage') $questionTaskC[] = $id_;
                     if ($type['type']=='enterAnswer') $questionTaskB[] = $id_;

                     $k++;
                 }
                 $stringTaskA = implode(',',$questionTaskA);
                 $stringTaskB = implode(',',$questionTaskB);
                 $stringTaskC = implode(',',$questionTaskC);

                 $tasks = array();
                 if (!empty($stringTaskA)) $tasks[] = 'taskA';
                 if (!empty($stringTaskB)) $tasks[] = 'taskB';
                 if (!empty($stringTaskC)) $tasks[] = 'taskC';
                 $tasks = implode(',',$tasks);

                 $this->db->insert('steps',array('tasks'=>$tasks,'taskA'=>$stringTaskA,'taskB'=>$stringTaskB,'taskC'=>$stringTaskC));
                 $stepsID[] = $this->db->insert_id();

             }

             for($k=0;$k<count($stepsID);$k++) {
                 for ($i = 0; $i < count($langs); $i++) {
                     $this->db->insert('stepsHint',array('stepId'=>$stepsID[$k],'lang'=>$langs[$i],'text'=>$data['hint'.$k.'_'.$langs[$i]]));
                 }
             }
             $stringSteps = implode(',',$stepsID);
             $this->db->query("UPDATE `campaigns` SET `steps`='".$stringSteps."' WHERE `id`='".$campID."'");

        }
        $date['html']= 'Succesfull added';
        echo json_encode($date);

    }

	public function uploadphoto($lang)
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        if (empty($_FILES['photo'])){
            $this->template->view('admin/upload_photo');
        }else{
            $ph = '';
            $filetypes = array('image/png','image/jpg','image/gif','image/jpeg');
            for($i=0;$i<3;$i++){
                if (!empty($_FILES["photo"]["tmp_name"][$i])&&in_array($_FILES["photo"]["type"][$i],$filetypes)){
                    $tmp_name = $_FILES["photo"]["tmp_name"][$i];
                    $expl = explode('/',$_FILES["photo"]["type"][$i]);
                    $name = time().'_'.$i.'.'.$expl[1];
                    move_uploaded_file($tmp_name, dirname(__FILE__)."/../../uploads/$name");
                    if(empty($ph)) $ph = base_url().'uploads/'.$name; else $ph.=','.base_url().'uploads/'.$name;
                }
            }
            $this->session->set_userdata(array('photo'.$lang=>$ph));
            $p = explode(',',$ph);
            for($i=0;$i<count($p);$i++)
                echo '<img src="'.$p[$i].'" width=100 style="margin-right:20px">';
        }
    }

    public function deletequestion($id)
    {
        $id = htmlspecialchars($id,ENT_QUOTES);
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        if(!empty($this->input->post('post'))){
            $this->db->query("DELETE FROM `questions` WHERE `id`='".$id."'");
            $this->db->query("DELETE FROM `questions_lang` WHERE `questionsId`='".$id."'");
            $this->db->query("DELETE FROM `answers` WHERE `questionsId`='".$id."'");
            echo 'Deleted';
        }

    }

    public function editquestion($id)
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        if(empty($this->input->post('values'))) {
            $data['id'] = $id;
            $data['themes'] = $this->am->getThemes();
            $campaigns = $this->am->getAllCampaigns();
            $data['questions'] = $this->am->getQuestion($id);
            $data['answers'] = $this->am->getAnswers($id);
            $correct = $this->am->getCorrect($id);
            for($i=0;$i<count($correct);$i++)
            {
                if ($correct[$i]['correct']==1) $data['correct'] = $i+1;
            }
            $Types = array('selectText'=>'taskA','enterAnswer'=>'taskB','selectImage'=>'taskC');

            $type = $Types[$data['questions'][0]['type']];
            $data['steps'] = $this->am->getStepsQuestion($id,$type);

            for($i=0;$i<count($campaigns);$i++){
                $data['campaigns'][$i]['title'] = $campaigns[$i]->name;
                $data['campaigns'][$i]['steps'] = $campaigns[$i]->steps;
            }
            $this->render('questions_form', $data);
            return 0;
        }

        $date = array('html'=>'','error'=>'');
        $data = $this->makeData(array('type','theme','title_en','title_ru','title_ro'),array('title_en'=>'en','title_ru'=>'ru','title_ro'=>'ro','title_es'=>'es'),$this->input->post('values'),$date);

        if (empty($date['error'])) {

            $this->db->query("UPDATE `questions` SET `themeId`='".$data['theme']."' WHERE `id`='".$id."'");

            $langs = array('en', 'ro','ru');
            $arrAID = array();

            if ($data['type'] == 'enterAnswer') {
                for ($i = 0; $i < count($langs); $i++) {

                    $this->db->query("UPDATE `questions_lang` SET `title`='".$data[$langs[$i]]."' WHERE `questionId`='".$id."' AND `lang`='".$langs[$i]."'");
                    $this->db->select('id');
                    $this->db->from('answers');
                    $this->db->where('questionId',$id);
                    $aId = $this->db->get()->row();
                    $this->db->query("UPDATE `answers_lang` SET `text`='".$data['res_'.$langs[$i]]."' WHERE (`answerId`='".$aId->id."' OR `answerId`='".($aId->id+1)."') AND `lang`='".$langs[$i]."'");
                }
            }


            if ($data['type'] !== 'enterAnswer') {
                $this->db->select('id,correct');
                $this->db->from('answers');
                $this->db->where('questionId',$id);
                $aId = $this->db->get()->result_array();
                $answerIDS = array();
                for($i=0;$i<count($aId);$i++)
                  $answerIDS[] = $aId[$i]['id'];

                $answerIDS = implode(',',$answerIDS);

                for ($i = 0; $i < count($langs); $i++) {
                    $this->db->query("UPDATE `questions_lang` SET `title`='".$data[$langs[$i]]."' WHERE `questionId`='".$id."' AND `lang`='".$langs[$i]."'");
                    for ($k = 0; $k < 3; $k++) {
                        if ($data['correct'] == $k + 1) $cor = 1; else $cor = 0;
                        $this->db->query("UPDATE `answers` SET `correct`='".$cor."' WHERE `id`='".$aId[$k]['id']."'");
                        $this->db->query("UPDATE `answers_lang` SET `text`='".$data['res_' . $langs[$i] . '_' . $k]."' WHERE `answerId` = '".$aId[$k]['id']."' AND `lang`='".$langs[$i]."'");
                    }
                }

            }

            $Types = array('selectText' => 'taskA', 'enterAnswer' => 'taskB', 'selectImage' => 'taskC');
            $type = $Types[$data['type']];
            $steps = $this->am->getStepsQuestion($id,$type);
            for($i=0;$i<count($steps);$i++){
                $currentStep = $this->am->getStep($steps[$i]->id,$type);
                $currentStep = explode(',',$currentStep[0]['t']);
                $found = array_keys($currentStep, $id);
                for($k=0;$k<count($found);$k++)
                    unset($currentStep[$found[$k]]);

                $string_steps = implode(',',$currentStep);
                $this->db->query("UPDATE `steps` SET `".$type."`='".$string_steps."' WHERE `id`='".$steps[$i]->id."'");
            }


            if (!empty($data['campaign'])) {
                $this->db->select($type);
                $this->db->from('steps');
                $this->db->where('id', $data['campaign']);
                $row = $this->db->get()->row_array();
                if (!empty($row[$type])) $this->db->query("UPDATE `steps` SET `" . $type . "`=CONCAT(" . $type . ",'," . $id . "') WHERE `id`='$data[campaign]'");
                else $this->db->query("UPDATE `steps` SET `" . $type . "`='" . $id . "' WHERE `id`='$data[campaign]'");
                $i = 1;
                while (!empty($data['campaign' . $i])) {
                    $this->db->select($type);
                    $this->db->from('steps');
                    $this->db->where('id', $data['campaign' . $i]);
                    $row = $this->db->get()->row_array();
                    if (!empty($row[$type])) $this->db->query("UPDATE `steps` SET `" . $type . "`=CONCAT(" . $type . ",'," . $id . "') WHERE `id`='" . $data['campaign' . $i] . "'");
                    else $this->db->query("UPDATE `steps` SET `" . $type . "`='" . $id . "' WHERE `id`='" . $data['campaign' . $i] . "'");

                    $i++;
                }
            }

           $date['html'] = 'Updated with success';
            echo json_encode($date);
        }

    }

    public function addquestion()
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        if(empty($this->input->post('values'))) {
            $data['themes'] = $this->am->getThemes();
            $campaigns = $this->am->getAllCampaigns();
           // print_r($campaigns);
            for($i=0;$i<count($campaigns);$i++){
                $data['campaigns'][$i]['title'] = $campaigns[$i]->name;
                $data['campaigns'][$i]['steps'] = $campaigns[$i]->steps;
            }
            $this->render('questions_form', $data);
            return 0;
        }
        $date = array('html'=>'','error'=>'');
        $data = $this->makeData(array('type','theme','title_en','title_ru','title_ro'),array('title_en'=>'en','title_ru'=>'ru','title_ro'=>'ro','title_es'=>'es'),$this->input->post('values'),$date);

        if (empty($date['error'])) {
            $this->db->insert('questions', array('themeId' => $data['theme'], 'activ' => 1));
            $questID = $this->db->insert_id();

            $langs = array('en', 'ro','ru');
            $arrAID = array();


            if ($data['type'] == 'enterAnswer') {

                for ($i = 0; $i < count($langs); $i++) {

                    $this->db->insert('questions_lang', array('questionId' => $questID, 'title' => $data[$langs[$i]], 'lang' => $langs[$i], 'questionType' => $data['type']));
                    $this->db->insert('answers', array('questionId' => $questID, 'correct' => 1));
                    $aID = $this->db->insert_id();
                    $this->db->insert('answers_lang', array('answerId' => $aID, 'lang' => $langs[$i], 'answerType' => $data['type'], 'text' => $data['correct_' . $langs[$i]]));
                }
            }
            if ($data['type'] == 'selectImage') {
                for ($i = 0; $i < count($langs); $i++) {
                    $this->db->insert('questions_lang', array('questionId' => $questID, 'title' => $data[$langs[$i]], 'lang' => $langs[$i], 'questionType' => $data['type']));
                    $images = explode(',', $this->session->userdata('photo' . $langs[$i]));
                    for ($k = 0; $k < 3; $k++) {
                        if (empty($arrAID[$k])) {
                            if ($data['correct'] == $k + 1) $cor = 1; else $cor = 0;
                            $this->db->insert('answers', array('questionId' => $questID, 'correct' => $cor));
                            $arrAID[$k] = $this->db->insert_id();
                        }
                        $data['res_' . $langs[$i] . '_' . $k] = $images[$k];
                        $this->db->insert('answers_lang', array('answerId' => $arrAID[$k], 'lang' => $langs[$i], 'answerType' => $data['type'], 'text' => $data['res_' . $langs[$i] . '_' . $k]));
                        $date['html'].=$this->db->last_query().'<br>';
                    }
                }
            }

            if ($data['type'] == 'selectText') {
                for ($i = 0; $i < count($langs); $i++) {
                    $this->db->insert('questions_lang', array('questionId' => $questID, 'title' => $data[$langs[$i]], 'lang' => $langs[$i], 'questionType' => $data['type']));
                    for ($k = 0; $k < 3; $k++) {
                        if (empty($arrAID[$k])) {
                            if ($data['correct'] == $k+ 1) $cor = 1; else $cor = 0;
                            $this->db->insert('answers', array('questionId' => $questID, 'correct' => $cor));
                            $arrAID[$k] = $this->db->insert_id();
                        }
                        $this->db->insert('answers_lang', array('answerId' => $arrAID[$k], 'lang' => $langs[$i], 'answerType' => $data['type'], 'text' => $data['res_' . $langs[$i] . '_' . $k]));
                    }
                }
            }

           $Types = array('selectText' => 'taskA', 'enterAnswer' => 'taskB', 'selectImage' => 'taskC');
            $type = $Types[$data['type']];
            if (!empty($data['campaign'])) {
                $this->db->select($type);
                $this->db->from('steps');
                $this->db->where('id', $data['campaign']);
                $row = $this->db->get()->row_array();
                if (!empty($row[$type])) $this->db->query("UPDATE `steps` SET `" . $type . "`=CONCAT(" . $type . ",'," . $questID . "') WHERE `id`='$data[campaign]'");
                else $this->db->query("UPDATE `steps` SET `" . $type . "`='" . $questID . "' WHERE `id`='$data[campaign]'");
                $i = 1;
                while (!empty($data['campaign' . $i])) {
                    $this->db->select($type);
                    $this->db->from('steps');
                    $this->db->where('id', $data['campaign' . $i]);
                    $row = $this->db->get()->row_array();
                    if (!empty($row[$type])) $this->db->query("UPDATE `steps` SET `" . $type . "`=CONCAT(" . $type . ",'," . $questID . "') WHERE `id`='" . $data['campaign' . $i] . "'");
                    else $this->db->query("UPDATE `steps` SET `" . $type . "`='" . $questID . "' WHERE `id`='" . $data['campaign' . $i] . "'");

                    $i++;
                }
            }

        }
        $date['html'] = 'Added with success';
        echo json_encode($date);

    }

    public function campaings()
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        $data['campaigns'] = $this->am->getCampaigns();
        $this->render('campaigns',$data);
    }
    public function questions()
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        $data['questions'] = $this->am->getQuestions();
        $this->render('questions',$data);
    }

	public function themes()
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        $this->load->model("admin_model", "am");
        $data['themes'] = $this->am->getThemes();
        $this->render('themes',$data);
    }

    public function edit_theme($id)
    {
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        if(empty($this->input->post('values'))){
            $this->load->model("admin_model", "am");
            $data['themes'] = $this->am->getTheme($id);
            $data['id'] = $id;
            $this->render('theme_form',$data);
            return 0;
        }

        $date = array('html'=>'','error'=>'');
        $data = $this->makeData(array('title_en','title_ru','title_ro'),array('title_en'=>'en','title_ru'=>'ru','title_ro'=>'ro','title_es'=>'es'),$this->input->post('values'),$date);

        foreach ($data as $k=>$v) {
            $this->db->query("UPDATE `themes_lang` SET `title`='$v' WHERE `theme_id`='" . $id . "' AND `lang`='" . $k . "'");
        }

        $date['html'] = 'Edited with success';
        echo json_encode($date);

    }

    public function delete_theme($id)
    {
        $id = htmlspecialchars($id,ENT_QUOTES);
        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        if(!empty($this->input->post('post'))){
            $this->db->query("DELETE FROM `themes` WHERE `id`='".$id."'");
            $this->db->query("DELETE FROM `themes_lang` WHERE `theme_id`='".$id."'");
            echo 'Deleted';
        }
    }

    private function makeData($mustValues,$replcaeTitles,$post,&$date,$add = array())
    {
        $data = array();
        foreach ($post as $k=>$val)
        {
            $value = htmlspecialchars(trim($val['value']),ENT_QUOTES);
            if (in_array($val['name'],$mustValues)&&empty($value)){
                $date['error'].=', '.$val['title'];
            }


            if (array_key_exists($val['name'], $replcaeTitles)) $val['name'] = $replcaeTitles[$val['name']];
            $data[$val['name']]= $value;
        }
        if (mb_strlen($date['error'])>0) $date['error'] = mb_substr($date['error'],2).' not entered';
        foreach ($add as $k=>$v)
           $data[$k] = $v;
        return $data;
    }

    public function addtheme()
    {

        if (empty($this->session->userdata("admin_enter"))) redirect($this->admin_url());
        if(empty($this->input->post('values'))){
            $this->render('theme_form');
            return 0;
        }

        $date = array('html'=>'','error'=>'');
        $data = $this->makeData(array('title_en','title_ru','title_ro'),array('title_en'=>'en','title_ru'=>'ru','title_ro'=>'ro','title_es'=>'es'),$this->input->post('values'),$date);

        $this->db->insert('themes',array('alt_name'=>$data['en']));
        $themeID = $this->db->insert_id();

        foreach ($data as $k=>$v)
        $this->db->insert('themes_lang',array('lang'=>$k,'theme_id'=>$themeID,'title'=>$v));


        $date['html'] = 'ID of added theme is '.$this->db->insert_id();
        echo json_encode($date);


    }

	public function login()
	{
		
		$this->db->select('*');
        $this->db->from('admin');
        $query = $this->db->get();
		$post = $this->input->post();
		$userId = false;
		if($query->num_rows()==1)
        {
            $user = $query->row_array();
            if ($user['password'] = md5(md5($post['pass']) . $user['salt'])) {
				$this->session->set_userdata(array('admin_enter'=>time()));
				redirect('admin/');
			}
        }

	}
	
	
	
	private function render($page,$data = array())
	{
        $this->template->view('admin/header');
		if($this->session->userdata("admin_enter")) $this->template->view('admin/top_bar');
		$this->template->view('admin/'.$page,$data);
		$this->template->view('admin/footer');
	}

	
    

}

?>