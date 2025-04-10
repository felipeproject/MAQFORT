<?php
// Caminho do arquivo JSON
$jsonFile = 'produtos.json';

// Função para ler os produtos do JSON
function getProdutos() {
  global $jsonFile;
  // Verificar se o arquivo existe e tem conteúdo
  return file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
}

// Função para salvar os produtos no arquivo JSON
function saveProdutos($produtos) {
  global $jsonFile;
  file_put_contents($jsonFile, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Função para adicionar um novo produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
  // Captura os dados do formulário
  $nome = $_POST['nome'];
  $descricao = $_POST['descricao'];
  $preco = $_POST['preco'];
  $imagens = array_map('trim', explode("\n", $_POST['imagens']));
  $detalhes = array_map('trim', explode("\n", $_POST['detalhes']));

  // Lê os produtos atuais
  $produtos = getProdutos();

  // Gera novo ID
  $novoId = empty($produtos) ? 1 : max(array_column($produtos, 'id')) + 1;

  // Monta o novo produto
  $novoProduto = [
    'id' => $novoId,
    'nome' => $nome,
    'descricao' => $descricao,
    'preco' => $preco,
    'imagens' => $imagens,
    'detalhes' => $detalhes
  ];

  // Adiciona ao array de produtos
  $produtos[] = $novoProduto;

  // Salva de volta no arquivo
  saveProdutos($produtos);

  $mensagem = "✅ Produto adicionado com sucesso!";
}

// Função para editar um produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
  $id = $_POST['id'];
  $nome = $_POST['nome'];
  $descricao = $_POST['descricao'];
  $preco = $_POST['preco'];
  $imagens = array_map('trim', explode("\n", $_POST['imagens']));
  $detalhes = array_map('trim', explode("\n", $_POST['detalhes']));

  $produtos = getProdutos();
  $produtoEncontrado = false;

  foreach ($produtos as &$produto) {
    if ($produto['id'] == $id) {
      // Atualiza os dados do produto
      $produto['nome'] = $nome;
      $produto['descricao'] = $descricao;
      $produto['preco'] = $preco;
      $produto['imagens'] = $imagens;
      $produto['detalhes'] = $detalhes;
      $produtoEncontrado = true;
      break;
    }
  }

  if ($produtoEncontrado) {
    saveProdutos($produtos);
    $mensagem = "✅ Produto atualizado com sucesso!";
  } else {
    $mensagem = "❌ Produto não encontrado para edição.";
  }
}

// Função para excluir um produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
  $id = $_POST['id'];

  $produtos = getProdutos();
  $produtos = array_filter($produtos, function ($produto) use ($id) {
    return $produto['id'] != $id;
  });

  $produtos = array_values($produtos); // Reindexa o array

  saveProdutos($produtos);

  $mensagem = "✅ Produto excluído com sucesso!";
}

// Se a ação for editar, carregamos o produto para pré-preencher o formulário
$produtoParaEditar = null;
if (isset($_GET['editar_id'])) {
  $produtos = getProdutos();
  foreach ($produtos as $produto) {
    if ($produto['id'] == $_GET['editar_id']) {
      $produtoParaEditar = $produto;
      break;
    }
  }
}

$produtos = getProdutos(); // Carregar os produtos para exibir na página
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Produtos - Maqfort</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Navbar -->
  <nav class="bg-[#802A31] text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <a href="#" class="text-2xl font-bold">Maqfort</a>
      <ul class="flex space-x-6">
        <li><a href="#adicionar" class="hover:text-[#C66B31]">Adicionar Produto</a></li>
        <li><a href="#listar" class="hover:text-[#C66B31]">Listar Produtos</a></li>
      </ul>
    </div>
  </nav>

  <!-- Mensagem de sucesso -->
  <?php if (!empty($mensagem)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
      <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

  <!-- Formulário para Adicionar ou Editar Produto -->
  <section id="adicionar" class="max-w-3xl mx-auto mt-16 p-8 bg-white shadow-lg rounded-lg">
    <h2 class="text-3xl font-bold text-[#802A31] mb-8"><?= $produtoParaEditar ? 'Editar Produto' : 'Adicionar Novo Produto' ?></h2>
    <form method="POST" class="space-y-6">
      <input type="hidden" name="action" value="<?= $produtoParaEditar ? 'edit' : 'add' ?>" />
      <?php if ($produtoParaEditar): ?>
        <input type="hidden" name="id" value="<?= $produtoParaEditar['id'] ?>" />
      <?php endif; ?>
      <div>
        <label class="block font-medium">Nome do Produto:</label>
        <input type="text" name="nome" required class="w-full border rounded px-4 py-2" value="<?= $produtoParaEditar['nome'] ?? '' ?>" />
      </div>

      <div>
        <label class="block font-medium">Descrição:</label>
        <textarea name="descricao" rows="4" required class="w-full border rounded px-4 py-2"><?= $produtoParaEditar['descricao'] ?? '' ?></textarea>
      </div>

      <div>
        <label class="block font-medium">Preço:</label>
        <input type="text" name="preco" required class="w-full border rounded px-4 py-2" value="<?= $produtoParaEditar['preco'] ?? '' ?>" />
      </div>

      <div>
        <label class="block font-medium">Imagens:</label>
        <textarea name="imagens" rows="3" class="w-full border rounded px-4 py-2"><?= isset($produtoParaEditar['imagens']) ? implode("\n", $produtoParaEditar['imagens']) : '' ?></textarea>
      </div>

      <div>
        <label class="block font-medium">Detalhes:</label>
        <textarea name="detalhes" rows="4" class="w-full border rounded px-4 py-2"><?= isset($produtoParaEditar['detalhes']) ? implode("\n", $produtoParaEditar['detalhes']) : '' ?></textarea>
      </div>

      <button type="submit" class="bg-[#802A31] text-white px-6 py-3 rounded hover:bg-[#A73B40] transition">
        <?= $produtoParaEditar ? 'Atualizar Produto' : 'Salvar Produto' ?>
      </button>
    </form>
  </section>

  <!-- Listar Produtos -->
  <section id="listar" class="max-w-6xl mx-auto mt-16 p-8 bg-white shadow-lg rounded-lg">
    <h2 class="text-3xl font-bold text-[#802A31] mb-8">Lista de Produtos</h2>
    <table class="min-w-full table-auto">
      <thead>
        <tr class="text-left bg-[#802A31] text-white">
          <th class="px-4 py-2">Nome</th>
          <th class="px-4 py-2">Preço</th>
          <th class="px-4 py-2">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produtos as $produto): ?>
          <tr>
            <td class="px-4 py-2"><?= htmlspecialchars($produto['nome']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($produto['preco']) ?></td>
            <td class="px-4 py-2">
              <a href="?editar_id=<?= $produto['id'] ?>" class="text-blue-500 hover:underline">Editar</a> |
              <form method="POST" style="display:inline-block;">
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id" value="<?= $produto['id'] ?>" />
                <button type="submit" class="text-red-500 hover:underline">Excluir</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

</body>
</html>
