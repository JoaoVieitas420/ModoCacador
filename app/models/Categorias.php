<?php
namespace app\models;
use app\core\Db;

class Categorias {

  public static function getAllCategorias() {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, categoria FROM categorias ORDER BY categoria');
    return $response;
  }

  /**
  * Categorias com o número de artigos de cada uma
  *
  * @return   array
  */
  public static function getCategoriasComContagem() {
    $conn = new Db();
    $response = $conn->execQuery('SELECT c.id, c.categoria, COUNT(a.id) AS artigos FROM categorias c LEFT JOIN artigos a ON a.categoria_id = c.id GROUP BY c.id ORDER BY c.categoria');
    return $response;
  }

  /**
  * Verifica se já existe uma categoria com o mesmo nome (sem distinguir maiúsculas)
  */
  public static function existeCategoria(string $nome, $ignorarId = null) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT COUNT(*) AS total FROM categorias WHERE LOWER(categoria) = LOWER(?) AND id <> ?', array('si', array($nome, (int) $ignorarId)));
    return (int) $response[0]['total'] > 0;
  }

  /**
  * Método para a obtenção de uma categoria pelo id correspondente
  * @param    int     $id   Id. da categoria
  *
  * @return   array
  */
  public static function findCategoriaById(int $id) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, categoria FROM categorias WHERE id = ?', array('i', array($id)));
    return $response;
  }

  /**
  * Método para a obtenção dos artigos de uma categoria
  * @param    int     $id   Id. da categoria
  *
  * @return   array
  */
  public static function getArtigosByCategoria(int $id) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, nome, preco FROM artigos WHERE categoria_id = ?', array('i', array($id)));
    return $response;
  }

  public static function addCategoria($data) {
    $conn = new Db();
    $response = $conn->execQuery('INSERT INTO categorias (categoria) VALUES (?)', array(
      's',
      array($data['categoria'])
    ));
    return $response;
  }

  public static function updateCategoria($id, $data) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE categorias SET categoria = ? WHERE id = ?', array(
      'si',
      array($data['categoria'], $id)
    ));
    return $response;
  }

  public static function deleteCategoria($id) {
    $conn = new Db();
    $response = $conn->execQuery('DELETE FROM categorias WHERE id = ?', array(
      'i', array($id)
    ));
    return $response;
  }

}
?>
