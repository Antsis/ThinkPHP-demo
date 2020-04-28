<?php
namespace app\index\controller;

use app\index\controller\SendSms;
use app\index\controller\Captcha;
use think\Session;
use app\index\model\Web;
use Exception;
use think\Controller;

class Index extends Controller
{

    public function index()
    {
        return view();
    }

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
        if(empty($_POST['phone'])){
            return "error";
        }
        $code = strtolower($_POST['code']);
        $phoneNum = $_POST['phone'];
        if(Session::get('verify_code')==null){
            return "error1";
        }
        if($code!=Session::get('verify_code')){
            return "error1";
        }
        if(!(preg_match('/^1[3-9]\d{9}$/', $phoneNum))){
            return "error2";
        }
        Session::set('phone', $phoneNum);
        return $phoneNum;
        if($this->sendCodeSms()){
            return $phoneNum;
        }else{
            return "error";
        }
        
    }

    public function sendCodeSms()
    {
        $sm = new SendSms();
        $res = $sm->sendCodeSms(Session::get('phone'));
        if($res==0){
            return 0;
        }else if($res==1){
            return 1;
        }else{
            return 2;
        }
    }

    public function register2()
    {
        if(empty($_POST['smscode'])){
            return "error";
        }
        if(empty($_POST['password'])){
            return "error";
        }
        $code = $_POST['smscode'];
        $p = $_POST['password'];
        if(Session::get('smsCode')==null){
            return "error";
        }
        if($code!=Session::get('smsCode')){
            return "error2";
        }
        if(!((strlen($p)>7&&strlen($p)<21)&&((preg_match('/\d/', $p)&&preg_match('/[a-zA-Z]/', $p))||(preg_match('/[a-zA-Z]/', $p)&&preg_match('/\W/', $p))||(preg_match('/\d/', $p)&&preg_match('/\W/', $p))))){
            return "error1";
        }
        if(Session::get('phone')==null){
            return "error";
        }
        
        $phone = Session::get('phone');
        if($this->regCreate($phone, $p)){
            
            return "success";
        }else{
            return "error";
        }
    }

    public function regCreate($phone, $passwd)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = "";
        for ( $i = 0; $i < 6; $i++ ){
            $username .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        $username .= $phone;
        $user = new Web;
        $user->username = $username;
        $user->password = md5($passwd);
        $user->phone = $phone;
        $user->create_time = time();
        try{
            if($user->save()){
                return true;
            }else return false;
        }catch(Exception $e){
            return false;
        }
        
    }

    public function login()
    {
        if(empty($_POST['phone'])){
            return "error";
        }
        if(empty($_POST['password'])){
            return "error";
        }
        $phone = $_POST['phone'];
        $passwd = $_POST['password'];
        try{
        $res = Web::where('phone', $phone)
                ->find();
        if(empty($res)){
            return "error1";
        }
        $array = $res->toArray();
        $phoneS = $array['phone'];
        $passwdS = $array['password'];
        }catch(Exception $e){
            return "error";
        }
        if($phoneS==$phone&&$passwdS==$passwd){
            Session::set('logined', $res);
            return "success";
        }else return "error2";
    }

    public function test()
    {

    }
}
