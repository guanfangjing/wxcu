<?php
namespace Home\Model;
use Think\Model;

/**
 * 分类模型
 */
class CommentModel extends Model{
    
    
    /* 定义用户字段 */
    protected $fields = array(
        'id','userid','pid','create_time','message','articleid','_pk'=>'id'
    );
    
   /* 定义字段自动验证  */
   protected $_validate = array(
     
   );
   
   
   /* 定义字段自动完成 */
   protected $_auto = array(

       
   );
   
   
   function doUpdate($data=null,$where=null){
       if(empty($where)){
           $data = $this -> create($data);
           if(!empty($data)){
               if(!$this->add($data)){
                   return $this->getDbError();
               }
               return $data;
           } else {
               return $this->error;
           }
       }
   }
}
