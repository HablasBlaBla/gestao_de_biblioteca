<?php
include('../_BACK-END/visualizar_livros.php')
?>

<!DOCTYPE html>
<html lang="pt" data-theme="light">
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
            --primary-hover: #00695c;
            --secondary-color: #f0f4f8;
            --text-color: #212121;
            --card-bg: #ffffff;
            --body-bg: linear-gradient(135deg, #f0f4f8, #e0e7ff);
            --shadow-color: rgba(0, 0, 0, 0.1);
            --icon-color: #00796b;
            --danger-color: #dc3545;
            --danger-hover: #c82333;
            --header-bg: #00796b;
            --stats-card-bg: #ffffff;
            --quick-action-bg: #e3f2fd;
            --notification-badge: #ff5722;
        }

        [data-theme="dark"] {
            --primary-color: #4db6ac;
            --primary-hover: #26a69a;
            --secondary-color: #121212;
            --text-color: #e0e0e0;
            --card-bg: #1e1e1e;
            --body-bg: linear-gradient(135deg, #121212, #0d0d1a);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --icon-color: #4db6ac;
            --danger-color: #f44336;
            --danger-hover: #d32f2f;
            --header-bg: #004d40;
            --stats-card-bg: #1e1e1e;
            --quick-action-bg: #263238;
            --notification-badge: #ff7043;
        }

        body {
            background: var(--body-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            transition: all 0.8s ease;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .page-header {
            background-color: var(--header-bg);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.8s ease;
        }

        .page-header h1 {
            font-weight: 600;
            font-size: 2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 8px 24px var(--shadow-color);
            transition: all 0.5s ease;
            background-color: var(--card-bg);
            border: none;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            border-radius: 12px 12px 0 0;
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            transition: all 0.5s ease;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 1.5rem;
            background-color: var(--card-bg);
            transition: all 0.5s ease;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .stat-card {
            background-color: var(--stats-card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 4px 12px var(--shadow-color);
            transition: all 0.4s ease;
            cursor: pointer;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px var(--shadow-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-color);
            opacity: 0.8;
        }

        .book-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px var(--shadow-color);
            transition: all 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px var(--shadow-color);
        }

        .book-cover {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 1.5rem;
            box-shadow: 0 2px 8px var(--shadow-color);
        }

        .book-info {
            flex: 1;
        }

        .book-title {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .book-meta {
            color: var(--text-color);
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .book-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow-color);
        }

        .book-details {
            margin-top: 1rem;
            padding: 1rem;
            background-color: var(--secondary-color);
            border-radius: 6px;
            display: none;
        }

        .search-container {
            margin-bottom: 1.5rem;
        }

        .search-input {
            border-radius: 6px;
            border: 1px solid var(--primary-color);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            color: var(--primary-hover);
            transform: translateX(-3px);
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.5s ease;
            border: none;
            outline: none;
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(15deg);
        }

        /* Theme Animation Overlay */
        .theme-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .theme-animation.active {
            opacity: 1;
        }

        .sun-moon-container {
            width: 200px;
            height: 200px;
            position: relative;
            perspective: 1000px;
        }

        .sun-moon {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 1.5s ease;
        }

        .sun, .moon {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sun {
            background: radial-gradient(circle, #ffeb3b, #ffc107);
            box-shadow: 0 0 80px #ffeb3b;
            transform: rotateY(0deg);
        }

        .sun::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, transparent 60%, rgba(255, 235, 59, 0.3) 100%);
            border-radius: 50%;
            animation: pulse 2s infinite alternate;
        }

        .moon {
            background: radial-gradient(circle, #e0e0e0, #9e9e9e);
            box-shadow: 0 0 80px #e0e0e0;
            transform: rotateY(180deg);
        }

        .moon::before {
            content: '';
            position: absolute;
            background-color: #424242;
            border-radius: 50%;
            width: 30%;
            height: 30%;
            top: 20%;
            left: 20%;
            box-shadow: 
                40px -20px 0 -5px #424242,
                60px 30px 0 -10px #424242,
                20px 60px 0 -7px #424242,
                40px 70px 0 -12px #424242;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(1.1); opacity: 0.6; }
        }

        /* Floating Stars (for dark theme) */
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            opacity: 0;
            transition: opacity 1s ease;
        }

        [data-theme="dark"] .stars {
            opacity: 1;
        }

        .star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: twinkle var(--duration) infinite ease-in-out;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .stats-container {
                flex-direction: column;
            }
            
            .stat-card {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding-bottom: 100px;
            }
            
            .theme-toggle {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }
            
            .book-card {
                flex-direction: column;
            }
            
            .book-cover {
                width: 100%;
                height: auto;
                max-height: 300px;
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .book-actions {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Theme Animation Overlay -->
    <div class="theme-animation" id="themeAnimation">
        <div class="sun-moon-container">
            <div class="sun-moon" id="sunMoon">
                <div class="sun"></div>
                <div class="moon"></div>
            </div>
        </div>
    </div>
    
    <!-- Floating Stars -->
    <div class="stars" id="stars"></div>
    
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <div class="page-header">
        <div class="container">
            <h1 class="text-center mb-0">
                <i class="fas fa-book"></i> Biblioteca Digital
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
            <div class="stat-card animate-fade-in">
                <div class="stat-number"><?php echo $total_books; ?></div>
                <div class="stat-label">Total de Livros</div>
            </div>
            <div class="stat-card animate-fade-in">
                <div class="stat-number"><?php echo $result->num_rows; ?></div>
                <div class="stat-label">Livros nesta página</div>
            </div>
            <div class="stat-card animate-fade-in">
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
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;
        const themeAnimation = document.getElementById('themeAnimation');
        const sunMoon = document.getElementById('sunMoon');
        const starsContainer = document.getElementById('stars');
        
        // Check for saved theme preference or use preferred color scheme
        const savedTheme = localStorage.getItem('theme') || 
                          (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        // Apply saved theme
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        // Create stars for dark theme
        createStars();
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Show animation
            showThemeAnimation(newTheme);
            
            // Change theme after animation
            setTimeout(() => {
                html.setAttribute('data-theme', newTheme);
                updateThemeIcon(newTheme);
                localStorage.setItem('theme', newTheme);
            }, 800);
        });
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
        
        function showThemeAnimation(theme) {
            themeAnimation.classList.add('active');
            
            if (theme === 'dark') {
                sunMoon.style.transform = 'rotateY(180deg)';
            } else {
                sunMoon.style.transform = 'rotateY(0deg)';
            }
            
            setTimeout(() => {
                themeAnimation.classList.remove('active');
            }, 1500);
        }
        
        function createStars() {
            const starCount = 100;
            
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                
                // Random size between 1 and 3px
                const size = Math.random() * 2 + 1;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                
                // Random position
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                
                // Random animation duration and delay
                const duration = Math.random() * 5 + 3;
                star.style.setProperty('--duration', `${duration}s`);
                
                starsContainer.appendChild(star);
            }
        }
        
        // Add animation delays for stats cards
        document.querySelectorAll('.animate-fade-in').forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1 + 0.2}s`;
        });
        
        // Add click animation to stats cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });

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