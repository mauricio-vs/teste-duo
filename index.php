<?php
    $time = microtime(1);
    try {
        $connection = new PDO("mysql:host=localhost;port=3306;dbname=duo_db","root","");
        $query = $connection->query("
            SELECT 
                indicadores_respostas.resposta_text AS indicador,
                COALESCE(
                    (
                        SELECT indicadores_respostas.resposta_text 
                        FROM indicadores_respostas
                        LEFT JOIN indicadores_secoes_itens
                            ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Latitude'
                            AND indicadores_respostas.id_indicador = indicadores.id
                    ),
                    (
                        SELECT cidades.latitude
                        FROM cidades
                        INNER JOIN indicadores_respostas
                        LEFT JOIN indicadores_secoes_itens
                            ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Cidade'
                            AND indicadores.id = indicadores_respostas.id_indicador
                            AND cidades.cidades_id = CAST(indicadores_respostas.resposta_text AS UNSIGNED)
                    )
                ) AS latitude,
                COALESCE(
                    (
                        SELECT indicadores_respostas.resposta_text 
                        FROM indicadores_respostas
                        LEFT JOIN indicadores_secoes_itens
                            ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Longitude'
                            AND indicadores_respostas.id_indicador = indicadores.id
                    ),
                    (
                        SELECT cidades.longitude
                        FROM cidades
                        INNER JOIN indicadores_respostas
                        LEFT JOIN indicadores_secoes_itens
                        ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Cidade'
                            AND indicadores.id = indicadores_respostas.id_indicador
                            AND cidades.cidades_id = CAST(indicadores_respostas.resposta_text AS UNSIGNED)
                    )
                ) AS longitude
            FROM indicadores
            LEFT JOIN indicadores_respostas
                ON indicadores_respostas.id_indicador = indicadores.id
            LEFT JOIN indicadores_secoes_itens
                ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
            WHERE indicadores_secoes_itens.titulo = 'Nome da capacitação'
                OR (indicadores_secoes_itens.titulo = 'Instituição')
        ");
        $data = $query->fetchAll();
    } catch(PDOException $error) {
        echo 'Erro de conexão com banco de dados: ' . $error->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="favicon.png" rel="shortcut icon" type="image/x-icon">
    <title>Teste Duo</title>
</head>
<body>
    <main class="container">
        <h1>Teste Duo</h1>
        <?php
            if($data) {
                echo "<table class='striped'><thead><tr><th>Indicador</th>"
                    ."<th>Latitude</th><th>Longitude</th></tr></thead><tbody>";
                foreach($data as $item) {
                    echo "<tr><td>{$item["indicador"]}</td><td>{$item["latitude"]}</td>"
                        ."<td>{$item["longitude"]}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                "<p class='center-align'>Nenhum indicador encontrado.</p>";
            }
            echo "<p class='center-align'>Tempo de execução (Aproximadamente): "
                .(1000 * (microtime(1) - $time))."ms</p>";
        ?>
    </main>
</body>
</html>