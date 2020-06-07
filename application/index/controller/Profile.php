<?php
namespace app\index\controller;

use think\Controller;

class Profile extends Controller{
    public function profile($op='base')
    {
        $this->assign('title', '个人资料');
        if($op=='base'){
            return view();
        }else if($op=='contact'){

            return view('profile/profile_contact');
        }else{
            $this->error('参数错误');
        }
        
        return view();
    }
    public function avatar()
    {
        $this->assign('title', '修改头像');
        return view();
    }
    public function account()
    {
        $this->assign('title', '账号安全');
        return view();
    }
}