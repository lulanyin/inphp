<?php
// +----------------------------------------------------------------------
// | INPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2020 https://inphp.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/licenses/MIT )
// +----------------------------------------------------------------------
// | Author: lulanyin <me@lanyin.lu>
// +----------------------------------------------------------------------
namespace Inphp\Middleware;

use Inphp\Config;
use Inphp\Service\Context;
use Inphp\Service\Http\Response;
use Inphp\Service\Middleware\IServerOnResponseMiddleware;

/**
 * 视图处理，仅在 http
 * 这里使用了 Smarty 模板引擎
 * Class View
 * @package Inphp\Middleware
 */
class View implements IServerOnResponseMiddleware
{
    public function __construct()
    {

    }

    public function process(Response $response, $controller = null, $method = null)
    {
        // TODO: Implement process() method.
        //获取客户端请求数据
        if($response->content == null && $response->content_type == 'default'){

            $status = $response->status;
            $smarty_config = Config::get("private.smarty");
            $smarty = new \Smarty();
            $smarty->cache_lifetime = $smarty_config['cache_lifetime'];
            $smarty->left_delimiter = $setting['left_delimiter'] ?? "{";
            $smarty->right_delimiter = $setting['right_delimiter'] ?? "}";
            $smarty->setCacheDir($smarty_config['cache_dir']);
            $smarty->setCompileDir($smarty_config['compile_dir']);

            //载入公共配置
            $configs = Config::get("public");
            $smarty->assign("configs", $configs);
            $smarty->assign("method", $response->status->method);
            $smarty->assign("domain", Config::get("domain"));
            //客户端数据
            $client = Context::getClient();
            $smarty->assign("get", $client->get);
            $smarty->assign("post", $client->post);
            //控制器赋值的模板数据
            $dataList = getAssignData();
            $dataList = is_array($dataList) ? $dataList : [];
            foreach ($dataList as $key => $value){
                $smarty->assign($key, $value);
            }
            //控制器返回的数据
            if(!empty($response->controller_result)){
                $smarty->assign('data', $response->controller_result);
            }
            //视图位置
            $view_dir = $status->view_dir;
            $view_dir = strrchr($view_dir, "/") == "/" ? $view_dir : "{$view_dir}/";
            //首个斜杠去掉
            $file = stripos($status->view, "/") === 0 ? substr($status->view, 1) : $status->view;
            //后缀
            $file = in_array(strrchr($file, "."), [".html", ".htm", ".tpl"]) ? $file : "{$file}.html";
            //组合路径
            $view_file = $view_dir."/".$file;
            //双斜杠转换为单斜杠
            $view_file = str_replace("//", "/", $view_file);
            if(file_exists($view_file)){
                $smarty->setTemplateDir($view_dir);
                try{
                    $response->withHTML($smarty->fetch($file));
                }catch (\Exception $exception){
                    $response->withText($exception->getMessage());
                }
            }else{
                $response->withStatus(404);
            }
        }
    }
}