<?php
namespace Inphp\Attributes;

use Doctrine\Common\Annotations\Annotation\Target;
use Inphp\Annotation\IAnnotation;
use Inphp\Service\Context;
use Inphp\Service\Http\Response;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * Class View
 * @package Inphp\Attributes
 */
class View implements IAnnotation
{
    /**
     * 定义的模板
     * @var string
     */
    public $template;

    public function __construct(array $values)
    {
        if(isset($values['template'])){
            $this->template = is_string($values['template']) ? $values['template'] : null;
        }elseif(isset($values['value'])){
            $this->template = is_string($values['value']) ? $values['value'] : null;
        }
    }

    public function process($class, string $target = null, string $targetType = null)
    {
        // TODO: Implement process() method.
        $response = Context::getResponse();
        if($response instanceof Response && !empty($this->template)){
            $response->status->view = $this->template;
        }
    }

}