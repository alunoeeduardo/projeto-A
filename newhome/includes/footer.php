<?php
// includes/footer.php
?>
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-home"></i>
                        <span>New<span>Home</span></span>
                    </div>
                    <p class="footer-description">
                        Sistema completo para gerenciamento de imóveis. 
                        Conectamos proprietários, corretores e clientes em uma única plataforma.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>Links Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="../index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="../imoveis.php"><i class="fas fa-chevron-right"></i> Imóveis</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Sobre Nós</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Contato</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Para Clientes</h3>
                    <ul class="footer-links">
                        <li><a href="../cadastro.php"><i class="fas fa-chevron-right"></i> Criar Conta</a></li>
                        <li><a href="../login.php"><i class="fas fa-chevron-right"></i> Login</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Como Funciona</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Contato</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> São Paulo, SP</li>
                        <li><i class="fas fa-phone"></i> (11) 9999-9999</li>
                        <li><i class="fas fa-envelope"></i> contato@newhome.com</li>
                        <li><i class="fas fa-clock"></i> Seg-Sex: 9h-18h</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    &copy; <?php echo date('Y'); ?> New Home. Todos os direitos reservados.
                </div>
                <div class="footer-links-bottom">
                    <a href="#">Política de Privacidade</a>
                    <span>•</span>
                    <a href="#">Termos de Uso</a>
                    <span>•</span>
                    <a href="#">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Tema claro/escuro
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // Verificar preferência salva
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            body.classList.add('dark-theme');
        }
        
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const currentTheme = body.classList.contains('dark-theme') ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
        });

        // Dropdown do usuário
        const userBtn = document.querySelector('.user-btn');
        if(userBtn) {
            userBtn.addEventListener('click', () => {
                document.querySelector('.dropdown-menu').classList.toggle('show');
            });
            
            // Fechar ao clicar fora
            document.addEventListener('click', (e) => {
                if (!userBtn.contains(e.target)) {
                    document.querySelector('.dropdown-menu').classList.remove('show');
                }
            });
        }
    </script>
</body>
</html>