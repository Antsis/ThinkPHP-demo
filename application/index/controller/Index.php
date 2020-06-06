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


    public function userInfo()
    {
        if(Session::get('logined')==null){
            $this->redirect('index/index', '', 200, [
                'wait'  => 3,
                'msg'   => '请您登录'
            ]);
        }
        return $this->fetch();
    }

    public function logout()
    {
        Session::clear();
        $this->redirect('index/index', "", 200, [ 
            'wait'  => 0, 
            'msg'   => '正在跳转'
        ]);
    }
}
