<?php
// Inicia ou resume uma sessão existente no servidor.
session_start();

// Obtém o protocolo usado (http ou https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Obtém o nome do host (ex: ani-book.000webhostapp.com)
$host = $_SERVER['SERVER_NAME'];

// Obtém o diretório atual (ex: /public_html/)
$directory = dirname($_SERVER['REQUEST_URI']);

// Remove a barra final se ela existir no diretório
$directory = rtrim($directory, '/');

// Cria a URL base
$BASE_URL = $protocol . $host . $directory . '/';
?>
