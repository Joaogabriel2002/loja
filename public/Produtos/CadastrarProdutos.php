<?php
// 1. Inicializamos as variáveis que vão guardar a mensagem e o estilo do alerta
$mensagem = '';
$classeAlerta = '';

// 2. Verificamos se a URL contém os parâmetros 'status' e 'msg'
if (isset($_GET['status']) && isset($_GET['msg'])) {
    $status = $_GET['status'];
    // Decodificamos a mensagem que veio na URL para exibir corretamente
    $msg = urldecode($_GET['msg']);

    // 3. Com base no status, definimos o conteúdo da variável $mensagem e a classe CSS do alerta
    if ($status === 'sucesso') {
        $mensagem = $msg;
        // Classes do Tailwind para um alerta de sucesso (fundo verde)
        $classeAlerta = 'p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg';
    } elseif ($status === 'erro') {
        $mensagem = $msg;
        // Classes do Tailwind para um alerta de erro (fundo vermelho)
        $classeAlerta = 'p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-700">Cadastrar Novo Produto</h2>
        
        <?php
        // 4. Se a variável $mensagem não estiver vazia, significa que temos algo para exibir.
        if (!empty($mensagem)):
        ?>
        <div class="<?php echo $classeAlerta; ?>" role="alert">
            <?php echo htmlspecialchars($mensagem); // Usamos htmlspecialchars por segurança ?>
        </div>
        <?php endif; ?>

        <!-- ATUALIZAÇÃO IMPORTANTE: Adicionado o enctype para permitir upload de arquivos -->
        <form action="..\..\App\Controller\cadastrar_produto.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="nome" class="block text-gray-600 font-medium mb-2">Nome do Produto</label>
                <input type="text" id="nome" name="nome" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: Teclado Mecânico RGB" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="preco_custo" class="block text-gray-600 font-medium mb-2">Preço de Custo (R$)</label>
                    <input type="number" id="preco_custo" name="preco_custo" step="0.01" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: 150.00">
                </div>
                <div>
                    <label for="preco_venda" class="block text-gray-600 font-medium mb-2">Preço de Venda (R$)</label>
                    <input type="number" id="preco_venda" name="preco_venda" step="0.01" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: 299.90" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="quantidade_estoque" class="block text-gray-600 font-medium mb-2">Quantidade em Estoque</label>
                    <input type="number" id="quantidade_estoque" name="quantidade_estoque" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: 50" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="id_categoria" class="block text-gray-600 font-medium mb-2">Categoria</label>
                <select id="id_categoria" name="id_categoria" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Selecione uma categoria</option>
                    <option value="1">Periféricos</option>
                    <option value="2">Hardware</option>
                    <option value="3">Software</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="descricao" class="block text-gray-600 font-medium mb-2">Descrição</label>
                <textarea id="descricao" name="descricao" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Detalhes do produto..."></textarea>
            </div>

            <!-- NOVA SEÇÃO: Campos para Upload de Imagens -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Imagens do Produto</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="imagem1" class="block text-gray-600 font-medium mb-2">Imagem Principal</label>
                        <input type="file" id="imagem1" name="imagem1" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="imagem2" class="block text-gray-600 font-medium mb-2">Imagem 2</label>
                        <input type="file" id="imagem2" name="imagem2" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="imagem3" class="block text-gray-600 font-medium mb-2">Imagem 3</label>
                        <input type="file" id="imagem3" name="imagem3" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-4">
                 <button type="button" onclick="window.location.href='produtos.php'" class="w-full md:w-1/3 bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition duration-300">
                    Voltar
                </button>
                <button type="button" onclick="window.location.href='CadastrarProdutos.php'" class="w-full md:w-1/3 bg-gray-400 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-500 transition duration-300">
                    Limpar
                </button>
                <button type="submit" class="w-full md:w-1/3 bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
                    Salvar Produto
                </button>
            </div>
        </form>
    </div>

</body>
</html>

