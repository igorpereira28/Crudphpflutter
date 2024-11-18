<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";

// Cria a conexão
$conn = new mysqli($servername, $username, $password);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Apaga a base de dados se existir
$sql = "DROP DATABASE IF EXISTS api";
if ($conn->query($sql) === TRUE) {
    echo "Banco de dados 'api' apagado com sucesso, se existia.<br>";
} else {
    echo "Erro ao apagar banco de dados: " . $conn->error . "<br>";
}

// Cria a base de dados
$sql = "CREATE DATABASE api";
if ($conn->query($sql) === TRUE) {
    echo "Banco de dados 'api' criado com sucesso.<br>";
} else {
    echo "Erro ao criar banco de dados: " . $conn->error . "<br>";
}

// Seleciona a base de dados
$conn->select_db("api");

// SQL para criar a tabela 'cliente'
$sql = "CREATE TABLE cliente (
    idcli INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    categoria VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabela 'cliente' criada com sucesso.<br>";
} else {
    echo "Erro ao criar tabela: " . $conn->error . "<br>";
}

// Fecha a conexão
$conn->close();
?>
