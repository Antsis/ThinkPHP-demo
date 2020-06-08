<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use app\index\model\Web;
use Exception;

class Profile extends Controller{
    public function __construct()
    {
        parent::__construct();
        new Web();
    }

    public function profile($op='base')
    {
        if(empty(Session::get('logined'))){
            $this->redirect('index/userinfo');
        }
        $array = Session::get('logined');
        if(!empty($array['phone'])){
            $phone = $array['phone'];
        }else $phone=null;
        if(!empty($array['email'])){
            $email = $array['email'];
        }else $email=null;
        if(!empty($array['username'])){
            $username = $array['username'];
        }else $username=null;

        $data = $this->queryInfo($phone, $email, $username);
        if($data==null){
            $this->error('服务器内部错误, 请联系管理员');
        }
        if($data['birthday']==null){
            $data['birthday']=0;
        }
        $data['birthday']=date('Y-m-d', $data['birthday']);
        $this->assign('title', '个人资料');
        $this->assign('data', $data);
        if($op=='base'){
            return view();
        }else if($op=='contact'){
            return view('profile/profile_contact');
        }else{
            $this->error('参数错误');
        }
    }
    public function avatar()
    {
        if(empty(Session::get('logined'))){
            $this->redirect('index/userinfo');
        }
        $this->assign('title', '修改头像');
        return view();
    }
    public function account()
    {
        if(empty(Session::get('logined'))){
            $this->redirect('index/userinfo');
        }
        $this->assign('title', '账号安全');
        return view();
    }
    public function queryInfo($phone=null, $email=null, $username=null)
    {
        if($phone!=null){
            $res = Web::where('phone', $phone)
                    ->find();
            if($res==null){
                return 0;
            }else{
                return $res->toArray();
            }
        }else if($email!=null){
            $res = Web::where('email', $email)
                    ->find();
            if($res==null){
                return 0;
            }else{

                return $res->toArray();
            }
        }else if($username!=null){
            $res = Web::where('username', $username)
                    ->find();
            if($res==null){
                return 0;
            }else {
                return $res->toArray();
            }
        }else return;
    }
    public function profileSave()
    {
        if(empty($_POST['name'])&&empty($_POST['gender'])&&empty($_POST['birthday'])&&empty($_POST['signature'])){
            return "success2";
        }
        if(!empty($_POST['name'])){
            $name = $_POST['name'];
        }else $name = null;
        if(!empty($_POST['gender'])){
            $gender = $_POST['gender'];
        }else $gender = 0;
        if(!empty($_POST['birthday'])){
            $date = strtotime($_POST['birthday']);
        }else $date = 0;
        if(!empty($_POST['signature'])){
            $signature = $_POST['signature'];
        }else $signature = null;
        $array = Session::get('logined');
        $user = new Web;
        try{
            $user->save([
                'name' => $name,
                'gender' => $gender,
                'birthday' => $date,
                'signature' => $signature
            ], ['id' => $array['id']]);
            return "success";
        }catch(Exception $e){
            return "error";
        }
    }
    public function profileContactSave()
    {
        if(empty($_POST['qq'])){
            return "success2";
        }
        $qq = $_POST['qq'];
        if(!preg_match('/^[1-9][0-9]{4,11}$/', $qq)){
            return 'error';
        }
        $array = Session::get('logined');
        $user = new Web;
        try{
            $user->save([
                'qq' => $qq,
            ], ['id' => $array['id']]);
            return "success";
        }catch(Exception $e){
            return "error";
        }
    }
}