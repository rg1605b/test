<?php 

namespace app\controllers;

use Yii;
use yii\web\Controller;

class ClientController extends Controller
{     
	
	  private $xml;//xml对象
     
	  public $enableCsrfValidation=false;


      public function actionIndex(){
        
        //验证来源
      	// $this->checkSign();
      	
      	$str=file_get_contents('php://input'); 

      	file_put_contents('./a.xml',$str);

      	$this->xml=simplexml_load_string($str);

        switch ($this->xml->MsgType) {

        	case 'event' : $this->_event();break;

        	case 'text' : $this->_event();break;
        }
      	      
      }
      
      private function _event(){
        
         //关注事件
         if($this->xml->Event == 'subscribe'){

               $this->_msg('text','欢迎你关注我的第一个公众号');

         }else if($this->xml->MsgType == 'text'){

               $this->_msg('text','回复文本消息');

         }

      }
      
private function _msg($type='text',$data=array()){
            
    $xml='
      <xml>
        <ToUserName><![CDATA['.$this->xml->FromUserName.']]></ToUserName>
        <FromUserName><![CDATA['.$this->xml->ToUserName.']]></FromUserName>
        <CreateTime><![CDATA['.time().']]></CreateTime>
        <MsgType><![CDATA['.$type.']]></MsgType>';
      
        switch ($type) {

        	case 'text' : $xml.='<Content>'.$data.'</Content>';break;
        }

        $xml.='</xml>';

        echo $xml;exit;
      }
      private function checkSign(){

      	 $get=Yii::$app->request->get(); 
         
         if(!isset($get['timestamp']) &&
         	!isset($get['nonce']) &&
         	!isset($get['signature']) &&
         	!isset($get['echostr'])){
         	die('params errors');
         }

         // file_put_contents('a.txt',json_encode($get)); 
         
         $token='suibian';

         $arr=array($get['timestamp'],$get['nonce'],$token);

         sort($arr);

         $str=implode($arr);

         $sign=sha1($str);

         if($sign == $get['signature']){

             echo $get['echostr'];

         }else{

         	 die('sign errors');

         }
    }
}



