<?php

return [
    
    // server para onde o request deve ser enviado ao detectar um novo log
    // envia uma request PUT para => config('cpblogs.server').'MINHA-CHAVE-DE-API'
    // http://localhost:8000/api/log/MINHA-CHAVE-DE-API
    'server' => 'http://api-log.testecpb.com.br/api/log/',

    // deve enviar logs quando APP_DEBUG=true
    'reportInDebug' => false,

    // tipos de erros que devem ser ignorados no envio segundo a classificação 'severity' de cada um
    // mais tipos podem ser adicionados ao array para serem ignorados
    'ignore' => []
];