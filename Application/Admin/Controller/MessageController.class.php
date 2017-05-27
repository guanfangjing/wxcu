<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Think\Page;
/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class MessageController extends AdminController {

    public function index(){
        
          $dao = D('message');
          $map['reciver'] = 1234;// $_SESSION['onethink_admin']['user_auth']['username'];
          $count = $dao ->where($map)->count();
          
          $page = new \Think\Page($count,10);   
            $show = $page -> show();
        $list=$dao->where($map)->limit($page->firstRow,$page->listRows)->select();
        $this->assign('_list',$list);//列表
        $this->assign('_page',$show);//分页
        $this->assign("_hours",date('H'));
        $this->display();
    }
    
    function sendmessage(){
        if(IS_POST){
            $_POST['sender'] = $_SESSION['onethink_admin']['user_auth']['username'];
            $_POST['time'] = NOW_TIME;
            $_POST['tag'] = 0;
            if(D('message')->add($_POST)){
                $this->success("私信发送成功",'/onethink/index.php?s=/Home/Article/lists/category/default_blog.html');
            } else {
                $this->error("私信发送失败",'/onethink/index.php?s=/Home/Article/lists/category/default_blog.html');
            }
        } else {
            $this->assign("reciver",I('reciver'));
            $this->assign("_hours",date('H'));
            $this->display();
        }
    }
    
    function detail(){
        
        $dao = D('message');
        $list =  $dao->where(array('id'=>I('id')))->find();
        $dao->where(array('id'=>I('id')))->setField('tag',I('tag'));
        $this->assign("_hours",date('H'));
        $this->assign('list',$list);
        $this->display();
    }
    
    function  remove(){
        $dao = D('message');
        $map['id']=array('in',I('id'));
        if($dao->where($map)->delete()){
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

}