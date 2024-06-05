<?php

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\Statement;

require_once 'vendor/autoload.php';

$client = ClientBuilder::create()
  ->withDriver('bolt', 'bolt+s://user:password@localhost')
  ->withDriver('https', 'https://test.com', Authenticate::basic('user', 'password'))
  ->withDriver('neo4j', 'neo4j://neo4j.test.com?database=my-database', Authenticate::oidc('token'))
  ->withDefaultDriver('bolt')
  ->build();

// var_dump($client->verifyConnectivity());
// $result = $client->run('CREATE (u:Usuario {nome: $nome})', ['nome' => 'Vinicius']);
// var_dump($result->getSummary());

// $client->writeTransaction(
//   static function(TransactionInterface $transaction) {
//     $transaction->runStatements([
//       Statement::create('CREATE (u:Usuario {nome: $nome})', ['nome' => 'Patricia']),
//       Statement::create('CREATE (u:Usuario {nome: $nome})', ['nome' => 'Rafaela']),
//     ]);
//   }
// );

$client->writeTransaction(static function (TransactionInterface $transaction) {
  $transaction->runStatements([
    Statement::create(
      'MATCH (vinicius:Usuario {nome: "Vinicius"}), (patricia:Usuario {nome: "Patricia"}) CREATE (vinicius)-[:AMIGO_DE]->(patricia)'
    ),
    Statement::create(
      'MATCH (patricia:Usuario {nome: "Patricia"}), (rafaela:Usuario {nome: "Rafaela"}) CREATE (patricia)-[:AMIGO_DE]->(rafaela)'
    ),
  ]);
});


$result = $client->readTransaction(static function (TransactionInterface $transaction) {
  return $transaction->run(
    'MATCH (vinicius:Usuario {nome: "Vinicius"})-[:AMIGO_DE*2..3]-(sugestao:Usuario)
    WHERE NOT (vinicius)-[:AMIGO_DE]-(sugestao)
    RETURN sugestao.nome'
  );
});

// foreach ($result as $item) {
//   var_dump($item);
// }

/** @var \Laudis\Neo4j\Types\CypherMap $item */
foreach ($result as $item) {
  echo $item->get('sugestao.nome');
}
