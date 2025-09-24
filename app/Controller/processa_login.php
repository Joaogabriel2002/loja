<?php
session_start();
require_once '..\Models\Usuario.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login'] = "Preencha todos os campos.";
        header('Location: ../../public/index.php');
        exit();
    }

    $usuario = new Usuario();
    $usuario->setEmail($email);
    $usuario->setSenha($senha);

    $resultado = $usuario->login();

    if ($resultado) {
        // Login bem-sucedido
        $_SESSION['usuario_id'] = $resultado['id'];
        $_SESSION['usuario'] = $email; // você pode buscar o nome real depois

        header('Location: ../../public/dashboard.php');
        exit();
    } else {
        $_SESSION['erro_login'] = "E-mail ou senha incorretos.";
        header('Location: ../../public/index.php');
        exit();
    }
} else {
    header('Location: ../../public/index.php');
    exit();
}

?>