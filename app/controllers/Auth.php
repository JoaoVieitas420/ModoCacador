<?php
use app\core\Controller;
use app\core\Sessao;

class Auth extends Controller {

  public function index() {
    $this->redirect('/auth/login');
  }

  /**
  * Formulário de login (GET) e validação das credenciais (POST)
  */
  public function login() {
    if (Sessao::utilizador() !== null) {
      $this->redirect('/artigo');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = trim($_POST['id_utilizador'] ?? '');
      $palavraPasse = $_POST['palavra_passe'] ?? '';

      $Utilizadores = $this->model('Utilizadores');
      $registo = $id !== '' ? $Utilizadores::findUtilizadorById($id) : [];
      $utilizador = $registo[0] ?? null;

      // a mesma mensagem para utilizador inexistente, palavra-passe errada ou conta inativa:
      // assim não se revela que utilizadores existem
      if ($utilizador === null || !password_verify($palavraPasse, $utilizador['palavra_passe']) || (int) $utilizador['ativo'] !== 1) {
        $this->view('auth/login', ['erro' => 'Utilizador ou palavra-passe incorretos.', 'id_utilizador' => $id], false);
        return;
      }

      Sessao::entrar($utilizador);
      $this->redirect((int) $utilizador['alterar_palavra_passe'] === 1 ? '/perfil' : '/artigo');
    }

    $this->view('auth/login', ['id_utilizador' => ''], false);
  }

  /**
  * Terminar sessão (apenas por POST, com token CSRF validado no App)
  */
  public function logout() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      Sessao::sair();
    }
    $this->redirect('/auth/login');
  }
}
?>
