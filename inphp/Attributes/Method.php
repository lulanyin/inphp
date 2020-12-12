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
namespace Inphp\Attributes;

use Doctrine\Common\Annotations\Annotation\Target;
use Inphp\Annotation\IAnnotation;
use Inphp\Service\Context;

/**
 * @Annotation
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
        $client = Context::getClient();
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