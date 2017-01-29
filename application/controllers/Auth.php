<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

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
	public function registration()
	{
	    if(getUser()) redirect(base_url());

		$this->template->render('registration');
	}

	public function reg(){

        if(empty($_POST['values'])){
            return translation('no entered form');
        }


        $date = array('html'=>'','error'=>'');
        $data = makeData(array('name','keystring','email','password','password2'),array(),$_POST['values'],$date);
        if (empty($date['error'])) return json_encode($date);
        $this->load->helper('email');
        if(!valid_email($data['email'])) $date['error'] = translation('email error');
        if (empty($date['error'])) return json_encode($date);

        $this->load->model('user_model','um');
        if ($this->um->verifyEmail($data['email'])) $date['error'] = translation('email is used');
        if (empty($date['error'])) return json_encode($date);

        if ($data['password']!==$data['password2']) $date['error'] = translation('passwords are different');
        if (empty($date['error'])) return json_encode($date);

        



       return json_encode($date);

    }

}
