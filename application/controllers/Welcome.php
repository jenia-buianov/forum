<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
	    $this->load->model('category_model','cm');
        $topCategories = $this->cm->getTopCategories();
        $categories = array();
        foreach ($topCategories as $k =>$v){
            $child = $this->cm->getChildCategories($v->categoryId);
            $categories[$v->url]['title'] = translation($v->titleKey);
            foreach ($child as $item =>$value){
                if(!empty($value->descKey)) $desc = translation($value->descKey); else $desc = '';
                $categories[$v->url]['child'][] = array('title'=>translation($value->titleKey),'url'=>$value->url,'topics'=>$this->cm->countTopics($value->categoryId),'messages'=>$this->cm->countMessages($value->categoryId),'desc'=>$desc);
            }
        }
        $data['categories'] = $categories;
		$this->template->render('welcome_message',$data);
	}
}
