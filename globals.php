<?php
// Inicia ou resume uma sessão existente no servidor.
session_start();

$BASE_URL = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI'] . '?') . '/';
