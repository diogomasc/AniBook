<?php

   // Configurações de acesso ao banco de dados
   $db_name = "anibook";
   $db_host = "localhost";
   $db_user = "root";
   $db_pass = "";

   // Conexão com o banco de dados

   // Define o conjunto de caracteres padrão para UTF-8. Isso é útil para lidar com caracteres especiais e internacionais.
   ini_set('default_charset', 'UTF-8'); 
   // Cria a conexão com o banco de dados
   $conn = new PDO("mysql:dbname=". $db_name .";host=". $db_host, $db_user, $db_pass); 
   // Define o conjunto de caracteres da conexão como UTF-8. Isso garante que os dados transferidos entre o PHP e o 
   // banco de dados sejam codificados corretamente.
   $conn->query("SET NAMES utf8");

   //Errors PDO

   // Define o modo de erro para exceções. Isso significa que o PDO lançará exceções sempre que ocorrer um erro, 
   // facilitando a detecção e o tratamento de erros durante o desenvolvimento.
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   //  Desativa a emulação de prepared statements. Isso significa que o PDO executará consultas preparadas diretamente 
   // no banco de dados, melhorando a segurança e o desempenho das consultas.
   $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);