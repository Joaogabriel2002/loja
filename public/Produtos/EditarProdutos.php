<?php
// 1. Incluímos os arquivos essenciais
require_once __DIR__ . '/../../App/Config/conexao.php';
require_once __DIR__ . '/../../App/Models/Produtos.php';

// 2. Criamos a conexão com o banco
$conexao = new Conexao();
$pdo = $conexao->getConn();

// 3. Pegamos o ID do produto da URL e validamos
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    // Se o ID for inválido, redireciona para a listagem
    header("Location: ListarProdutos.php?status=erro&msg=" . urlencode("ID do produto inválido!"));
    exit();
}

// 4. Buscamos os dados do produto no banco
$produto = Produto::findById($pdo, $id);
if (!$produto) {
    // Se o produto não for encontrado, redireciona para a listagem
    header("Location: ListarProdutos.php?status=erro&msg=" . urlencode("Produto não encontrado!"));
    exit();
}

$caminhoBaseImagem = '../uploads/produtos/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - <?php echo htmlspecialchars($produto['nome']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-700">Editar Produto</h2>
        
        <!-- Formulário aponta para o novo controller de edição -->
        <form action="..\..\App\Controller\editar_produto.php" method="POST" enctype="multipart/form-data">
            
            <!-- CAMPO OCULTO: Essencial para saber qual produto atualizar -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($produto['id']); ?>">

            <div class="mb-4">
                <label for="nome" class="block text-gray-600 font-medium mb-2">Nome do Produto</label>
                <input type="text" id="nome" name="nome" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="preco_custo" class="block text-gray-600 font-medium mb-2">Preço de Custo (R$)</label>
                    <input type="number" id="preco_custo" name="preco_custo" step="0.01" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($produto['preco_custo']); ?>">
                </div>
                <div>
                    <label for="preco_venda" class="block text-gray-600 font-medium mb-2">Preço de Venda (R$)</label>
                    <input type="number" id="preco_venda" name="preco_venda" step="0.01" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($produto['preco_venda']); ?>" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                 <div>
                    <label for="quantidade_estoque" class="block text-gray-600 font-medium mb-2">Quantidade em Estoque</label>
                    <input type="number" id="quantidade_estoque" name="quantidade_estoque" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($produto['quantidade_estoque']); ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="id_categoria" class="block text-gray-600 font-medium mb-2">Categoria</label>
                <select id="id_categoria" name="id_categoria" class="w-full px-4 py-2 border rounded-lg bg-white">
                    <option value="">Selecione uma categoria</option>
                    <option value="1" <?php echo ($produto['id_categoria'] == 1) ? 'selected' : ''; ?>>Periféricos</option>
                    <option value="2" <?php echo ($produto['id_categoria'] == 2) ? 'selected' : ''; ?>>Hardware</option>
                    <option value="3" <?php echo ($produto['id_categoria'] == 3) ? 'selected' : ''; ?>>Software</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="descricao" class="block text-gray-600 font-medium mb-2">Descrição</label>
                <textarea id="descricao" name="descricao" rows="4" class="w-full px-4 py-2 border rounded-lg"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
            </div>

            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Imagens do Produto</h3>
                <p class="text-sm text-gray-500 mb-4">Envie um novo arquivo apenas para substituir a imagem atual.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Imagem 1 -->
                    <div>
                        <label for="imagem1" class="block text-gray-600 font-medium mb-2">Imagem Principal</label>
                        <?php if (!empty($produto['imagem1'])): ?>
                            <img src="<?php echo $caminhoBaseImagem . htmlspecialchars($produto['imagem1']); ?>" class="w-full h-24 object-cover rounded-md mb-2 border">
                        <?php endif; ?>
                        <input type="file" id="imagem1" name="imagem1" class="w-full text-sm">
                    </div>
                    <!-- Imagem 2 -->
                    <div>
                        <label for="imagem2" class="block text-gray-600 font-medium mb-2">Imagem 2</label>
                         <?php if (!empty($produto['imagem2'])): ?>
                            <img src="<?php echo $caminhoBaseImagem . htmlspecialchars($produto['imagem2']); ?>" class="w-full h-24 object-cover rounded-md mb-2 border">
                        <?php endif; ?>
                        <input type="file" id="imagem2" name="imagem2" class="w-full text-sm">
                    </div>
                    <!-- Imagem 3 -->
                    <div>
                        <label for="imagem3" class="block text-gray-600 font-medium mb-2">Imagem 3</label>
                         <?php if (!empty($produto['imagem3'])): ?>
                            <img src="<?php echo $caminhoBaseImagem . htmlspecialchars($produto['imagem3']); ?>" class="w-full h-24 object-cover rounded-md mb-2 border">
                        <?php endif; ?>
                        <input type="file" id="imagem3" name="imagem3" class="w-full text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-4">
                 <a href="ListarProdutos.php" class="w-full md:w-1/2 bg-gray-400 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-500 transition duration-300 text-center">
                    Cancelar
                </a>
                <button type="submit" class="w-full md:w-1/2 bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</body>
</html>
