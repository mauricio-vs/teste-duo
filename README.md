# Teste Duo

## Objetivo
Retornar uma lista com a identificação dos indicadores e sua latitude e longitude.

## Execução
Ao analisar a tarefa, percebi que poderia fazer apenas uma query para retornar o resultado desejado, evitando fazer iterações desnecessárias em PHP ou outras querys. Resolvi também desenvolver uma interface simples para mostrar o resultado, utilizando o CSS do Framework Materialize.

## Atualização
A primeira versão do teste foi feito utilizando PDO, mas devido a simplicidade e necessidade de performance em detrimento de robustez, refiz a conexão utilizando MySQLi. Também fechei a conexão, algo que, equivocadamente, não tinha feito na conexão com PDO.