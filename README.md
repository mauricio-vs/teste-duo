# Teste Duo

## Objetivo
Retornar uma lista com a identificação dos indicadores e sua latitude e longitude.

## Execução
Ao analisar a tarefa, percebi que poderia fazer apenas uma query para retornar o resultado desejado, evitando fazer iterações desnecessárias em PHP ou outras querys. Resolvi também desenvolver uma interface simples para mostrar o resultado, utilizando o CSS do Framework Materialize.

## Atualização
A primeira versão do teste foi feito utilizando PDO, mas devido a simplicidade e necessidade de performance em detrimento de robustez, refiz a conexão utilizando MySQLi. Também fechei a conexão, algo que, equivocadamente, não tinha feito na conexão com PDO.

## Atualização v2
Analisando o código percebi que um inner join na tabela indicadores_respostas não seria performático pelo fato da tabela em produção ter 650.000 registros aproximadamente. Não percebi antes pois estou testando em uma base de teste, com poucos registros. Refiz o código iterando sobre a lista de indicadores e fazendo selects adicionais para selecionar a latitude e longitude.