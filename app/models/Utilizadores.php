<?php
namespace app\models;
use app\core\Db;

class Utilizadores {

  /**
  * Método para a obtenção de um utilizador (inclui o hash da palavra-passe, para o login)
  * @param    string  $id   Id. do utilizador
  *
  * @return   array
  */
  public static function findUtilizadorById(string $id) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id_utilizador, palavra_passe, nome, fotografia, ativo, admin, alterar_palavra_passe FROM utilizador WHERE id_utilizador = ?', array('s', array($id)));
    return $response;
  }

  /**
  * Lista de utilizadores (sem o hash da palavra-passe)
  *
  * @return   array
  */
  public static function getAllUtilizadores() {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id_utilizador, nome, fotografia, ativo, admin FROM utilizador ORDER BY nome');
    return $response;
  }

  public static function addUtilizador($data) {
    $conn = new Db();
    $response = $conn->execQuery('INSERT INTO utilizador (id_utilizador, palavra_passe, nome, fotografia, ativo, admin, alterar_palavra_passe) VALUES (?, ?, ?, NULL, ?, ?, 1)', array(
      'sssii',
      array($data['id_utilizador'], password_hash($data['palavra_passe'], PASSWORD_DEFAULT), $data['nome'], $data['ativo'], $data['admin'])
    ));
    return $response;
  }

  public static function updateUtilizador($id, $data) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE utilizador SET nome = ?, admin = ? WHERE id_utilizador = ?', array(
      'sis',
      array($data['nome'], $data['admin'], $id)
    ));
    return $response;
  }

  public static function updateDados($id, $nome, $fotografia) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE utilizador SET nome = ?, fotografia = ? WHERE id_utilizador = ?', array(
      'sss',
      array($nome, $fotografia, $id)
    ));
    return $response;
  }

  /**
  * Altera a palavra-passe (guarda sempre o hash) e define se terá de ser alterada no próximo acesso
  */
  public static function updatePalavraPasse($id, $palavraPasse, $alterarNoProximoAcesso = 0) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE utilizador SET palavra_passe = ?, alterar_palavra_passe = ? WHERE id_utilizador = ?', array(
      'sis',
      array(password_hash($palavraPasse, PASSWORD_DEFAULT), $alterarNoProximoAcesso, $id)
    ));
    return $response;
  }

  public static function setAtivo($id, $ativo) {
    $conn = new Db();
    $response = $conn->execQuery('UPDATE utilizador SET ativo = ? WHERE id_utilizador = ?', array(
      'is',
      array($ativo, $id)
    ));
    return $response;
  }

  /**
  * Número de administradores ativos (para nunca ficar sem nenhum)
  */
  public static function countAdminsAtivos() {
    $conn = new Db();
    $response = $conn->execQuery('SELECT COUNT(*) AS total FROM utilizador WHERE admin = 1 AND ativo = 1');
    return (int) $response[0]['total'];
  }
}
?>
