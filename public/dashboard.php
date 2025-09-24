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
    <link rel="icon" href="/sistemaglpi/img/chesiquimica-logo-png.png" type="image/png" />
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center font-sans">

    <div class="bg-white p-8 rounded shadow-md w-full max-w-md text-center">
        <h1 class="text-3xl font-bold mb-4">Bem-vindo ao Dashboard!</h1>
        <p class="text-gray-700 mb-6">Você está logado como:</p>
        <p class="text-blue-600 font-semibold text-lg mb-6"><?= $email ?></p>
        <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Sair</a>
    </div>

</body>
</html>
