<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

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
	public function view($id)
	{
	    $this->load->model('Category_Model','cm');
	    $this->load->model('User_Model','um');
        $cat = $this->cm->getCategoryInfo($id);
        if (!count($cat))
        {
            $data['heading'] = 'NOT FOUND';
            $data['message'] = 'SORRY! PAGE WAS NOT FOUND';
            $this->template->render('errors/html/error_404',$data);
            return ;
        }
        if (!$cat->parentId) {
            $child = $this->cm->getChildCategories($cat->categoryId);
            $categories['category/view/' . $cat->url]['title'] = translation($cat->titleKey);
            foreach ($child as $item => $value) {
                if (!empty($value->descKey)) $desc = translation($value->descKey); else $desc = '';
                $categories['category/view/' . $cat->url]['child'][] = array('locked' => $value->locked, 'title' => translation($value->titleKey), 'url' => $value->url, 'topics' => $this->cm->countTopics($value->categoryId), 'messages' => $this->cm->countMessages($value->categoryId), 'desc' => $desc, 'last' => $this->cm->lastPost($value->categoryId), 'unread' => $this->cm->unreadCategory($value->categoryId));
            }
            $data['categories'] = $categories;
            $this->template->render('category_view',$data);
        }
        else{
            $topics = $this->cm->getTopicsFromCategory($id);
            print_r($topics);
 //           $this->template->render('category_topics',$data);
        }
	}
}
