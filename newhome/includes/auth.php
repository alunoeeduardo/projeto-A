<?php
// includes/auth.php
session_start();

// Função para verificar se usuário está logado
function verificarLogin() {
    if(!isset($_SESSION['usuario_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

// Função para verificar se usuário é admin
function verificarAdmin() {
    if(!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'admin') {
        header("Location: ../dashboard.php");
        exit();
    }
}

// Função para verificar se usuário é gerente
function verificarGerente() {
    $tipos_permitidos = ['gerente', 'admin'];
    if(!isset($_SESSION['usuario_tipo']) || !in_array($_SESSION['usuario_tipo'], $tipos_permitidos)) {
        header("Location: ../dashboard.php");
        exit();
    }
}

// Função para fazer logout
function logout() {
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>