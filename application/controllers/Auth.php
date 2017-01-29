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

    public function singin()
    {
        if(getUser()) redirect(base_url());

        $this->template->render('singin');
    }

    public function logout(){
        if (getUser()){
            $page = getLastPage();
            $this->session->unset_userdata('user');
            redirect(base_url($page));
        }
    }

    public function login(){
        if(empty($_POST['values'])){
            return translation('no entered form');
        }

        $date = array('html'=>'','error'=>'','redirect'=>base_url(''),'delay'=>0);
        $data = makeData(array('name','keystring','email','password','password2'),array(),$_POST['values'],$date);
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }
        $this->load->helper('email');
        if(!valid_email($data['email'])) $date['error'] = translation('email error');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        $this->load->model('user_model','um');
        $user = $this->um->getUserByEmail($data['email']);
        if(count($user)==0) $date['error'] = translation('not found email');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        if(!password_verify($data['password'],$user->password)) $date['error'] = translation('password incorrect');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }
        $this->session->set_userdata('user',$user->userId);
        $data['html'] = 'Success';
        echo json_encode($date);

    }

	public function reg(){

        if(empty($_POST['values'])){
            return translation('no entered form');
        }


        $date = array('html'=>'','error'=>'','redirect'=>base_url('auth/singin'),'delay'=>2000);
        $data = makeData(array('name','keystring','email','password','password2'),array(),$_POST['values'],$date);

        if (!empty($date['error'])) {        echo json_encode($date);           return;      }
        $this->load->helper('email');
        if(!valid_email($data['email'])) $date['error'] = translation('email error');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        $this->load->model('user_model','um');
        if ($this->um->verifyEmail($data['email'])) $date['error'] = translation('email is used');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        if ($data['password']!==$data['password2']) $date['error'] = translation('passwords are different');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        if (mb_strlen($data['password'])<8) $date['error'] = translation('password short');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        if(!isset($_COOKIE['captcha_keystring']) or empty($_COOKIE['captcha_keystring']) or ($_COOKIE['captcha_keystring'] !== $data['keystring'])) $date['error'] = translation('code is incorrect');
        if (!empty($date['error'])) {        echo json_encode($date);           return;      }

        unset($_COOKIE['captcha_keystring']);
        setcookie('captcha_keystring', null, -1, '/');

        $ignore = array('password2','keystring');
        $data['activationCode'] = mb_substr(md5(md5($data['email'].time()).$data['name']),0,25);
        $data['lang'] = getLang();
        $this->load->model('settings_model','sm');
        $settings_temp = $this->sm->get();

        foreach ($settings_temp as $k=>$v){
            $settings[$v->key] = $v->value;
        }

        $body = '<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body style="width:100%;text-align:center;margin:0px;background: #E6EFF2;color:#666;padding:20px">
	<div style="background:#fff;width:60%;margin-left:20%;padding: 15px;">
        <table width=100%><tr>
        <td valign=middle align=center>'.$settings['title'].'</td>
        </tr></table>
        <b style="font-size:15px;color:#000">'.translation('dates reg').'</b>
        <p style="width:100%;display:block;text-align:left"><font style="display:inline-block;width:120px;color:#333">'.translation('name').':</font> '.$data['name'].'</p>
        <p style="width:100%;display:block;text-align:left"><font style="display:inline-block;width:120px;color:#333">'.translation('email').':</font> '.$data['email'].'</p>
        <p style="width:100%;display:block;text-align:left"><font style="display:inline-block;width:120px;color:#333">'.translation('password').':</font> '.$data['password'].'</p>
        <p style="text-align:center;margin-top:15px"><a href="'.base_url('users/confirm/'.$data['activationCode']).'">'.translation('activate profile').'</a></p>
    
        <p align=Center style="color:#000;margin-top:25px;">При возникновении вопросов пишите jeniabuianov@gmail.com</p>
	
	</div>
</body>

</html>';
        $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);

        $rows = "";
        $values = "";
        foreach ($data as $k=>$v){
            if(!in_array($k,$ignore)){
                $rows.= "`".$k."`,";
                $values.="'".$v."',";
            }
        }
        $query = "INSERT INTO `users`(".mb_substr($rows,0,mb_strlen($rows)-1).")VALUES(".mb_substr($values,0,mb_strlen($values)-1).")";
        $this->db->query($query);

        $sendMail = send(host,port,kind,true,mail,mail_password,$settings['title'],$data['email'],$data['name'],$body,translation('registration email title'));
        if (is_bool($sendMail) and $sendMail==true) $date['html'] = translation('registration with success');
        else $date['error'] = $sendMail;

        echo json_encode($date);

    }

}
