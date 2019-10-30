<?php

namespace wendrpatrck\cpblogs\helpers;

use SplFileObject;

class Pointers
{
    protected static $ERROR_TYPES = [
        E_ERROR => [            
            'name' => 'PHP Fatal Error',
            'severity' => 'error',
            'key' => 'E_ERROR'
        ],

        E_WARNING => [
            'name' => 'PHP Warning',
            'severity' => 'warning',
            'key' => 'E_WARNING'
        ],

        E_PARSE => [
            'name' => 'PHP Parse Error',
            'severity' => 'error',
            'key' => 'E_PARSE'
        ],

        E_NOTICE => [
            'name' => 'PHP Notice',
            'severity' => 'info',
            'key' => 'E_NOTICE'
        ],

        E_CORE_ERROR => [
            'name' => 'PHP Core Error',
            'severity' => 'error',
            'key' => 'E_CORE_ERROR'
        ],

        E_CORE_WARNING => [
            'name' => 'PHP Core Warning',
            'severity' => 'warning',
            'key' => 'E_CORE_WARNING'
        ],

        E_COMPILE_ERROR => [
            'name' => 'PHP Compile Error',
            'severity' => 'error',
            'key' => 'E_COMPILE_ERROR'
        ],

        E_COMPILE_WARNING => [
            'name' => 'PHP Compile Warning',
            'severity' => 'warning',
            'key' => 'E_COMPILE_WARNING'
        ],

        E_USER_ERROR => [
            'name' => 'User Error',
            'severity' => 'error',
            'key' => 'E_USER_ERROR'
        ],

        E_USER_WARNING => [
            'name' => 'User Warning',
            'severity' => 'warning',
            'key' => 'E_USER_WARNING'
        ],

        E_USER_NOTICE => [
            'name' => 'User Notice',
            'severity' => 'info',
            'key' => 'E_USER_NOTICE'
        ],

        E_STRICT => [
            'name' => 'PHP Strict',
            'severity' => 'info',
            'key' => 'E_STRICT'
        ],

        E_RECOVERABLE_ERROR => [
            'name' => 'PHP Recoverable Error',
            'severity' => 'error',
            'key' => 'E_RECOVERABLE_ERROR'
        ],

        E_DEPRECATED => [
            'name' => 'PHP Deprecated',
            'severity' => 'info',
            'key' => 'E_DEPRECATED'
        ],

        E_USER_DEPRECATED => [
            'name' => 'User Deprecated',
            'severity' => 'info',
            'key' => 'E_USER_DEPRECATED'
        ],
    ];
   
    // busca por uma chave dentro da lista de typos da aplicação
    public static function getInTypes($code, $key){
        if (array_key_exists($code, static::$ERROR_TYPES)) {
            return static::$ERROR_TYPES[$code][$key];
        }

        return 'undefined';
    }

    //pegar a chave de api no arquivo .env
    public static function getApiKey(){
        return env('LOGGER_APIKEY', 'undefined');
    }

    //pegar a chave de api no arquivo .env
    public static function getEnvironmentName(){
        return env('LOGGER_ENVIRONMENT', 'dev');
    }
    
    public static function getMachineName(){
        return env('LOGGER_MACHINE_LABEL', self::getLocalHostName());
    }
    
    public static function getLocalHostName(){
        return (gethostname()??'machine');
    }

    //carrega o estágio da aplicação e o nome da máquina
    public static function getStage(){
        return self::getEnvironmentName().'@'.self::getMachineName();
    }

    public static function getEnvironmentState(){
        return env('APP_DEBUG', true)?'debug':'production';
    }

    //carrega o espaço livre atual em disco e exibe simplificado
    public static function getFreeSpace(){
        try {
            $bytes = disk_free_space("."); 
            $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
            $base = 1024;
            $class = min((int)log($bytes , $base) , count($si_prefix) - 1);     

            return sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . ' free';

        } catch (\Exception $ex) {
            return 'unavailable';
        }
             
    }
}