<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php';

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = '';
if (isset($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search']));
}

$sql = "SELECT * FROM livros WHERE titulo LIKE ? OR isbn LIKE ? ORDER BY titulo ASC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

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
        :root {
            --primary-color: #00796b;
            --primary-dark: #004d40;
            --secondary-color: #26a69a;
            --background-gradient: linear-gradient(135deg, #f8f9fa, #e9ecef);
            --card-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: var(--background-gradient);
            font-family: 'Arial', sans-serif;
            color: #212121;
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .page-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
            text-align: center;
        }

        .search-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .search-input {
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            border: 2px solid #e0e0e0;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 121, 107, 0.25);
        }

        .book-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            border: 1px solid #eee;
            animation: fadeIn 0.5s ease-in-out;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .book-cover {
            width: 140px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }

        .book-cover:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .book-info {
            flex: 1;
            padding-left: 1.5rem;
        }

        .book-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .book-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .book-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .book-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
            animation: slideDown 0.3s ease-in-out;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .pagination .btn {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: var(--transition);
        }

        .pagination .btn:hover {
            transform: translateY(-2px);
        }

        .btn-back {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            transition: var(--transition);
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-back:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            color: white;
        }

        .stats-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            background: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }

        .stat-number {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .book-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .book-info {
                padding-left: 0;
                padding-top: 1rem;
            }

            .book-actions {
                justify-content: center;
            }

            .book-cover {
                width: 120px;
                height: 180px;
            }

            .stat-card {
                min-width: 45%;
            }
        }

        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .search-container {
                padding: 1rem;
            }

            .book-card {
                padding: 1rem;
            }

            .stat-card {
                min-width: 100%;
            }

            .pagination .btn {
                min-width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
                color: #ffffff;
            }

            .card, .search-container, .book-card, .stat-card {
                background-color: #333333;
                color: #ffffff;
            }

            .book-details {
                background-color: #404040;
            }

            .search-input {
                background-color: #404040;
                border-color: #555555;
                color: #ffffff;
            }

            .book-meta {
                color: #cccccc;
            }

            .stat-label {
                color: #cccccc;
            }
        }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <h1 class="text-center mb-0">
                <i class="fas fa-books"></i> Biblioteca Digital
            </h1>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['msg']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_books; ?></div>
                <div class="stat-label">Total de Livros</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $result->num_rows; ?></div>
                <div class="stat-label">Livros nesta página</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_pages; ?></div>
                <div class="stat-label">Total de Páginas</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="mb-0"><i class="fas fa-book"></i> Catálogo de Livros</h2>
            </div>
            <div class="card-body">
                <form method="GET" action="visualizar_livros.php" class="search-container">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control search-input" 
                               placeholder="Pesquisar por título ou ISBN..."
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>

                <div id="book-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="book-card d-flex">
                                <img src="<?php echo $row['capa_url'] ?: 'default-cover.jpg'; ?>" 
                                     alt="Capa do Livro" 
                                     class="book-cover"
                                     loading="lazy">
                                
                                <div class="book-info">
                                    <h3 class="book-title"><?php echo $row['titulo']; ?></h3>
                                    <p class="book-meta">
                                        <i class="fas fa-user-edit"></i> <?php echo $row['autor']; ?>
                                    </p>
                                    <p class="book-meta">
                                        <i class="fas fa-barcode"></i> ISBN: <?php echo $row['isbn']; ?>
                                    </p>
                                    
                                    <div class="book-actions">
                                        <button class="btn btn-info btn-action" onclick="toggleDetails(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-info-circle"></i> Detalhes
                                        </button>
                                        <a href="editar_livro.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-action">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="#" class="btn btn-danger btn-action" 
                                           onclick="confirmarExclusao(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-trash"></i> Excluir
                                        </a>
                                    </div>

                                    <div class="book-details" id="details-<?php echo $row['id']; ?>">
                                        <p><i class="fas fa-calendar"></i> <strong>Ano:</strong> <?php echo $row['ano_publicacao']; ?></p>
                                        <p><i class="fas fa-bookmark"></i> <strong>Gênero:</strong> <?php echo $row['genero']; ?></p>
                                        <p><i class="fas fa-align-left"></i> <strong>Descrição:</strong> <?php echo $row['descricao'] ?: 'Nenhuma descrição disponível.'; ?></p>
                                        <p><i class="fas fa-tag"></i> <strong>Categoria:</strong> <?php echo $row['categoria'] ?: 'Não especificada'; ?></p>
                                        <p><i class="fas fa-boxes"></i> <strong>Quantidade:</strong> <?php echo $row['quantidade']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Nenhum livro encontrado.
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1&search=<?php echo $search; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);

                        if ($start_page > 1) {
                            echo '<span class="btn btn-outline-secondary disabled">...</span>';
                        }

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" 
                               class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor;

                        if ($end_page < $total_pages) {
                            echo '<span class="btn btn-outline-secondary disabled">...</span>';
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="?page=<?php echo $total_pages; ?>&search=<?php echo $search; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>

        <a href="dashboard.php" class="btn-back mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para o Painel
        </a>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir este livro?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Confirmar Exclusão</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDetails(bookId) {
            const details = document.getElementById('details-' + bookId);
            const button = event.target.closest('.btn-action');
            
            if (details.style.display === 'block') {
                details.style.display = 'none';
                button.innerHTML = '<i class="fas fa-info-circle"></i> Detalhes';
            } else {
                details.style.display = 'block';
                button.innerHTML = '<i class="fas fa-times-circle"></i> Fechar';
            }
        }

        function confirmarExclusao(bookId) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('confirmDelete').href = 'excluir_livro.php?id=' + bookId;
            modal.show();
        }

        // Animação suave ao scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Lazy loading para imagens
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[loading="lazy"]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
