<?php
use app\core\Controller;
use app\core\Sessao;

/**
* Gestão de utilizadores (apenas administradores: verificado no App::controlarAcesso)
* Os utilizadores não são eliminados, são desativados: assim mantém-se o histórico.
*/
class Utilizador extends Controller {

  public function index() {
    $this->listar([], []);
  }

  public function create() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/utilizador');
    }
    $Utilizadores = $this->model('Utilizadores');
    $dados = [
      'nome' => trim($_POST['nome'] ?? ''),
      'id_utilizador' => strtolower(trim($_POST['id_utilizador'] ?? '')),
      'palavra_passe' => $_POST['palavra_passe'] ?? '',
      'ativo' => empty($_POST['ativo']) ? 0 : 1,
      'admin' => empty($_POST['admin']) ? 0 : 1
    ];
    $erros = [];
    if ($dados['nome'] === '' || mb_strlen($dados['nome']) > 400) {
      $erros['nome'] = 'Indique o nome (até 400 caracteres).';
    }
    if (!preg_match('/^[a-z0-9._-]{3,50}$/', $dados['id_utilizador'])) {
      $erros['id_utilizador'] = 'Use 3 a 50 caracteres: letras minúsculas, números, ponto, hífen ou _.';
    } elseif (!empty($Utilizadores::findUtilizadorById($dados['id_utilizador']))) {
      $erros['id_utilizador'] = 'Já existe um utilizador com este nome.';
    }
    if (mb_strlen($dados['palavra_passe']) < 8) {
      $erros['palavra_passe'] = 'Mínimo de 8 caracteres.';
    }
    if (!empty($erros)) {
      unset($dados['palavra_passe']);
      return $this->listar($erros, $dados);
    }
    $Utilizadores::addUtilizador($dados);
    Sessao::flash('sucesso', 'Utilizador <strong>' . e($dados['nome']) . '</strong> criado. Vai ter de alterar a palavra-passe no primeiro acesso.');
    $this->redirect('/utilizador');
  }

  /**
  * Editar nome, perfil de administrador e (opcional) definir nova palavra-passe temporária
  */
  public function update($id = null) {
    $alvo = $this->obterUtilizador($id);
    $Utilizadores = $this->model('Utilizadores');
    $eu = Sessao::utilizador()['id'] === $alvo['id_utilizador'];
    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nome = trim($_POST['nome'] ?? '');
      $admin = empty($_POST['admin']) ? 0 : 1;
      $novaPP = $_POST['palavra_passe'] ?? '';
      if ($nome === '' || mb_strlen($nome) > 400) {
        $erros['nome'] = 'Indique o nome (até 400 caracteres).';
      }
      if ($eu && !$admin) {
        $erros['admin'] = 'Não pode retirar a si próprio o perfil de administrador.';
      }
      if ($novaPP !== '' && mb_strlen($novaPP) < 8) {
        $erros['palavra_passe'] = 'Mínimo de 8 caracteres.';
      }
      if (empty($erros)) {
        $Utilizadores::updateUtilizador($alvo['id_utilizador'], ['nome' => $nome, 'admin' => $admin]);
        if ($novaPP !== '') {
          $Utilizadores::updatePalavraPasse($alvo['id_utilizador'], $novaPP, 1);
        }
        if ($eu) {
          Sessao::atualizar(['nome' => $nome]);
        }
        Sessao::flash('sucesso', 'Utilizador <strong>' . e($nome) . '</strong> atualizado.' . ($novaPP !== '' ? ' Vai ter de alterar a palavra-passe no próximo acesso.' : ''));
        $this->redirect('/utilizador');
      }
      $alvo = array_merge($alvo, ['nome' => $nome, 'admin' => $admin]);
    }

    unset($alvo['palavra_passe']);
    $this->view('utilizador/form', ['titulo' => 'Editar utilizador', 'alvo' => $alvo, 'eu' => $eu, 'erros' => $erros, 'classe_conteudo' => 'conteudo-estreito']);
  }

  public function desativar($id = null) {
    $this->mudarEstado($id, 0);
  }

  public function reativar($id = null) {
    $this->mudarEstado($id, 1);
  }

  private function mudarEstado($id, int $ativo) {
    $alvo = $this->obterUtilizador($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $Utilizadores = $this->model('Utilizadores');
      if (!$ativo && Sessao::utilizador()['id'] === $alvo['id_utilizador']) {
        Sessao::flash('erro', 'Não pode desativar a sua própria conta.');
      } elseif (!$ativo && (int) $alvo['admin'] === 1 && $Utilizadores::countAdminsAtivos() <= 1) {
        Sessao::flash('erro', 'Tem de existir pelo menos um administrador ativo.');
      } else {
        $Utilizadores::setAtivo($alvo['id_utilizador'], $ativo);
        Sessao::flash('sucesso', 'Utilizador <strong>' . e($alvo['nome']) . '</strong> ' . ($ativo ? 'reativado.' : 'desativado. Deixa de conseguir iniciar sessão.'));
      }
    }
    $this->redirect('/utilizador');
  }

  private function obterUtilizador($id) {
    $Utilizadores = $this->model('Utilizadores');
    $alvo = is_string($id) && $id !== '' ? ($Utilizadores::findUtilizadorById(urldecode($id))[0] ?? null) : null;
    if ($alvo === null) {
      $this->pageNotFound();
      exit;
    }
    return $alvo;
  }

  private function listar(array $erros, array $valores) {
    $Utilizadores = $this->model('Utilizadores');
    $this->view('utilizador/index', [
      'titulo' => 'Utilizadores',
      'utilizadores' => $Utilizadores::getAllUtilizadores(),
      'erros' => $erros,
      'valores' => $valores
    ]);
  }
}
?>
