<?php
// Configurações de tempo e variáveis (sem cores para economizar log)
date_default_timezone_set("America/Sao_Paulo");

// Função de resposta para o Render não dar erro 502
echo "Bot Executando... ";

function post_2($url, $ua, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $ua);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}

// Lógica de uma execuçao

$ua = [
    "Content-Type: application/json",
    "Accept: application/json",
    "User-Agent: okhttp/4.11.0",
    "Connection: keep-alive"
];

// O e-mail deve ser uma variável de ambiente no Render por segurança
$email = getenv('USER_EMAIL') ?: "pgreceber17@gmail.com"; 
$data = json_encode(["email" => $email]);
$url = "https://pixassistindo.thm.app.br/backend/buscar_usuario.php";

$res = post_2($url, $ua, $data);
$resData = json_decode($res, true);

if (isset($resData['count']) && $resData['count'] == true) {
    $balance = $resData['results']['0']['saldo'];
    $progresso = $resData['results']['0']['progresso_missao2'];
    $meta = $resData['results']['0']['meta_missao2'];

    if ($progresso >= $meta) {
        echo "Meta já atingida.";
        exit;
    }

    // Lógica do valor randômico
    $valor = number_format(mt_rand(300, 800) / 100000, 5, '.', '');
    $update = $valor + $balance;

    // Atualizar saldo
    $data_up = json_encode(["id" => "13084", "saldo" => $update, "views" => 1]);
    $url_up = "https://pixassistindo.thm.app.br/backend/atualizar_usuario.php";
    $res_up = post_2($url_up, $ua, $data_up);
    
    // Atualizar missão
    $data_ms = json_encode(["email" => $email, "valor_pago" => $valor]);
    $url_ms = "https://pixassistindo.thm.app.br/backend/atualizar_missao.php";
    post_2($url_ms, $ua, $data_ms);

    echo "Sucesso! Novo Saldo: " . $update;
} else {
    echo "Erro ao buscar usuário. Resposta da API: " . $res;
    // Se o $res vier vazio, o Render está sendo bloqueado antes de chegar na API.
}
