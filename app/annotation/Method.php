<?php
/**
 * Create By Hunter
 * 2020/11/25 3:01 下午
 * 主要检测请求方式
 */
namespace app\annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Inphp\Annotation\IAnnotation;
use Inphp\Service\Http\Container;

/**
 * @Annotation()
 * @Target({"CLASS", "METHOD"})
 * Class Method
 * @package app\annotation
 */
class Method implements IAnnotation
{
    /**
     * ALL, GET, POST, AJAX_GET, AJAX_POST
     * @var array
     */
    public $type = [];

    /**
     * Method constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if(isset($values['value'])){
            $this->type = $values['value'];
        }
        $this->type = is_string($this->type) ? [$this->type] : $this->type;
    }

    public function process($class, string $target = null, string $targetType = null)
    {
        // TODO: Implement process() method.
        if(strtolower($this->type[0])=="all"){
            return;
        }
        $client = Container::getClient();
        $bool = $client->method !== null;
        if($bool){
            $ajax = false;
            $bool = false;
            foreach ($this->type as $str){
                $bool = $bool ? $bool : stripos($str, $client->method)!==false;
                $ajax = $ajax ? $ajax : stripos($str, "ajax")!==false;
            }
            if($ajax){
                $http_x_requested_with = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;
                if($http_x_requested_with == "XMLHttpRequest" && $bool){
                    //
                    return;
                }
            }elseif($bool){
                return;
            }
        }
        //未通过
        response(1, 'distrust request method');
    }
}