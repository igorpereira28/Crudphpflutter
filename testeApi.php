<?php
// Habilita CORS
header("Access-Control-Allow-Origin: *"); // Permite qualquer origem (pode ser restrito a um domínio específico)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Permite métodos HTTP específicos
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Permite cabeçalhos específicos

// Verifica se é uma requisição OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obtém a URL de execução da API
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Array de resposta para o servidor
$response = array();

// Criar atributos a partir da URL (apenas para debug)
$response['folder'] = $uri[1] ?? null;
$response['api'] = $uri[2] ?? null;
$response['endPoint'] = $uri[3] ?? null;
$response['action'] = $uri[4] ?? null;

// Obtém método solicitado
$response['method'] = $_SERVER['REQUEST_METHOD'];

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "api";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['message' => "Erro na conexão com o banco de dados: " . $conn->connect_error]);
    exit;
}

// Executa a operação baseada no método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'PUT':
        // Verifica se o ID foi fornecido
        $id = $uri[4] ?? null;
        if (!$id) {
            $response['message'] = "ID não fornecido.";
            http_response_code(400);
            echo json_encode($response);
            exit;
        }

        // Obtém os dados enviados no corpo da requisição
        $data = json_decode(file_get_contents("php://input"), true);
        $nome = $data['nome'] ?? null;
        $categoria = $data['categoria'] ?? null;

        if (!$nome || !$categoria) {
            $response['message'] = "Dados incompletos.";
            http_response_code(400);
            echo json_encode($response);
            exit;
        }

        // Atualiza o cliente no banco de dados
        $sql = "UPDATE cliente SET nome='$nome', categoria='$categoria' WHERE idcli=$id";
        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                $response['message'] = "Cliente atualizado com sucesso!";
            } else {
                $response['message'] = "Nada foi alterado!";
            }
        } else {
            $response['message'] = "Erro ao atualizar cliente: " . $conn->error;
        }
        break;

    case 'POST':
        // Insere um novo cliente no banco de dados
        $data = json_decode(file_get_contents("php://input"), true);
        $nome = $data['nome'] ?? null;
        $categoria = $data['categoria'] ?? null;

        if (!$nome || !$categoria) {
            $response['message'] = "Dados incompletos.";
            http_response_code(400);
            echo json_encode($response);
            exit;
        }

        $sql = "INSERT INTO cliente (nome, categoria) VALUES ('$nome', '$categoria')";
        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                $response['message'] = "Cliente adicionado com sucesso!";
            } else {
                $response['message'] = "Erro ao adicionar cliente: " . $conn->error;
            }
        }
        break;

    case 'DELETE':
        // Verifica se o ID foi fornecido
        $id = $uri[4] ?? null;
        if (!$id) {
            $response['message'] = "ID não fornecido.";
            http_response_code(400);
            echo json_encode($response);
            exit;
        }

        // Exclui o cliente no banco de dados
        $sql = "DELETE FROM cliente WHERE idcli='$id'";
        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                $response['message'] = "Cliente excluído com sucesso!";
            } else {
                $response['message'] = "Erro ao tentar excluir cliente: " . $conn->error;
            }
        } else {
            $response['message'] = "Erro ao tentar excluir cliente: " . $conn->error;
        }
        break;

    case 'GET':
        // Consulta de clientes
        $sql = "SELECT * FROM cliente";
        $result = $conn->query($sql);
        $response = array(); // Limpa a resposta anterior
        if ($result->num_rows > 0) {
            // Obtém os campos da consulta
            while ($row = $result->fetch_assoc()) {
                $response[] = ['id' => $row['idcli'], 'nome' => $row['nome'], 'categoria' => $row['categoria']];
            }
        } else {
            $response['message'] = "Nenhum cliente encontrado.";
        }
        break;

    default:
        $response['message'] = "Método não suportado.";
        http_response_code(405); // Método não permitido
        break;
}

// Fecha a conexão com o banco de dados
$conn->close();

// Retorna a resposta em formato JSON
header('Content-Type: application/json');
echo json_encode($response);
