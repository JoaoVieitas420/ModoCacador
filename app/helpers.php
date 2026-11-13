<?php
/**
* Funções auxiliares da aplicação.
*
* icone(): ícones SVG (traço) usados nas views. Decorativos: aria-hidden.
*
* @param  string  $nome
* @param  int     $tamanho
*/
function icone(string $nome, int $tamanho = 18) {
  $caminhos = [
    'artigos' => '<path d="M21 8l-9-5-9 5 9 5 9-5z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path>',
    'categorias' => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8z"></path><circle cx="7.5" cy="7.5" r="1.5"></circle>',
    'utilizadores' => '<circle cx="9" cy="8" r="4"></circle><path d="M2 21c0-4 3-6 7-6s7 2 7 6"></path><path d="M16 4a4 4 0 010 8M22 21c0-3-1.5-5-4-5.6"></path>',
    'sair' => '<path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10"></path>',
    'mais' => '<path d="M12 5v14M5 12h14"></path>',
    'editar' => '<path d="M4 20h4L19 9l-4-4L4 16v4z"></path>',
    'eliminar' => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"></path>',
    'pesquisa' => '<circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path>',
    'seta-esq' => '<path d="M15 6l-6 6 6 6"></path>',
    'seta-dir' => '<path d="M9 6l6 6-6 6"></path>',
    'sucesso' => '<circle cx="12" cy="12" r="10"></circle><path d="M8 12l3 3 5-6"></path>',
    'erro' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 7v6M12 16.5v.5"></path>',
    'upload' => '<path d="M12 16V4M7 9l5-5 5 5M4 20h16"></path>',
    'estrela' => '<path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z" fill="#B7791F" stroke="none"></path>',
  ];
  return '<svg width="' . $tamanho . '" height="' . $tamanho . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($caminhos[$nome] ?? '') . '</svg>';
}

/**
* Iniciais do nome (para o avatar sem fotografia)
*/
function iniciais(string $nome) {
  $partes = preg_split('/\s+/', trim($nome));
  $ini = count($partes) > 1 ? mb_substr($partes[0], 0, 1) . mb_substr(end($partes), 0, 1) : mb_substr($nome, 0, 2);
  return mb_strtoupper($ini);
}

/**
* Preço no formato português (89,90 €)
*/
function euros($valor) {
  return number_format((float) $valor, 2, ',', ' ') . ' €';
}

/**
* Escapa texto para HTML
*/
function e($texto) {
  return htmlspecialchars((string) ($texto ?? ''), ENT_QUOTES, 'UTF-8');
}
