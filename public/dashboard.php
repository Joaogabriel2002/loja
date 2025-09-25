<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$email = htmlspecialchars($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link rel="icon" href="/sistemaglpi/img/chesiquimica-logo-png.png" type="image/png" /> -->
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">

    <div class="flex w-full max-w-4xl gap-6">

        <!-- Informações do Usuario -->
        <!-- <div class="bg-white p-8 rounded shadow-md w-1/2 text-center">
            <h1 class="text-3xl font-bold mb-4">Bem-vindo ao Dashboard!</h1>
            <p class="text-gray-700 mb-6">Você está logado como:</p>
            <p class="text-blue-600 font-semibold text-lg mb-6"><?= $email ?></p>
            <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Sair</a>
        </div> -->

        <!-- Div vazia para preencher depois -->
        <div class="bg-white p-8 rounded shadow-md w-full md:w-1/2 text-center">
            <h2 class="text-2xl font-bold mb-4">Opções</h2>
            <p class="text-gray-500 mb-6">Escolha uma ação:</p>

            <div class="flex flex-col gap-3">
                <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Botão 1</button>
                <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Botão 2</button>
                <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Botão 3 </button>
            </div>
        </div>


    </div>

</body>
</html>
