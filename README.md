# conect-PDO


![Maintainer](https://img.shields.io/badge/maintainer-Otoniel%20Ferreira-informational)
![PHP](https://img.shields.io/badge/PHP-%5E5.6-blueviolet)
![VERSION](https://img.shields.io/badge/Versão-1.0.1-success)
![BUILD](https://img.shields.io/badge/build-pass-success)


Class de conexão de banco de dados, utilizando PDO. `PHP`.

### Atenção

> Esta class é serve para conectar com DB -  `Oracle` e `Mysqul (mariaDB)`
> mais pode ser adaptada para outras bancos ex. `PostgreSQL etc.` 


<br>

### Arquivo de configuração

Copie e renomeie o arquivo ``config.php.example`` para ``config.php``

### Download

Só baixar e colocar na Raiz do seu projeto php

usar a sintax para conectar crie uma variavel $DB =  new Database();
 
Ex: Select = `$SQL = " select.. .... . "; $DB->query( $SQL );`
<br><br>
Ex: Update = `$dado =  [
                        'NAME' => $name,
                        'EMAIL' => $email
                      ];
            $update = $DB->update(table,idfield,$dados);`
<br><br>
EX: Insert = `$dado =  [
                        'NAME' => $name,
                        'EMAIL' => $email
                        ];
            $insert = $DB->save(table,$dados);`
<br><br>
EX: Delete = `$delete = $DB->delete(tabela,colun);`
      