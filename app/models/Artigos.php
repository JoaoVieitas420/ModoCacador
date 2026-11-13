<?php

namespace app\models;

use app\core\Db;
class Artigos {
  /**
  * Método para obtenção do dataset de todos os artigos
  *
  * @return   array
  */
  public static function getAllArtigos() {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, nome, preco, avaliacao, ano, imagem FROM artigos');
    return $response;
  }

  /**
  * Ordenações permitidas na lista (lista fechada: o valor vindo do URL nunca entra diretamente no SQL)
  */
  const ORDENACOES = [
    'nome' => 'a.nome ASC',
    'preco' => 'a.preco ASC',
    'preco_desc' => 'a.preco DESC',
    'recentes' => 'a.ano DESC, a.id DESC'
  ];

  /**
  * Pesquisa de artigos com filtro por nome e categoria, ordenação e paginação.
  * Inclui o nome da categoria, o número de variantes e o stock total.
  *
  * @return   array
  */
  public static function pesquisarArtigos(string $pesquisa, int $categoriaId, string $ordem, int $limite, int $offset) {
    $conn = new Db();
    $ordenacao = self::ORDENACOES[$ordem] ?? self::ORDENACOES['nome'];
    $response = $conn->execQuery(
      'SELECT a.id, a.nome, a.preco, a.avaliacao, a.ano, a.imagem, c.categoria,
              COUNT(v.id) AS nv, COALESCE(SUM(v.stock), 0) AS stock
       FROM artigos a
       LEFT JOIN categorias c ON c.id = a.categoria_id
       LEFT JOIN variantes v ON v.artigo_id = a.id
       WHERE a.nome LIKE ? AND (? = 0 OR a.categoria_id = ?)
       GROUP BY a.id
       ORDER BY ' . $ordenacao . '
       LIMIT ? OFFSET ?',
      array('siiii', array('%' . $pesquisa . '%', $categoriaId, $categoriaId, $limite, $offset))
    );
    return $response;
  }

  public static function contarArtigos(string $pesquisa, int $categoriaId) {
    $conn = new Db();
    $response = $conn->execQuery(
      'SELECT COUNT(*) AS total FROM artigos WHERE nome LIKE ? AND (? = 0 OR categoria_id = ?)',
      array('sii', array('%' . $pesquisa . '%', $categoriaId, $categoriaId))
    );
    return (int) $response[0]['total'];
  }

  /**
  * Método para a obtenção de um artigo pelo id correspondente
  * @param    int     $id   Id. do artigo
  *
  * @return   array
  */
  public static function findArtigoById(int $id) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, nome, preco, avaliacao, ano, imagem, categoria_id FROM artigos WHERE id = ?', array('i', array($id)));
    return $response;
  }

  public static function addArtigo($data) {
    $conn = new Db();
    $response = $conn->execQuery('INSERT INTO artigos (nome, preco, avaliacao, ano, imagem, categoria_id) VALUES (?, ?, ?, ?, ?, ?)', array(
      'sdsisi',
      array($data['nome'], $data['preco'], $data['avaliacao'], $data['ano'], $data['imagem'], $data['categoria_id'])
    ));
    return $response;
  }

  public static function updateArtigo($id, $data) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE artigos SET nome = ?, preco = ?, avaliacao = ?, ano = ?, imagem = ?, categoria_id = ? WHERE id = ?', array(
      'sdsisii',
      array($data['nome'], $data['preco'], $data['avaliacao'], $data['ano'], $data['imagem'], $data['categoria_id'], $id)
    ));
    return $response;
  }

  public static function deleteArtigo($id) {
    $conn = new Db();
    $response = $conn->execQuery('DELETE FROM artigos WHERE id = ?', array(
      'i', array($id)
    ));
    return $response;
  }

  

}