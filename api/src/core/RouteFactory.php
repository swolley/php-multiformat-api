<?php
namespace Api\Core;
use \Reflection;
use \ReflectionClass;
use \ReflectionMethod;

class RouteFactory {

    /**
     * main create class
     * @param   string  $name       new class name
     * @param   string  $implements optional interface to be implemented
     * @return  string  $definition stringified php code of new class to be saved into new file
     **/
    public static function defineClass(string &$name/*, string $implements = null*/) : string {
        $name = ucfirst($name);
        $extends = new ReflectionClass('Api\Core\RouteModel'); //fixed abstract class for routes
        //$implements = is_null($implements)? $implements : ucfirst($implements);
        
        //namespace and uses
        $definition = 'namespace Api\Routes;' . PHP_EOL;
        $definition .= 'use Api\Core\RouteModel;' . PHP_EOL . PHP_EOL;

        //starts defining class
        $definition .= "class {$name} extends {$extends->name}" . /*(!is_null($implements) ?:"") .*/' {' . PHP_EOL . PHP_EOL;
        
            //constructor
            $definition .= static::defineConstructor($extends);
        
            //abstract methods
            $definition .= static::defineDepencenceMethods($extends, ReflectionMethod::IS_ABSTRACT);

            //interface methods
            /*if(!is_null($implements)) {
                $definition .= static::defineDepencenceMethods($implements);
            }*/
    
        $definition .= '}' . PHP_EOL;
        
        return $definition;
    }

    /**
     * always calls parent constructor from inherited classes
     * @return  string  $definition stringified php code of constructor
     **/
    private static function defineConstructor(ReflectionClass &$inheritedClass) : string {
        $inheritedConstructor = $inheritedClass->getConstructor();
        $definition = "\tpublic function __construct(" . static::defineParams($inheritedConstructor) . ') {' . PHP_EOL;
            $definition .= "\t\tparent::__construct(" . static::defineParams($inheritedConstructor, FALSE) . ');' . PHP_EOL;
        $definition .= "\t}" . PHP_EOL . PHP_EOL;

        return $definition;
    }

    /**
     * defines all methods contained in the parent class
     * @param   ReflectionClass     $dependenceName     parent class name
     * @param   integer             $dependenceModifier defines if implementing abstract class or not
     * @return  string                                  stringified php code of parent methods
     **/
    private static function defineDepencenceMethods(ReflectionClass &$inheritedClass, int $dependenceModifier = 0) :string {
        return array_reduce(
            $dependenceModifier === ReflectionMethod::IS_ABSTRACT ? 
                $inheritedClass->getMethods(ReflectionMethod::IS_ABSTRACT) : 
                $inheritedClass->getMethods(), 
            function($definition, $method){
                //defines single method
                return $definition . static::defineMethod($method);
            }, "");
    }

    /**
     * defines specified method of the parent class with modifiers and return type if found
     * @param   ReflectionMethod    $method     parent method
     * @return  string              $definition stringified php code of passed method
     **/
    private static function defineMethod(ReflectionMethod &$method) : string {
        $reflectionMethod = (new ReflectionMethod($method->class, $method->name));

        //removes abstract from modifier because implemented
        $modifiers =  str_replace('abstract ', '', implode(' ', Reflection::getModifierNames($reflectionMethod->getModifiers())));

        //defines method
        $definition = "\t" . $modifiers . " function {$method->name}(";
            //defines parameters
            $definition .= static::defineParams($method) . ')';
            //appends return type if exists
            $definition .= $reflectionMethod->hasReturnType() ? " : {$reflectionMethod->getReturnType()} " : ' ';
            $definition .= '{' . PHP_EOL;
            //implementes void method
            $definition .= "\t\tthrow new NotImplementedException();" . PHP_EOL;
        $definition .= "\t}" . PHP_EOL . PHP_EOL;

        return $definition;
    }

    /**
     * defines specified method's parameters list with default values if found
     * @param   ReflectionMethod    $method         parent method
     * @param   string              $defaultValues  optionally add default values to params if found
     * @return  string                              stringified php code of parameters
     **/
    private static function defineParams(ReflectionMethod &$method, bool $defaultValues = TRUE) : string {
        return trim(implode(', ', array_map(function($param) use ($defaultValues) {
            return implode(' ', [
                'paramType' => $param->hasType() ? $param->getType() : '',
                'isPassedByReference' => $param->isPassedByReference() ? '&' : '',
                'paramName' => '$'.$param->getName(),
                'paramIsOptional' => $param->isOptional() ? ' = ' : '',
                'paramDefaultValue' => $defaultValues && $param->isDefaultValueAvailable() ? (static::formatParamDefaultValue($param->getDefaultValue())) : ''
            ]);
        }, (new ReflectionMethod($method->class, $method->name))->getParameters())));
    }

    /**
     * parse default value of passed parameter
     * @param   mixed   $params param default value
     * @return  string          stringified php code of default param's value
     **/
    private static function formatParamDefaultValue($param) : string {
        return str_replace(PHP_EOL, "", var_export($param, true));
    }

}