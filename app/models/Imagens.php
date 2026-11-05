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

}
?>
