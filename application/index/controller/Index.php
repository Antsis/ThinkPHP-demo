<?php
namespace app\index\controller;


use think\Controller;
use think\Session;

class Index extends Controller
{

    public function index()
    {
        return view();
    }

    public function checkLogined()
    {
        if(Session::get('logined')!=null){
            return "logged";
        }else return "notloged";
    }

    public function about()
    {
        return $this->fetch();
    }

    public function contact()
    {
        return $this->fetch();
    }

    public function userInfo()
    {
        if(Session::get('logined')==null){
            $this->error('请您登录');
        }else $this->redirect('profile/profile');
    }

    public function logout()
    {
        Session::clear();
        $this->success('正在跳转', 'index/index');
    }
}
