<?php
namespace app\models;
use app\core\Db;

class Imagens {

  /**
  * Método para a obtenção da galeria de imagens de um artigo
  * (cor = NULL -> imagem geral; cor preenchida -> imagem das variantes dessa cor)
  * @param    int     $artigoId   Id. do artigo
  *
  * @return   array
  */
  public static function getImagensByArtigo(int $artigoId) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, artigo_id, cor, caminho, descricao, autor, licenca, fonte, ordem FROM imagens WHERE artigo_id = ? ORDER BY ordem', array('i', array($artigoId)));
    return $response;
  }

  public static function findImagemById(int $id) {
    $conn = new Db();
    $response = $conn->execQuery('SELECT id, artigo_id, cor, caminho, ordem FROM imagens WHERE id = ?', array('i', array($id)));
    return $response;
  }

  /**
  * Acrescenta uma imagem no fim da galeria do artigo
  */
  public static function addImagem(int $artigoId, string $caminho, $cor) {
    $conn = new Db();
    $response = $conn->execQuery(
      'INSERT INTO imagens (artigo_id, cor, caminho, ordem) SELECT ?, ?, ?, COALESCE(MAX(ordem), 0) + 1 FROM imagens WHERE artigo_id = ?',
      array('issi', array($artigoId, $cor, $caminho, $artigoId))
    );
    self::atualizarImagemPrincipal($artigoId);
    return $response;
  }

  public static function deleteImagem(int $id) {
    $conn = new Db();
    $response = $conn->execQuery('DELETE FROM imagens WHERE id = ?', array('i', array($id)));
    if ($response !== null) {
      self::atualizarImagemPrincipal((int) $response['artigo_id']);
    }
    return $response;
  }

  /**
  * A imagem principal do artigo (artigos.imagem) é sempre a primeira da galeria
  */
  public static function atualizarImagemPrincipal(int $artigoId) {
    $conn = new Db();
    $conn->execQuery(
      'UPDATE artigos SET imagem = (SELECT caminho FROM imagens WHERE artigo_id = ? ORDER BY ordem LIMIT 1) WHERE id = ?',
      array('ii', array($artigoId, $artigoId))
    );
  }

}
?>
