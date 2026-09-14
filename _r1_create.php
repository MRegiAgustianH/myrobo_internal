<?php
// Sementara: import dump produksi ke DB scratch untuk rehearsal nyata.

$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
$pdo->exec('DROP DATABASE IF EXISTS myrobo_prod_rehearsal');
$pdo->exec('CREATE DATABASE myrobo_prod_rehearsal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
echo "DB scratch dibuat\n";
