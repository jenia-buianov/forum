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
	    $this->load->model('Category_Model','cm');
	    $this->load->model('User_Model','um');
        $topCategories = $this->cm->getTopCategories();
        $categories = array();
        foreach ($topCategories as $k =>$v){
            $child = $this->cm->getChildCategories($v->categoryId);
            $categories['category/view/'.$v->url]['title'] = translation($v->titleKey);
            foreach ($child as $item =>$value){
                if(!empty($value->descKey)) $desc = translation($value->descKey); else $desc = '';
                $categories['category/view/'.$v->url]['child'][] = array('locked'=>$value->locked,'title'=>translation($value->titleKey),'url'=>$value->url,'topics'=>$this->cm->countTopics($value->categoryId),'messages'=>$this->cm->countMessages($value->categoryId),'desc'=>$desc,'last'=>$this->cm->lastPost($value->categoryId),'unread'=>$this->cm->unreadCategory($value->categoryId));
            }
        }
        $data['categories'] = $categories;
        $data['onlineUsers'] = $this->um->onlineList();
        $data['online'] = $this->um->countOnline();
        $data['countCategories'] = $this->cm->countCategories();
        $data['countTopics'] = $this->cm->countAllTopics();
        $data['countMessages'] = $this->cm->countAllMessages();
		$this->template->render('welcome_message',$data);
	}
}
