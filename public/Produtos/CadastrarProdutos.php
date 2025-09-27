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
        
        <!-- O 'action' agora aponta para o nosso script PHP -->
        <form action="..\..\App\Controller\cadastrar_produto.php" method="POST">
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

            <div class="flex justify-center gap-4">
                <button type="button" onclick="window.location.href='form_cadastro.html'" class="w-full md:w-1/2 bg-gray-400 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-500 transition duration-300">
                    Limpar
                </button>
                <button type="submit" class="w-full md:w-1/2 bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
                    Salvar Produto
                </button>
            </div>
        </form>
    </div>

</body>
</html>
