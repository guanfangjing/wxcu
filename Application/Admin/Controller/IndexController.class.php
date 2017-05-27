<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use Home\Model\DocumentModel;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $this->meta_title = '管理首页';
        
        /* 前台迁移过来的数据 */
        $category = D('Category')->getTree();
        $DocumentModel   = new DocumentModel();
        $lists = $DocumentModel->lists(null);
        $this->assign('category',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',$DocumentModel->page);//分页
        /* end前台迁移过来的数据 */
        
        $this->display();
    }
}
