<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class CategoryWidget extends Controller{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($cate, $child = false){
		$field = 'id,name,pid,title,link_id';
		if($child){
			$category = D('Category')->getTree($cate, $field);
			$category = $category['_'];
		} else {
			$category = D('Category')->getSameLevel($cate, $field);
		}
		$member=D('Member');
		$info=$member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->find();
		$order=explode(',',$info['order']);
		foreach ($order as $key=>$value){
		    $nickname[]=$member->where(array('uid'=>$value))->find();
		}
		$this->assign('nickname',$nickname);
		$this->assign('category', $category);
		$this->assign('current', $cate);
		$this->display('Category/lists');
	}

}
