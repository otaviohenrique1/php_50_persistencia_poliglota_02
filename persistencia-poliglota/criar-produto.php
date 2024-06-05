<?php

header('Content-Type: application/json');

// receber um produto no payload da requisição e inserir no MongoDB
$documento = json_decode(file_get_contents('php://input'), true);

if (empty($documento['nome'])) {
  http_response_code(422);

  echo json_encode([
    'status' => 'error',
    'msg' => 'Nome do produto é obrigatório'
  ]);
  return;
}
