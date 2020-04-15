<?php
namespace app\index\controller;

use AlibabaCloud\Client\Request\Traits\RetryTrait;
use app\index\controller\SendSms;
use app\index\controller\Captcha;
use think\Session;
use app\index\model\Web;
use app\index\model\Game;
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
            return "error1";
        }
        $code = strtolower($_POST['code']);
        $phoneNum = $_POST['phone'];
        if(Session::get('verify_code')==null){
            return "error2";
        }
        if($code!=Session::get('verify_code')){
            return "error3";
        }
        if(!(preg_match('/^1[3-9]\d{9}$/', $phoneNum))){
            return "error4";
        }
        Session::set('phone', $phoneNum);
        if($this->sendCodeSms()){
            return $phoneNum;
        }else{
            return "error5";
        }
        
    }

    public function sendCodeSms()
    {
        $sm = new SendSms();
        $sm->sendCodeSms(Session::get('phone'));
        return 1;
    }

    public function register2()
    {
        if(empty($_POST['smscode'])){
            return "error";
        }
        if(empty($_POST['password'])){
            return "error1";
        }
        $code = $_POST['smscode'];
        $p = $_POST['password'];
        if(Session::get('smsCode')==null){
            return "error3";
        }
        if(!((strlen($p)>7&&strlen($p)<21)&&((preg_match('/\d/', $p)&&preg_match('/[a-zA-Z]/', $p))||(preg_match('/[a-zA-Z]/', $p)&&preg_match('/\W/', $p))||(preg_match('/\d/', $p)&&preg_match('/\W/', $p))))){
            return "error4";
        }
        if(Session::get('phone')==null){
            return "error5";
        }
        $phone = Session::get('phone');
        if($this->regCreate($phone, $p)){
            return "success";
        }else{
            return "error6";
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
            return "error2";
        }
        $phone = $_POST['phone'];
        $passwd = $_POST['password'];
        try{
        $res = Web::where('phone', '18889288054')
            ->field("phone, password")
            ->find();
        $array = $res->toArray();
        $phoneS = $array['phone'];
        $passwdS = $array['password'];
        }catch(Exception $e){
            return "error3";
        }
        if($phoneS==$phone&&$passwdS==$passwd){
            return "success";
        }else return "error4";
    }

    public function test1()
    {

        
    }
}
