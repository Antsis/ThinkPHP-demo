<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Image;
use app\index\model\Web;
use Exception;

class Profile extends Controller{
    public function __construct()
    {
        parent::__construct();
        new Web();
    }
    public function uploadSession($array)
    {
        $res=Web::get($array['id']);
        return $res->toArray();
    }

    public function profile($op='base')
    {
        if(empty(Session::get('logined'))){
            $this->redirect('index/userinfo');
        }
        $data = $this->uploadSession(Session::get('logined'));
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
        $data=$this->uploadSession(Session::get('logined'));
        $this->assign('title', '修改头像');
        $this->assign('data', $data);
        return view();
    }
    public function account()
    {
        if(empty(Session::get('logined'))){
            $this->redirect('index/userinfo');
        }
        $data=$this->uploadSession(Session::get('logined'));
        $this->assign('title', '账号安全');
        return view();
    }
    
    public function profileSave()
    {
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
        if($_POST['qq']==""){
            $qq=null;
        }else $qq = $_POST['qq'];
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
    public function avatarUpload()
    {
        $file = request()->file('image');
        $data = Session::get('logined');
        if($file){
            $info = $file->validate(['ext'=>'jpg,jpeg,png', 'type'=>'image/png,image/jpeg']);
            if($info){
                $image = Image::open($file);
                try{
                $info2 = $image->thumb(200, 200, 2)->save(ROOT_PATH . 'public' . DS . 'files'. DS. 'uploads'. DS. $data['username']. DS. 'avatar_200.jpg');
                $info3 = $image->thumb(38, 38, 2)->save(ROOT_PATH . 'public' . DS . 'files'. DS. 'uploads'. DS. $data['username']. DS. 'avatar_38.jpg');
                }catch(Exception $e){
                    return 'error5';
                }
                $path = 'files/uploads/'.$data['username'].'/';
                if($info2&&$info3){
                    $user = new Web;
                    try{
                        $user->save([
                            'avatar_url' => $path
                        ], ['id' => $data['id']]);
                        return "success";
                    }catch(Exception $e){
                        return "error4";
                    }
                }else return 'error3';
            }else{
                return 'error2';
            }
        }else{
            return 'error';
        }
    }
}