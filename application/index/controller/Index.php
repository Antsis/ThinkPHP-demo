<?php
namespace app\index\controller;


use think\Controller;

class Index extends Controller
{

    public function index()
    {
        return view();
    }


    

    public function test()
    {
        $sm = new Login();
        $sm->regCreateEmail("707636381@qq.com", "7076zheshiwcz");
    }
}
