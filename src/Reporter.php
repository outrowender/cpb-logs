<?php

namespace wendrpatrck\cpblogs;

use wendrpatrck\cpblogs\helpers\Code;
use wendrpatrck\cpblogs\helpers\Pointers;

use Exception;
use Illuminate\Database\QueryException;

class Reporter
{
    //reporta todos os exceptions que passam por log::listen()
    public static function reportException($ex){
        static::newReport('', '', $ex, false, null);  
    }

    public static function reportMessage($type, $message){
        //static::createReport($ex, false, null);  
    }

    //Quando é preciso reportar um erro manualmente
    public static function catchError($ex, $data = null){        
        static::newReport('', '', $ex, true, $data);
    }

    protected static function newReport($level, $message, $ex, bool $handled, $moreData){
        if(!isset($ex)){
            error_log('cpblogs: Nenhum dado para reportar. Abortando...');
            return;
        }

        try {            
            //monta uma coleção de dados usando os ponteiros de projeto  
            $details = [                
                'app' => [
                    'type'=>'http',
                    'stage' => Pointers::getEnvironmentState(),
                    'environment' => Pointers::getEnvironmentName()
                ],
                'request' => [
                    'method' => $_SERVER['REQUEST_METHOD'],
                    'url' => $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                    'userAgent' => $_SERVER['HTTP_USER_AGENT']
                ],
                'server' => [
                    'software' => $_SERVER['SERVER_SOFTWARE'],
                    'name' => $_SERVER['SERVER_NAME'],
                    'port' => $_SERVER['SERVER_PORT'],
                    'protocol' => $_SERVER['SERVER_PROTOCOL'],
                    'storage' => Pointers::getFreeSpace()
                ],
                'stacktrace' => [                    
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'code' => Code::getCode($ex->getFile(), $ex->getLine(), 7),
                    'trace' => Code::formatTrace($ex->getTrace())
                ],
                'details' => [
                    'title' => $ex->getMessage(),
                    'type' => get_class($ex)
                ]              
            ];

            //se for uma query de banco
            if($ex instanceof QueryException){

                //adiciona detalhes ao log 
                $details['queryException'] = [
                    'sql' => $ex->getSql(),
                    'bindings' => implode(", ", $ex->getBindings())
                ];

                //se for um problema de pdo, adiciona detalhes
                if($ex->getPrevious() instanceof \PDOException){
                    $details['queryException']['pdoMessage'] = $ex->getPrevious()->getMessage();                 
                }
            }

            //se forem passados mais dados na chamada manual
            if($moreData){
                $details['info'] = $moreData;
            }

            $content = [
                'handled' => $handled,
                'title' => substr($ex->getMessage(),0,255),
                'level' => $ex->getCode(),
                'stage' => Pointers::getStage(),
                'details' => $details            
            ];
            
            //verifica se a aplicação deve enviar logs em modo debug
            $sendDebug = (config('cpblogs.reportInDebug') == true);
            //captura o tipo de logs que devem ser ignorados
            $ignoreTypes = (array) config('cpblogs.ignore');

            //verifica se logs em debug serão enviados
            if($sendDebug == false && Pointers::getEnvironmentState() == 'debug'){
                return;
            }

            //nome de ambiente setado como local não envia logs
            if(Pointers::getEnvironmentName() == 'local'){
                return;
            }

            //procura o tipo de erro que deve ser ignorado no arquivo config. Se encontra, o erro não é reportado
            $ignoreType = in_array($ex->getCode(), $ignoreTypes);

            if($ignoreType){
                return;
            }

            //se todos os dados forem coletados com sucesso, tenta enviar a requisição
            static::send($content);

        } catch (\Exception $ex) {
            error_log('cpblogs: Erro ao coletar dados para o envio: '.$ex->getMessage());
        }

    }

    //envia a requisição via http para o servidor indicado em config/cpblogs
    protected static function send($req){

        try {
            
            //pega a chave de api no arquivo .env
            $apiKey = Pointers::getApiKey();

            //Se a chave de api não for definida
            if($apiKey == 'undefined'){
                return;
            }

            //le a config do servidor 
            $url = config('cpblogs.server').$apiKey;
       
            //carrega os cabeçalhos da request
            $options = array(
                'http' => array(
                'header'  => "Content-type: application/json",
                'method'  => 'PUT',
                'content' => json_encode($req)
            ));
    
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
    
            if ($result === FALSE) { 
                error_log('cpblogs: Erro ao enviar dados para o servidor'); 
            }

        } catch (\Exception $ex) {
            error_log('cpblogs: Ocorreu um erro ao enviar a requisição: '. $ex->getMessage());
        }
    }


}
