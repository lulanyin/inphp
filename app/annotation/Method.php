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
    public $type = "ALL";

    /**
     * Method constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if(isset($values['value'])){
            $this->type = strtoupper($values['value']);
        }
        $this->type = in_array($this->type, ["GET", "POST", "AJAX_GET", "AJAX_POST"]) ? $this->type : "ALL";
    }

    public function process($class, string $target = null, string $targetType = null)
    {
        // TODO: Implement process() method.
        if(strtolower($this->type)=="all"){
            return;
        }
        $client = Container::getClient();
        $list = explode("_", $this->type);
        if(in_array($client->method, $list)){
            $ajax = in_array("AJAX", $list);
            if(!$ajax || ($ajax && $client->ajax)){
                return;
            }
        }
        //未通过
        response(1, 'distrust request method');
    }
}