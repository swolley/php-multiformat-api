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
        $definition .= 'use Api\Core\RouteModel;' . PHP_EOL;
        $definition .= 'use Api\Core\Request;' . PHP_EOL;
        $definition .= 'use Api\Core\HttpStatusCode;' . PHP_EOL . PHP_EOL;

        //starts defining class
        $definition .= "class {$name} extends {$extends->name}" . /*(!is_null($implements) ?:"") .*/' {' . PHP_EOL . PHP_EOL;
        
            //constructor
            $definition .= static::defineConstructor($extends);
        
            //abstract methods
            $definition .= static::defineDepencenceMethods($extends, ReflectionMethod::IS_ABSTRACT);

            //interface methods
            /*if( !is_null($implements)) {
                $definition .= static::defineDepencenceMethods($implements);
            }*/
    
        $definition .= '}' . PHP_EOL;
        
        return $definition;
    }

    /**
     * always calls parent constructor from inherited classes
     * @return  string  $definition stringified php code of constructor
     **/
    private static function defineConstructor(ReflectionClass &$inherited_class) : string {
        $inherited_constructor = $inherited_class->getConstructor();
        $definition = "\tpublic function __construct(" . static::defineParams($inherited_constructor) . ') {' . PHP_EOL;
            $definition .= "\t\tparent::__construct(" . static::defineParams($inherited_constructor, FALSE) . ');' . PHP_EOL;
        $definition .= "\t}" . PHP_EOL . PHP_EOL;

        return $definition;
    }

    /**
     * defines all methods contained in the parent class
     * @param   ReflectionClass     $dependenceName     parent class name
     * @param   integer             $dependence_modifier defines if implementing abstract class or not
     * @return  string                                  stringified php code of parent methods
     **/
    private static function defineDepencenceMethods(ReflectionClass &$inherited_class, int $dependence_modifier = 0) :string {
        return array_reduce(
            $dependence_modifier === ReflectionMethod::IS_ABSTRACT ? 
                $inherited_class->getMethods(ReflectionMethod::IS_ABSTRACT) : 
                $inherited_class->getMethods(), 
            function($definition, $method ) {
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
        $reflection_method = (new ReflectionMethod($method->class, $method->name));

        //removes abstract from modifier because implemented
        $modifiers =  str_replace('abstract ', '', implode(' ', Reflection::getModifierNames($reflection_method->getModifiers())));

        //defines method
        $definition = "\t" . $modifiers . " function {$method->name}(";
            //defines parameters
            $definition .= static::defineParams($method) . ')';
            //appends return type if exists
            $definition .= $reflection_method->hasReturnType() ? " : {$reflection_method->getReturnType()} " : ' ';
            $definition .= '{' . PHP_EOL;
            //implementes void method
            $definition .= "\t\tthrow new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);" . PHP_EOL;
        $definition .= "\t}" . PHP_EOL . PHP_EOL;

        return $definition;
    }

    /**
     * defines specified method's parameters list with default values if found
     * @param   ReflectionMethod    $method         parent method
     * @param   string              $default_values  optionally add default values to params if found
     * @return  string                              stringified php code of parameters
     **/
    private static function defineParams(ReflectionMethod &$method, bool $default_values = TRUE) : string {
        return trim(implode(', ', array_map(function($param) use ($default_values) {
            return implode(' ', [
                'paramType' => $param->hasType() ? $param->getType() : '',
                'isPassedByReference' => $param->isPassedByReference() ? '&' : '',
                'paramName' => '$'.$param->getName(),
                'paramIsOptional' => $param->isOptional() ? ' = ' : '',
                'paramDefaultValue' => $default_values && $param->isDefaultValueAvailable() ? (static::formatParamDefaultValue($param->getDefaultValue())) : ''
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