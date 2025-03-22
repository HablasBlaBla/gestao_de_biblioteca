<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Definindo os parâmetros de paginação
$limit = 6; // Número de livros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtro de pesquisa
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Consultando todos os livros com paginação e filtro
$sql = "SELECT * FROM livros WHERE titulo LIKE ? OR isbn LIKE ? ORDER BY titulo ASC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Contando o número total de livros
$sql_total = "SELECT COUNT(*) as total FROM livros WHERE titulo LIKE ? OR isbn LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param('ss', $searchTerm, $searchTerm);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="favicon/favicon-32x32.png" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            border-radius: 15px 15px 0 0;
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .book-cover {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .book-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #333;
        }
        .book-info {
            margin-left: 15px;
        }
        .book-details {
            padding-top: 15px;
            display: none;
        }
        .book-actions {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            margin-top: 10px;
        }
        .book-actions a {
            margin-left: 10px;
        }
        .toggle-details-btn {
            cursor: pointer;
        }
        .list-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .list-item:hover {
            transform: scale(1.02);
            transition: all 0.3s ease;
        }
        .pagination {
            justify-content: center;
            margin-top: 30px;
        }
        .search-container {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-book"></i> Visualização de Livros</h4>
            </div>
            <div class="card-body">
                <!-- Campo de pesquisa -->
                <form method="GET" action="visualizar_livros.php" class="search-container">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Pesquisar por nome ou ISBN" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                    </div>
                </form>

                <!-- Lista de livros -->
                <div id="book-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="list-item">
                                <div>
                                    <img src="<?php echo $row['capa_url'] ?: 'default-cover.jpg'; ?>" alt="Capa do Livro" class="book-cover">
                                </div>
                                <div class="book-info">
                                    <p class="book-title"><?php echo $row['titulo']; ?></p>
                                    <p><strong>Autor:</strong> <?php echo $row['autor']; ?></p>
                                    <p><strong>ISBN:</strong> <?php echo $row['isbn']; ?></p>
                                    
                                    <div class="book-actions">
                                        <button class="btn btn-info btn-sm toggle-details-btn" onclick="toggleDetails(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-chevron-down"></i> Detalhes
                                        </button>
                                        <a href="editar_livro.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="excluir_livro.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Excluir
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalhes adicionais (ocultos inicialmente) -->
                            <div class="book-details" id="details-<?php echo $row['id']; ?>">
                                <p><strong>Ano de Publicação:</strong> <?php echo $row['ano_publicacao']; ?></p>
                                <p><strong>Gênero:</strong> <?php echo $row['genero']; ?></p>
                                <p><strong>Descrição:</strong> <?php echo $row['descricao'] ?: 'Nenhuma descrição disponível.'; ?></p>
                                <p><strong>Categoria:</strong> <?php echo $row['categoria'] ?: 'Não especificada'; ?></p>
                                <p><strong>Quantidade disponível:</strong> <?php echo $row['quantidade']; ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">Nenhum livro encontrado.</p>
                    <?php endif; ?>
                </div>

                <!-- Paginação -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="visualizar_livros.php?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>" class="btn btn-outline-primary">Anterior</a>
                    <?php endif; ?>
                    <span class="mx-2">Página <?php echo $page; ?> de <?php echo $total_pages; ?></span>
                    <?php if ($page < $total_pages): ?>
                        <a href="visualizar_livros.php?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>" class="btn btn-outline-primary">Próxima</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <br>
        <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">Voltar para o Painel</a>
        <br>
    </div>

    <script>
        // Função para alternar a exibição dos detalhes
        function toggleDetails(bookId) {
            var details = document.getElementById('details-' + bookId);
            var button = event.target;
            
            if (details.style.display === 'block') {
                details.style.display = 'none';
                button.innerHTML = '<i class="fas fa-chevron-down"></i> Detalhes';
            } else {
                details.style.display = 'block';
                button.innerHTML = '<i class="fas fa-chevron-up"></i> Fechar Detalhes';
            }
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
