<?php
// Arquivo: App/Controller/finalizar_venda.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../Config/conexao.php';
require_once __DIR__ . '/../Models/Venda.php';

// Lê o corpo da requisição JSON
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$itensCarrinho = $data['carrinho'] ?? [];
$valorTotal = $data['total'] ?? 0;

if (empty($itensCarrinho) || $valorTotal <= 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio ou valor inválido.']);
    exit();
}

$conexao = new Conexao();
$pdo = $conexao->getConn();

try {
    $idVenda = Venda::salvarVenda($pdo, $itensCarrinho, $valorTotal);
    echo json_encode(['sucesso' => true, 'mensagem' => 'Venda #' . $idVenda . ' finalizada com sucesso!', 'idVenda' => $idVenda]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
