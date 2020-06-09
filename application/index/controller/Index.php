<?php
namespace app\index\controller;


use think\Controller;
use think\Session;

class Index extends Controller
{

    public function index()
    {
        $this->checkSession();
        return view();
    }

    public function checkLogined()
    {
        if(Session::get('logined')!=null){
            return "logined";
        }else return "notlogin";
    }
    public function bbs()
    {
        $this->checkSession();
        return $this->fetch();
    }


    public function about()
    {
        $this->checkSession();
        return $this->fetch();
    }

    public function contact()
    {
        $this->checkSession();
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
    public function checkSession()
    {
        if(Session::get('logined')){
            $data = Session::get('logined');
            $this->assign('data', $data);
        }else  $this->assign('data', ['avatar_url'=>null]);
    }
}
