<?php
namespace app\models;
use app\core\Db;

class Variantes {

  /**
  * Método para a obtenção das variantes (cor, tamanho, material) de um artigo
  * @param    int     $artigoId   Id. do artigo
  *
  * @return   array
  */
  public static function getVariantesByArtigo(int $artigoId) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, artigo_id, cor, tamanho, material, stock FROM variantes WHERE artigo_id = ?', array('i', array($artigoId)));
    return $response;
  }

  /**
  * Método para a obtenção de uma variante pelo id correspondente
  * @param    int     $id   Id. da variante
  *
  * @return   array
  */
  public static function findVarianteById(int $id) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, artigo_id, cor, tamanho, material, stock FROM variantes WHERE id = ?', array('i', array($id)));
    return $response;
  }

  public static function addVariante($data) {
    $conn = new Db();
    $response = $conn->execQuery('INSERT INTO variantes (artigo_id, cor, tamanho, material, stock) VALUES (?, ?, ?, ?, ?)', array(
      'isssi',
      array($data['artigo_id'], $data['cor'], $data['tamanho'], $data['material'], $data['stock'])
    ));
    return $response;
  }

  public static function updateVariante($id, $data) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE variantes SET cor = ?, tamanho = ?, material = ?, stock = ? WHERE id = ?', array(
      'sssii',
      array($data['cor'], $data['tamanho'], $data['material'], $data['stock'], $id)
    ));
    return $response;
  }

  public static function deleteVariante($id) {
    $conn = new Db();
    $response = $conn->execQuery('DELETE FROM variantes WHERE id = ?', array(
      'i', array($id)
    ));
    return $response;
  }

}
?>
