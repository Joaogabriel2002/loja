<?php
// Ficheiro: App/Ajax/buscar_detalhes_venda.php
header('Content-Type: application/json');
require_once __DIR__ . '/../Config/Conexao.php';
require_once __DIR__ . '/../Models/Venda.php';

$idVenda = $_GET['id'] ?? null;

if (!$idVenda) {
    echo json_encode(['erro' => 'ID do pedido não fornecido.']);
    exit();
}

try {
    $conexao = new Conexao();
    $pdo = $conexao->getConn();
    $itens = Venda::buscarItensPorVendaId($pdo, (int)$idVenda);
    echo json_encode($itens);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar detalhes do pedido.']);
}
