<?php
namespace Inphp;

use Inphp\Util\Arr;
use Inphp\Util\File;

/**
 * 配置
 * Class Config
 * @package Inphp
 */
class Config {

    private static $configs = [];

    /**
     * 加载配置
     */
    public static function loadConfig(){

    }

    /**
     * 加载所有配置
     * @param string $path
     * @return array|bool
     */
    private static function loadPathConfig(string $path){
        $files = File::getFiles(self::get("define.configs")."/{$path}", "php", null);
        if(!empty($files)){
            $configs = [];
            foreach ($files as $file){
                $config = include $file['path'];
                //去看后尾 .php 4个字符，得到文件名
                $name = substr($file['filename'], 0, -4);
                $configs[$name] = $config;
            }
            return self::set($path, Arr::parseValue($configs, self::$configs));
        }
        return false;
    }

    /**
     * 保存配置
     * @param string $name
     * @param array $values
     * @return array
     */
    public static function set(string $name, array $values){
        if(isset(self::$configs[$name])){
            unset(self::$configs[$name]);
        }
        self::$configs[$name] = $values;
        return $values;
    }

    /**
     * 获取配置
     * @param string $path
     * @return string|array|null
     */
    public static function get(string $path = null){
        if(null == $path){
            return self::$configs;
        }else{
            $pathArr = explode(".", $path);
            if(!isset(self::$configs[$pathArr[0]])){
                if($values = self::loadPathConfig($pathArr[0])){
                    return count($pathArr)>1 ? Arr::get($values, join(".", array_slice($pathArr, 1))) : $values;
                }else{
                    return null;
                }
            }else{
                return Arr::get(self::$configs, $path);
            }
        }
    }
}