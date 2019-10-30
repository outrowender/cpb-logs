<?php

namespace wendrpatrck\cpblogs\helpers;

use SplFileObject;

class Code
{
    //pega o código atual, lendo os arquivos e retornando as linhas marcadas
    public static function getCode($path, $line, $numLines)
    {
        if (empty($path) || empty($line) || !file_exists($path)) {
            return;
        }

        try {
            $file = new SplFileObject($path);
            $file->seek(PHP_INT_MAX);

            $bounds = static::getBounds($line, $numLines, $file->key() + 1);

            $code = [];

            $file->seek($bounds[0] - 1);
            while ($file->key() < $bounds[1]) {
                $code[$file->key() + 1] = rtrim(substr($file->current(), 0, 200));
                $file->next();
            }

            return $code;
        } catch (Exception $ex) {
            erro_log('cpblogs: não foi possível carregar o código indicado. '.$ex->getMessage());
        }
    }

    public static function getBounds($line, $num, $max)
    {
        $start = max($line - floor($num / 2), 1);

        $end = $start + ($num - 1);

        if ($end > $max) {
            $end = $max;
            $start = max($end - ($num - 1), 1);
        }

        return [$start, $end];
    }

    //formata o restante do trace para reportar
    public static function formatTrace($trace){
        try {
            return collect($trace)->map(function($item){            
                return [
                    'class' => $item['class']??null,
                    'file' => $item['file']??null,
                    'line' => $item['line']??null,
                    'function' => $item['function']??null,
                    'code' => !isset($item['file'])?null:Code::getCode($item['file'], $item['line'], 7)
                ];
            });
        } catch (\Exception $ex) {
            return [];
        }
 
    }
}