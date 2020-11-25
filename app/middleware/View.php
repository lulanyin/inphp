<?php
namespace app\middleware;

use Inphp\Config;
use Inphp\Service\Http\Container;
use Inphp\Service\Http\Response;
use Inphp\Service\IMiddleWare;

class View implements IMiddleWare
{
    public function __construct()
    {

    }

    public function process(Response $response, $controller = null, string $method = null)
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
            //控制器返回的数据
            if(!empty($response->controller_result)){
                $smarty->assign('data', $response->controller_result);
            }
            //判断文件是否存在
            $service_config = Container::getConfig();
            //视图位置
            $view_dir = $service_config['router']['http']['view'];
            //首个斜杠去掉
            $file = stripos($status->view, "/") === 0 ? substr($status->view, 1) : $status->view;
            $view_file = $view_dir.$status->path."/".$file;
            //双斜杠转换为单斜杠
            $view_file = str_replace("//", "/", $view_file);
            if(file_exists($view_file)){
                $smarty->setTemplateDir($view_dir.$status->path);
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