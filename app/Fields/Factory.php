<?php

namespace App\Fields;

class Factory
{
    /**
     * @param string $key
     * @param string $name
     * @param string $type
     * @param array $config
     * @return mixed
     * @throws \Exception
     */
    public static function generate($key, $name, $type, array $config = [])
    {
        $nameSpace = self::class;
        $nameSpace = explode('\\', $nameSpace);
        array_pop($nameSpace);
        $nameSpace = implode('\\', $nameSpace);
        $class     = "\\{$nameSpace}\\{$type}";

        if (!class_exists($class)) {
            throw new \Exception("Invalid field type {$type}");
        }

        return (new $class($key, $name, $config));
    }

    /**
     * @return array
     */
    public static function getFieldTypes()
    {
        return [
            'ID',
            'Checkbox',
            'Date',
            'Dropdown',
            'File',
            'IP',
            'Password',
            'Radio',
            'Status',
            'Text',
            'Textarea',
        ];
    }
}
