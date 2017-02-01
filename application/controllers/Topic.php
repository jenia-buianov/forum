<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topic extends CI_Controller {

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
	public function view($url)
	{
	    $this->load->model('Topic_Model','tm');
	    $this->load->model('User_Model','um');
        $settings = $this->sm->returnSettings();

        $data['topic'] = $this->tm->getTopic($url);
        $data['topic']->views = $this->tm->topicViews($url);

        $lastVisit = $this->um->getStatus($data['topic']->userId);
        if ($lastVisit > time() - 900) $data['topic']->userStatus = 'online';
        else $data['topic']->userStatus = date('d.m.Y ' . translation('in') . ' H:i', $lastVisit);
        if(!isset($_GET['page'])) $start = 0;
        else {
            $_GET['page'] = (int)$_GET['page'];
            $start = ($_GET['page'] - 1) * $settings['messagesOnPage'];
        }

        $data['topic']->messages = $this->tm->getMessages($data['topic']->messageId,$start,$settings['messagesOnPage']);

        $this->template->render('topic_view',$data);

	}
}
