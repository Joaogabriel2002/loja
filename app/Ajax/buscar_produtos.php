<?php
// Arquivo: App/Ajax/buscar_produtos.php

header('Content-Type: application/json');

require_once __DIR__ . '/../Config/conexao.php';
require_once __DIR__ . '/../Models/Produtos.php';

$termo = $_GET['term'] ?? '';

if (strlen($termo) < 2) {
    echo json_encode([]);
    exit();
}

$conexao = new Conexao();
$pdo = $conexao->getConn();

try {
    $produtos = Produto::buscarPorTermo($pdo, $termo);
    echo json_encode($produtos);
} catch (Exception $e) {
    // Em caso de erro, retorna um array vazio
    echo json_encode([]);
}
