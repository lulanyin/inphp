<?php
namespace app\middleware;

use Inphp\Config;
use Inphp\Service\Http\Container;
use Inphp\Service\Http\Response;
use Inphp\Service\IMiddleWare;

class View implements IMiddleWare
{
    public static function process(Response $response, $controller = null, string $method = null)
    {
        // TODO: Implement process() method.
        //获取客户端请求数据
        if($response->content == null){
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
            //客户端数据
            $client = Container::getClient();
            $smarty->assign("get", $client->get);
            $smarty->assign("post", $client->post);
            //控制器赋值的模板数据
            $dataList = getAssignData();
            $dataList = is_array($dataList) ? $dataList : [];
            foreach ($dataList as $key => $value){
                $smarty->assign($key, $value);
            }

            //判断文件是否存在
            $service_config = Container::getConfig();
            $view_dir = $service_config['router']['http']['view'];
            $view_file = $view_dir.$status->path."/".$status->view;
            if(file_exists($view_file)){
                $smarty->setTemplateDir($view_dir.$status->path);
                $response->withContent($smarty->fetch($status->view));
            }else{
                $response->withStatus(404);
            }
        }
    }
}