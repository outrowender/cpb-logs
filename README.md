# Cpblogs Laravel 🐞

### Esse pacote reporta logs de erros para um servidor remoto

### 1. Para usar esse pacote, faça a instalação via composer:

`composer require wendrpatrck/cpblogs`

### 2. Publique a configuração

`php artisan vendor:publish --provider="wendrpatrck\cpblogs\LogServiceProvider"`

Um arquivo de configuração será publicado em `config\cpblogs.php`

Consulte o arquivo para mais detalhes da configuração local

### 3. Adicione a chave de api gerada para a aplicação no arquivo `.env`

`LOGGER_APIKEY=XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX`

### 4. Defina um ambiente de desenvolvimento

`LOGGER_ENVIRONMENT="dev"`
`LOGGER_MACHINE_LABEL="server-01"`

* Pode se usar qualquer nome para ambiente, mas ao definir como 'local', nenhum log será reportado
* Essa é uma infomação de complemento do log

### 5. Pronto!

#### Integração

* Exceptions não tratadas serão enviadas para o servidor indicado automaticamente quando acontecerem.

Para exceptions tratadas, use o método `catchError`:

```
use wendrpatrck\cpblogs\Reporter;

Reporter::catchError($exception);
```

* É possível enviar dados junto com a requisição adicionando um objeto dentro do método `catchError`:

```
  Reporter::catchError($ex, ['data' => 'Mensagem opcional']);
```
* O arquivo de configuração gerado `config\cpblogs.php` contém 

```
# define o endereço para onde os logs devem ser reportados
'server' => 'http://localhost:8010/api/log/',

# Reporta erros mesmo em debug
'reportInDebug' => false,
    
# Exceptions que devem ser ignorados (ainda não implementado) 
'ignore' => []
```

