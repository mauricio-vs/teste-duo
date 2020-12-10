<?php
    $time = microtime(1);
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli("localhost","root","","duo_db");
        $query = $connection->query("
            SELECT
                indicadores.id AS indicador_id,
                indicadores_respostas.resposta_text AS indicador_identificacao
            FROM indicadores
            LEFT JOIN indicadores_respostas
                ON indicadores_respostas.id_indicador = indicadores.id
            LEFT JOIN indicadores_secoes_itens
                ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
            WHERE indicadores_secoes_itens.titulo = 'Nome da capacitação'
                OR (indicadores_secoes_itens.titulo = 'Instituição')
        ");
        $data = $query->fetch_all(MYSQLI_ASSOC);
        $array_indicadores = [];
        foreach($data as $indicador) {
            $indicador_query = $connection->query("
                    SELECT indicadores_respostas.resposta_text 
                    FROM indicadores_respostas
                    LEFT JOIN indicadores_secoes_itens
                        ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                    WHERE (indicadores_secoes_itens.titulo = 'Latitude'
                        OR indicadores_secoes_itens.titulo = 'Longitude')
                        AND indicadores_respostas.id_indicador =
                            {$indicador['indicador_id']}
                    LIMIT 2
                ");
            $coordenadas = $indicador_query->fetch_all(MYSQLI_ASSOC);
            $indicador_query->free_result();
            if(empty($coordenadas)) {
                $indicador_query = $connection->query("
                    SELECT cidades.latitude, cidades.longitude
                    FROM cidades
                    LEFT JOIN indicadores_respostas
                        ON indicadores_respostas.id_indicador = {$indicador['indicador_id']}
                    LEFT JOIN indicadores_secoes_itens
                        ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                    WHERE indicadores_secoes_itens.titulo = 'Cidade'
                        AND cidades.cidades_id =
                            CAST(indicadores_respostas.resposta_text AS UNSIGNED)
                    LIMIT 1
                ");
                $coordenadas = $indicador_query->fetch_all(MYSQLI_ASSOC);
                $indicador_query->free_result();
                $array_indicadores[] = [
                    'indicador' => $indicador['indicador_identificacao'],
                    'latitude' => $coordenadas[0]['latitude'],
                    'longitude' => $coordenadas[0]['longitude']
                ];
            } else {
                $array_indicadores[] = [
                    'indicador' => $indicador['indicador_identificacao'],
                    'latitude' => $coordenadas[0]['resposta_text'],
                    'longitude' => $coordenadas[1]['resposta_text']
                ];
            }
        }
        $query->free_result();
        $connection->close();
    } catch(mysqli_sql_exception $error) {
        echo 'Erro de conexão com banco de dados: '.$error->getMessage();
        exit();
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
            if(!empty($array_indicadores)) {
                echo "<table class='striped'><thead><tr><th>Identificação do indicador</th>"
                    ."<th>Latitude</th><th>Longitude</th></tr></thead><tbody>";
                foreach($array_indicadores as $item) {
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