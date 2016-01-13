<?php

namespace App\Fields;

class Factory
{
    /**
     * @param string $name
     * @param string $type
     * @param array $config
     * @return mixed
     * @throws \Exception
     */
    public static function generate($name, $type, array $config = [])
    {
        $nameSpace = self::class;
        $nameSpace = explode('\\', $nameSpace);
        array_pop($nameSpace);
        $nameSpace = implode('\\', $nameSpace);
        $class     = "\\{$nameSpace}\\{$type}";

        if ( ! class_exists($class))
        {
            throw new \Exception("Invalid field type {$type}");
        }

        return (new $class($name, $config));
    }

    public static function getFieldTypes()
    {
        return [
            'Checkbox',
            'Date',
            'File',
            'Radio',
            'Text',
            'Textarea',
        ];
    }
}
