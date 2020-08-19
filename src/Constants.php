<?php

namespace tegic;


use tegic\Exceptions\ConstantsException;
use ReflectionClass;

abstract class Constants
{
    public static function __callStatic($name, $arguments)
    {
        if (!self::startsWith($name, 'get')) {
            throw new ConstantsException('The function is not defined!');
        }

        if (!isset($arguments) || count($arguments) === 0) {
            throw new ConstantsException('The Code is required');
        }

        $code = $arguments[0];
        $name = strtolower(substr($name, 3));
        $class = get_called_class();

        $message = self::collectClass($class)[$code][$name] ?? '';
        array_shift($arguments);

        $count = count($arguments);
        if ($count > 0) {
            if ($count === 1 && is_array($arguments[0])) {
                return sprintf($message, ...$arguments[0]);
            }
            return sprintf($message, ...$arguments);
        }
        return $message;
    }

    public static function collectClass(string $className)
    {

        $ref = new ReflectionClass($className);
        $classConstants = $ref->getReflectionConstants();
        return self::getAnnotations($classConstants);

    }

    public static function getAnnotations(array $classConstants)
    {
        $result = [];
        foreach ($classConstants as $classConstant) {
            $code = $classConstant->getValue();
            $docComment = $classConstant->getDocComment();
            if ($docComment) {
                $result[$code] = self::parse($docComment);
            }
        }

        return $result;
    }

    protected static function parse(string $doc)
    {
        $pattern = '/\\@(\\w+)\\(\\"(.+)\\"\\)/U';
        if (preg_match_all($pattern, $doc, $result)) {
            if (isset($result[1], $result[2])) {
                $keys = $result[1];
                $values = $result[2];

                $result = [];
                foreach ($keys as $i => $key) {
                    if (isset($values[$i])) {
                        $result[self::lower($key)] = $values[$i];
                    }
                }
                return $result;
            }
        }

        return [];
    }

    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }
}