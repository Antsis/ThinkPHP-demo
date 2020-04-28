<?php

namespace app\index\controller;

use app\index\controller\SendSms;
use app\index\controller\Captcha;
use think\Session;
use app\index\model\Web;
use Exception;
use think\Controller;


class Login extends Controller
{
    public function verify()
    {     
        $captcha = new Captcha();
        $captcha->captcha();
    }

    public function checkCode()
    {
        if(empty($_POST['code'])){
            return false;
        }
        $code = strtolower($_POST['code']);
        if(Session::get('verify_code')==null){
            return false;
        }
        if($code==Session::get('verify_code')){
            return true;
        }else{
            return false;
        }
    }




    public function register()
    {   
        if(empty($_POST['code'])){
            return "error";
        }
        if(empty($_POST['phone_email'])){
            return "error";
        }

        $code = strtolower($_POST['code']);
        $phoneEmail = $_POST['phone_email'];

        if(Session::get('verify_code')==null){
            return "error1";
        }
        if($code!=Session::get('verify_code')){
            return "error1";
        }
        if(preg_match('/^1[3-9]\d{9}$/', $phoneEmail)){
            Session::set('phone', $phoneEmail);
            Session::set('reg_state', '1');
            if($this->sendCodeSms()){
                return $phoneEmail;
            }else return 'error2';
        }else if(preg_match('/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/', $phoneEmail)){
            Session::set('email', $phoneEmail);
            Session::set('reg_state', '0');
            if($this->sendCodeEmail()){
                return $phoneEmail;
            }else return 'error2';
        }else return "error2";
    }

    public function sendCodeEmail()
    {
        $sm = new SendEmail();
        $res = $sm->sendCodeEmail(Session::get('email'));
        if($res==0){
            return 1;
        }else return 0;
    }

    public function sendCodeSms()
    {
        $sm = new SendSms();
        $res = $sm->sendCodeSms(Session::get('phone'));
        if($res==0){
            return 1;
        }else return 0;
    }

    public function register2()
    {
        if(empty($_POST['code'])){
            return "error";
        }
        if(empty($_POST['password'])){
            return "error";
        }
        $code = $_POST['code'];
        $p = $_POST['password'];
        if(!((strlen($p)>7&&strlen($p)<21)&&((preg_match('/\d/', $p)&&preg_match('/[a-zA-Z]/', $p))||(preg_match('/[a-zA-Z]/', $p)&&preg_match('/\W/', $p))||(preg_match('/\d/', $p)&&preg_match('/\W/', $p))))){
            return "error2";
        }
        if(Session::get('reg_state')){
            if(Session::get('sms_code')==null||Session::get('phone')==null){
                return "error";
            }
            if($code!=Session::get('sms_code')){
                return "error1";
            }
            $phone = Session::get('phone');
            if($this->regCreatePhone($phone, $p)){
                $array = [
                    'phone'     => $phone,
                    'passwd'    =>$p
                ];
                Session::set('logined', $array);
                return "success";
            }else return "error";
        }else{
            if(Session::get('email_code')==null||Session::get('email')==null){
                return 'error';
            }
            if($code!=Session::get('email_code')){
                return "error1";
            }
            $email = Session::get('email');
            if($this->regCreateEmail($email, $p)){
                $array = [
                    'email'     => $email,
                    'passwd'    => $p
                ];
                Session::set('logined', $array);
                return "success";
            }else return "error";
        }
    }

    public function regCreatePhone($phone, $passwd)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = "";
        for ($i = 0; $i < 6; $i++ ){
            $username .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        $username .= $phone;

        $user = new Web;
        $user->username = $username;
        $user->password = md5($passwd);
        $user->phone = $phone;
        try{
            if($user->save()){
                return 1;
            }else return 0;
        }catch(Exception $e){
            return 0;
        }
        
    }

    public function regCreateEmail($email, $passwd)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = "";
        for ($i = 0; $i < 6; $i++ ){
            $username .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        $str = explode("@", $email);
        $username .= $str[0];

        $user = new Web;
        $user->username = $username;
        $user->password = md5($passwd);
        $user->email = $email;
        try{
            if($user->save()){
                return 1;
            }else return 0;
        }catch(Exception $e){
            return 0;
        }
        
    }

    public function login()
    {
        if(empty($_POST['login_i'])||empty($_POST['password'])){
            return "error";
        }
        $login = $_POST['login_i'];
        $passwd = $_POST['password'];
        try{
        $res = Web::where('phone', $login)
                ->whereor('email', $login)
                ->find();
        if(empty($res)){
            return "error1";
        }
        $array = $res->toArray();
        $emailS = $array['email'];
        $phoneS = $array['phone'];
        $passwdS = $array['password'];
        $usernameS = $array['username'];
        }catch(Exception $e){
            return "error";
        }
        if($phoneS==$login&&$passwdS==md5($passwd)||$emailS==$login&&$passwdS==md5($passwd)||$usernameS==$login&&$passwdS==md5($passwd)){
            Session::set('logined', $array);
            return "success";
        }else return "error2";
    }
}