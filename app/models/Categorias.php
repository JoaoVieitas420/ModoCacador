<?php
namespace app\models;
use app\core\Db;

class Categorias {

  public static function getAllCategorias() {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, categoria FROM categorias');
    return $response;
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
