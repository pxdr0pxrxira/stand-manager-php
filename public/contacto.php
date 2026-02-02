<?php
/**
 * ============================================
 * Página de Contacto - Estilo Paulimane
 * ============================================
 */


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/language.php';

$pageTitle = t('contact.title');
$noHero = true; // Navbar branca desde o início

// Carregar configurações
$settings = getAllSettings();

// Processar mensagens de feedback
$success = isset($_GET['success']) && $_GET['success'] == '1';
$error = isset($_GET['error']) ? $_GET['error'] : null;

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contact Hero -->
<section style="padding: 8rem 0 4rem; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
    <div class="container">
        <div style="text-align: center; color: white;">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">
                <?php echo t('contact.title'); ?>
            </h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                <?php echo t('contact.subtitle'); ?>
            </p>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section style="padding: 4rem 0; background: var(--bg-light);">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <div class="contact-card">
                    <div class="contact-card-header">
                        <i class="bi bi-envelope-fill"></i>
                        <h2><?php echo t('contact.form_title'); ?></h2>
                        <p><?php echo t('contact.form_subtitle'); ?></p>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <strong><?php echo t('contact.success_title'); ?></strong>
                                <p><?php echo t('contact.success_msg'); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <strong><?php echo t('contact.error_title'); ?></strong>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form onsubmit="handleContactEmail(event)" class="contact-form">
                        <div class="form-group">
                            <label for="nome">
                                <i class="bi bi-person"></i>
                                <?php echo t('contact.labels.name'); ?>
                            </label>
                            <input 
                                type="text" 
                                id="nome" 
                                name="nome" 
                                required 
                                placeholder="<?php echo t('contact.labels.name_placeholder'); ?>"
                                class="form-input"
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="bi bi-envelope"></i>
                                <?php echo t('contact.labels.email'); ?>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                placeholder="<?php echo t('contact.labels.email_placeholder'); ?>"
                                class="form-input"
                            >
                        </div>

                        <div class="form-group">
                            <label for="telefone">
                                <i class="bi bi-telephone"></i>
                                <?php echo t('contact.labels.phone'); ?>
                            </label>
                            <input 
                                type="tel" 
                                id="telefone" 
                                name="telefone" 
                                placeholder="+351 912 345 678"
                                class="form-input"
                            >
                        </div>

                        <div class="form-group">
                            <label for="assunto">
                                <i class="bi bi-tag"></i>
                                <?php echo t('contact.labels.subject'); ?>
                            </label>
                            <input 
                                type="text" 
                                id="assunto" 
                                name="assunto" 
                                required 
                                placeholder="<?php echo t('contact.labels.subject_placeholder'); ?>"
                                class="form-input"
                            >
                        </div>

                        <div class="form-group">
                            <label for="mensagem">
                                <i class="bi bi-chat-text"></i>
                                <?php echo t('contact.labels.message'); ?>
                            </label>
                            <textarea 
                                id="mensagem" 
                                name="mensagem" 
                                required 
                                rows="8" 
                                placeholder="<?php echo t('contact.labels.message_placeholder'); ?>"
                                class="form-input"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-envelope-forward-fill"></i>
                            <?php echo t('contact.labels.send_btn'); ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info & Map -->
            <div class="contact-info-wrapper">
                <!-- Contact Info Cards -->
                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h3><?php echo t('contact.info_address'); ?></h3>
                        <p><?php echo htmlspecialchars($settings['company_address']); ?><br><?php echo htmlspecialchars($settings['company_city']); ?></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <h3><?php echo t('contact.info_phone'); ?></h3>
                        <p><a href="tel:<?php echo htmlspecialchars($settings['company_phone']); ?>"><?php echo htmlspecialchars($settings['company_phone']); ?></a></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <h3><?php echo t('contact.info_email'); ?></h3>
                        <p><a href="mailto:<?php echo htmlspecialchars($settings['company_email']); ?>"><?php echo htmlspecialchars($settings['company_email']); ?></a></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div>
                        <h3><?php echo t('contact.info_hours'); ?></h3>
                        <p><?php echo getFormattedHours(); ?></p>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="map-container">
                    <div class="map-header">
                        <h3><i class="bi bi-map"></i> <?php echo t('contact.location'); ?></h3>
                        <a 
                            href="<?php echo getMapsDirectionsUrl(); ?>" 
                            target="_blank" 
                            class="btn-directions"
                        >
                            <i class="bi bi-compass"></i>
                            <?php echo t('contact.get_directions'); ?>
                        </a>
                    </div>
                    <div class="map-embed">
                        <!-- Google Maps Embed -->
                        <iframe 
                            src="<?php echo htmlspecialchars($settings['maps_embed_url']); ?>" 
                            width="100%" 
                            height="100%" 
                            style="border:0; border-radius: 12px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>

                <!-- WhatsApp CTA -->
                <div class="whatsapp-cta">
                    <div class="whatsapp-icon">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <div class="whatsapp-content">
                        <h3><?php echo t('contact.whatsapp_title'); ?></h3>
                        <p><?php echo t('contact.whatsapp_subtitle'); ?></p>
                    </div>
                    <a href="<?php echo getWhatsAppUrl(); ?>" target="_blank" class="btn-whatsapp-contact">
                        <i class="bi bi-whatsapp"></i>
                        <?php echo t('contact.whatsapp_btn'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Contact Page Styles */
.contact-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 3rem;
    align-items: start;
}

.contact-card {
    background: var(--white);
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
}

.contact-card-header {
    text-align: center;
    margin-bottom: 2rem;
}

.contact-card-header i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.contact-card-header h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.contact-card-header p {
    color: var(--text-gray);
    font-size: 1rem;
}

/* Alerts */
.alert {
    display: flex;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.alert i {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert strong {
    display: block;
    margin-bottom: 0.25rem;
}

.alert p {
    margin: 0;
    font-size: 0.9rem;
}

.alert-success {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Form Styles */
.contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.95rem;
}

.form-group label i {
    color: var(--primary-color);
    font-size: 1.1rem;
}

.form-input {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    font-family: inherit;
    font-family: inherit;
    transition: var(--transition);
    background: var(--bg-light);
    color: var(--text-dark);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(242, 101, 34, 0.1);
}

.form-input::placeholder {
    color: var(--text-light);
}

textarea.form-input {
    resize: vertical;
    min-height: 120px;
}

.btn-submit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    margin-top: 1rem;
}

.btn-submit:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

/* Contact Info Cards */
.contact-info-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.contact-info-card {
    display: flex;
    gap: 1.25rem;
    padding: 1.5rem;
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow);
    transition: var(--transition);
}

.contact-info-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.info-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 1.5rem;
    color: white;
}

.contact-info-card h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: var(--text-dark);
}

.contact-info-card p {
    color: var(--text-gray);
    margin: 0;
    line-height: 1.6;
}

.contact-info-card a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.contact-info-card a:hover {
    text-decoration: underline;
}

/* Map Container */
.map-container {
    background: var(--white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: var(--bg-light);
    border-bottom: 1px solid #e5e7eb;
}

.map-header h3 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.map-header i {
    color: var(--primary-color);
}

.btn-directions {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--transition);
}

.btn-directions:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.map-embed {
    height: 350px;
    width: 100%;
}

/* WhatsApp CTA */
.whatsapp-cta {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
}

.whatsapp-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.whatsapp-icon i {
    font-size: 1.75rem;
    color: white;
}

.whatsapp-content {
    flex-grow: 1;
}

.whatsapp-content h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: white;
    margin: 0 0 0.25rem 0;
}

.whatsapp-content p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 0.9rem;
}

.btn-whatsapp-contact {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    color: #128C7E;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 700;
    transition: var(--transition);
    white-space: nowrap;
}

.btn-whatsapp-contact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 968px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .contact-card {
        padding: 2rem;
    }

    .map-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .btn-directions {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .contact-card {
        padding: 1.5rem;
    }

    .contact-card-header h2 {
        font-size: 1.5rem;
    }

    .whatsapp-cta {
        flex-direction: column;
        text-align: center;
    }

    .btn-whatsapp-contact {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function handleContactEmail(event) {
    event.preventDefault();
    
    // Get form values
    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const telefone = document.getElementById('telefone').value;
    const assunto = document.getElementById('assunto').value;
    const mensagem = document.getElementById('mensagem').value;
    
    // Company email
    const companyEmail = "<?php echo htmlspecialchars($settings['company_email']); ?>";
    
    // Construct email body
    const body = `Nome: ${nome}
Email: ${email}
Telefone: ${telefone}

Mensagem:
${mensagem}

--
Enviado através do site`;

    // Construct mailto link
    const mailtoLink = `mailto:${companyEmail}?subject=${encodeURIComponent(assunto + ' - Contacto Site')}&body=${encodeURIComponent(body)}`;
    
    // Open default mail client
    window.location.href = mailtoLink;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
