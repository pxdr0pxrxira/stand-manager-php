    </main>
    
    <?php 
    // Carregar configurações se ainda não carregadas
    if (!function_exists('getAllSettings')) {
        require_once __DIR__ . '/../config/settings.php';
    }
    $footerSettings = getAllSettings();
    ?>
    
    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Sobre -->
                <div>
                    <div class="footer-logo">
                        <i class="bi bi-car-front-fill"></i>
                        <span><?php echo htmlspecialchars($footerSettings['company_name']); ?></span>
                    </div>
                    <p class="footer-text">
                        <?php echo nl2br(htmlspecialchars(getSetting('footer_text'))); ?>
                    </p>
                </div>
                
                <!-- Links -->
                <div>
                    <h4 class="footer-title"><?php echo t('footer.quick_links'); ?></h4>
                    <ul class="footer-links">
                        <li><a href="index.php"><?php echo t('nav.home'); ?></a></li>
                        <li><a href="stock.php"><?php echo t('nav.stock'); ?></a></li>
                        <li><a href="stock.php?combustivel=Elétrico">Carros Elétricos</a></li> <!-- Could be translated if dynamic -->
                        <li><a href="stock.php?combustivel=Híbrido">Híbridos</a></li>
                    </ul>
                </div>
                
                <!-- Contactos -->
                <div>
                    <h4 class="footer-title"><?php echo t('footer.contact_info'); ?></h4>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt"></i>
                        <span><?php echo htmlspecialchars($footerSettings['company_address']); ?><br><?php echo htmlspecialchars($footerSettings['company_city']); ?></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone"></i>
                        <span><?php echo htmlspecialchars($footerSettings['company_phone']); ?></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope"></i>
                        <span><?php echo htmlspecialchars($footerSettings['company_email']); ?></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-clock"></i>
                        <span><?php echo getFormattedHours(); ?></span>
                    </div>
                </div>
                
                <!-- Horário -->
                <div>
                    <h4 class="footer-title">Redes Sociais</h4>
                    <div class="social-links">
                        <a href="<?php echo htmlspecialchars($footerSettings['facebook_url']); ?>" class="social-link" aria-label="Facebook" <?php echo ($footerSettings['facebook_url'] === '#') ? 'style="display:none;"' : ''; ?>>
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="<?php echo htmlspecialchars($footerSettings['instagram_url']); ?>" class="social-link" aria-label="Instagram" <?php echo ($footerSettings['instagram_url'] === '#') ? 'style="display:none;"' : ''; ?>>
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="<?php echo getWhatsAppUrl(); ?>" class="social-link" aria-label="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($footerSettings['company_name']); ?>. Todos os direitos reservados.</p>
                <div class="social-links">
                    <a href="../admin/login.php" class="social-link" title="Backoffice">
                        <i class="bi bi-gear"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" onclick="scrollToTop()">
        <i class="bi bi-chevron-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script src="assets/js/main.js?v=<?php echo time(); ?>_9"></script>
</body>
</html>
