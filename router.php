<?php namespace PHPReflectionRouter;

class Exception404 extends \Exception {
}

/**
 * Class Router
 * @package PHPReflectionRouter
 *
 * example:
 *  PHPReflectionRouter\Router::map(function(){return Array('Index','doIndex');}, $_REQUEST);
 */
class Router {
    /**
     * @param $handlerProvider 函数闭包，返回array($className, $methodName)
     * @param $params 参数池
     * @return mixed
     * @throws Exception404
     */
    static public function map($handlerProvider, $params) {
        list($className, $methodName) = call_user_func_array($handlerProvider, array());

        if (!class_exists($className)) {
            throw new Exception404("class $className not found", 404);
        }

        $class = new $className;

        if (!method_exists($class, $methodName)) {
            throw new Exception404("method $className::$methodName not found", 404);
        }

        $args = array();
        $methodReflection = new \ReflectionMethod($className, $methodName);
        foreach ($methodReflection->getParameters() as $param) {
            $default = null;
            $name = $param->getName();
            if ($param->isDefaultValueAvailable()) {
                $default = $param->getDefaultValue();
            }
            if (isset($params[$name])) {
                $args[$name] = $params[$name];
            } else {
                $args[$name] = $default;
            }
        }

        return call_user_func_array(array($class, $methodName), $args);
    }
}