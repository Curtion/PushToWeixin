<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 基于Server酱提供的微信推送服务，插件作用是把新评论推送到微信，用作提醒作用！
 *
 * @package PushToWeixin
 * @author Curtion
 * @version 1.0.0
 * @link https://blog.3gxk.net/
 */
class PushToWeixin_Plugin implements Typecho_Plugin_Interface{//所有的方法放在这个类里面
	public static function activate(){//插件初始化方法
        Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('PushToWeixin_Plugin', 'render');//把render方法挂载到评论钩子上
        //挂载点在http://docs.typecho.org/plugins/hooks查看
		return '启动成功，请到设置界面设置你的SCKEY';
	}
	 
	public static function deactivate(){//插件禁用方法
		return '禁用成功';
	}
	 
	public static function config(Typecho_Widget_Helper_Form $form){//插件的配置方法
        $element = new Typecho_Widget_Helper_Form_Element_Text('sckey', null, '', 'SCKEY值', '如果你不知道SCKEY的话，请访问<a href="http://sc.ftqq.com/3.version">Server酱</a>，然后Github登录后绑定微信获取SCKEY');
        $form->addInput($element);//把sckey的值写入配置，使用plugin('PushToWeixin')->sckey获取
    }
    
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
        //个人配置方法，没什么用，但是必须加上
    }

	public static function render($comment){//推送方法
        $Plugin_Config = Helper::options();//获取配置
        $sckey = $Plugin_Config->plugin('PushToWeixin')->sckey;
        $text = '有人在你的博客中留言了';
        $desp = "**".$comment->author."**在你的文章**".$comment->title."**内说: \n\n >".$comment->text;
        //内容支持makedown，语法详情：http://www.appinn.com/markdown/#blockquote
        //下面的代码来自http://sc.ftqq.com/?c=code
        $postdata = http_build_query(
        array(
                'text' => $text,
                'desp' => $desp
            )
        );
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);
    return $result = file_get_contents('https://sc.ftqq.com/'.$sckey.'.send', false, $context);	
	}
}
