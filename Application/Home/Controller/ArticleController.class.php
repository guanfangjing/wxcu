<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Home\Model\MemberModel;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category();

		//频道页只显示模板，默认不读取任何内容
		//内容可以通过模板标签自行定制

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->display($category['template_index']);
	}

	/* 文档模型列表页 */
	public function lists($p = 1){
		/* 分类信息 */
	    if(I('uid')==''){
    		$category = $this->category();
    
    		/* 获取当前分类列表 */
    		$Document = D('Document');
    		$list = $Document->page($p, $category['list_row'])->lists($category['id']);
    		if(false === $list){
    			$this->error('获取列表数据失败！');
    		}
    
    		/* 模板赋值并渲染模板 */
    		$this->assign('category', $category);
    		$this->assign('list', $list);
    		$this->assign('ttttt',$_SESSION['onethink_admin']['user_auth']['uid']);
    		$this->assign('_who',$_SESSION['onethink_admin']['user_auth']['username']);
    		$this->display($category['template_lists']);
	    }else{
	        $category = $this->category();
	        /* 获取当前分类列表 */
	        $Document = D('Document');
	        $list = $Document->page($p, $category['list_row'])->lists($category['id']);
	        if(false === $list){
	            $this->error('获取列表数据失败！');
	        }
	        foreach ($list as $key=>$value){
	            if($value['uid']==I('uid')){
	                $info[]=$value;
	            }
	        }
	        /* 模板赋值并渲染模板 */
	       $this->assign('category', $category);
	        $this->assign('list', $info);
	        $this->assign('_who',$_SESSION['onethink_admin']['user_auth']['username']);
	        $this->display($category['template_lists']);
	        
	    }
	}

	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		/* 标识正确性检测 */
		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}

		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$Document = D('Document');
		$info = $Document->detail($id);
		if(!$info){
			$this->error($Document->getError());
		}

		/* 分类信息 */
		$category = $this->category($info['category_id']);

		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else { //使用默认模板
			$tmpl = 'Article/'. get_document_model($info['model_id'],'name') .'/detail';
		}

		/* 更新浏览数 */
		$map = array('id' => $id);
		$Document->where($map)->setInc('view');

	   $comment=D('comment');
		$map=array('articleid'=>$info['id']);
		$num =  $comment->where($map)->count(); //获取评论总数
		$this->assign('num',$num);
		$data=array();
		//$data=$this->getCommlist();//获取评论列表
		$data = D('comment')->where($map)->order("create_time desc")->select();//获取当前文章评论
		$user=D('Member');
		foreach ($data as $key=>$value){
		   $data[$key]['nickname']=$user->where(array('uid'=>$value['userid']))->getField('nickname');
		   $data[$key]['create_time']=date('Y-m-d H:i:s', $value['create_time']);
		    
		}
		$member=D('Member');
		$orderinfo=$member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->find();
		$order=explode(',',$orderinfo['order']);
		if(in_array($info['uid'], $order)){
		    $this->assign('_order','cancleorder');
		}else{
		    $this->assign('_order','order');
		}
		$this->assign("commlist",$data);		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('page', $p); //页码
		$this->display($tmpl);
	}

	/**
	 *添加评论
	 */
	public function addComment(){
       if(empty($_SESSION['onethink_admin']['user_auth']['uid'])){
           $this->ajaxReturn('没有登录无法评论');
       }
	    $data=array();
	    if((isset($_POST["message"]))&&(!empty($_POST["message"]))){
	        $cm=$_POST;
	        $cm['userid']=$_SESSION['onethink_home']['user_auth']['uid'];
	        
	        $cm['create_time']=NOW_TIME;
	        $cm['articleid']=$_GET['articleid'];
	        $cm['pid']='0';
	        $newcm = D('comment');
	        $id = D('comment')->doUpdate($cm);
	        $this->ajaxReturn('评论成功');
	    }else{
	       $this->ajaxReturn('评论失败');
	    }
	     
	     
	}
	
	/**
	 *关注
	 */
	public function order(){
	    if(empty($_SESSION['onethink_admin']['user_auth']['uid'])){
	        $this->ajaxReturn('没有登录无法关注');
	    }
	    $data=array();
	    if($_SESSION['onethink_admin']['user_auth']['uid']==I('uid')){
	        $this->ajaxReturn('无法关注自己');
	    }else{
	         
	        $member=D('Member');
	        $info=$member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->find();
	        if($info['order']==''){
	            $member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->setField('order',$_GET['uid']);
	        }else{
	            $info['order'].=','.$_GET['uid'];
	            $member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->setField('order',$info['order']);
	        }
	        $this->ajaxReturn('关注成功');
	    }
	}
	/**
	 *取消关注
	 */
	public function cancleorder(){
	    if(empty($_SESSION['onethink_admin']['user_auth']['uid'])){
	        $this->ajaxReturn('请登录');
	    }
	    $member=D('Member');
	    $info=$member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->find();
	    if($info['order']!=''){
	        $order=explode(',',$info['order']);
	        foreach($order as $value){
	            if($value!=$_GET['uid']){
	                $neworder.=','.$value;
	            }
	        }
	        $neworder= substr($neworder,1);
	        $member->where(array('uid'=>$_SESSION['onethink_admin']['user_auth']['uid']))->setField('order',$neworder);
	    }
	    $this->ajaxReturn('取消关注成功');
	}
	
// 	/**
// 	 *递归获取评论列表
// 	 */
// 	protected function getCommlist($pid = 0,&$result = array()){
// 	    $arr = D('comment')->where("pid = '".$pid."'")->order("create_time desc")->select();
// 	    if(empty($arr)){
// 	        return array();
// 	    }
// 	    foreach ($arr as $cm) {
// 	        $thisArr=&$result[];
// 	        $cm["children"] = $this->getCommlist($cm["id"],$thisArr);
// 	        $thisArr = $cm;
// 	    }
// 	    return $result;
// 	}
	
	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}
	
	public function addscore(){
	    
	    if(empty($_SESSION['onethink_admin']['user_auth']['uid'])){
	        $this->ajaxReturn('没有登录无法评论');
	    }
	    $auid=$_POST['uid'];
	    $duid=$_SESSION['onethink_admin']['user_auth']['uid'];
	    
	    $Member=M('Member');
	    $duidscore=$Member->where(array('uid'=>$duid))->getField('score');
	    if($duidscore<=5){
	        $this->error("您没有足够的积分打赏该用户，请赚取积分后再打赏");
	    } else {
	        $result=$Member->where(array('uid'=>$duid))->setDec('score',5);
	        $result1=$Member->where(array('uid'=>$auid))->setInc('score',5);
	        $this->success("打赏成功");
	    }
	}
	
	
	 public function addzan(){
        if(empty($_SESSION['onethink_admin']['user_auth']['uid'])){
            $this->ajaxReturn('没有登录无法点赞');
        }
	    $articleid=$_POST['articleid'];
	    $Document=M('Document');
	    $result1=$Document->where(array('id'=>$articleid))->setInc('thumbs_up_count',1);
	    $this->ajaxReturn('点赞成功');
	    
	} 

}
