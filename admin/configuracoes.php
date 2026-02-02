<?php
/**
 * ============================================
 * Configurações do Sistema - Admin
 * ============================================
 * Permite ao admin alterar informações de contacto
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Configurações';

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    try {
        $updated = 0;
        foreach ($_POST as $key => $value) {
            if ($key !== 'update_settings') {
                // Sanitizar valor
                $cleanValue = trim($value);
                
                // Atualizar configuração
                dbQuery(
                    "UPDATE settings SET setting_value = :value, updated_at = NOW() WHERE setting_key = :key",
                    [':value' => $cleanValue, ':key' => $key]
                );
                $updated++;
            }
        }
        
        header('Location: configuracoes.php?success=updated');
        exit;
    } catch (Exception $e) {
        $error = 'Erro ao atualizar configurações: ' . $e->getMessage();
    }
}

// Buscar todas as configurações
try {
    $settingsRaw = dbQuery("SELECT * FROM settings ORDER BY id ASC");
    $settings = [];
    foreach ($settingsRaw as $setting) {
        $settings[$setting['setting_key']] = $setting;
    }
} catch (Exception $e) {
    $settings = [];
    $error = 'Erro ao carregar configurações: ' . $e->getMessage();
}

// Mensagens
$success = $_GET['success'] ?? '';
$errorMsg = $_GET['error'] ?? '';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Configurações do Sistema</h1>
        <p class="page-subtitle">Gerira as informações globais e preferências do site</p>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-light border" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i> Atualizar
        </button>
    </div>
</div>

<form method="POST" action="" id="settingsForm">
    <input type="hidden" name="update_settings" value="1">
    <div class="row g-4">
    <div class="row g-4">
        <!-- Company Info - Full Width -->
        <div class="col-12">
            <div class="settings-card h-100">
                <div class="card-header-custom">
                    <div class="icon-box bg-primary-soft text-primary">
                        <i class="bi bi-building"></i>
                    </div>
                    <h5 class="card-title mb-0">Informações da Empresa</h5>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <label class="form-label">Nome da Empresa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($settings['company_name']['setting_value'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <label class="form-label">Email de Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="company_email" class="form-control" value="<?php echo htmlspecialchars($settings['company_email']['setting_value'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <label class="form-label">Telefone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" name="company_phone" class="form-control" value="<?php echo htmlspecialchars($settings['company_phone']['setting_value'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="col-xl-8 col-md-6 mb-4">
                            <label class="form-label">Morada Principal</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="company_address" class="form-control" value="<?php echo htmlspecialchars($settings['company_address']['setting_value'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-md-6 mb-4">
                            <label class="form-label">Cidade</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-pin-map"></i></span>
                                <input type="text" name="company_city" class="form-control" value="<?php echo htmlspecialchars($settings['company_city']['setting_value'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="col-12 mb-0">
                            <label class="form-label">Horário de Funcionamento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <textarea name="company_hours" class="form-control" rows="3" required><?php 
                                    $hours = $settings['company_hours']['setting_value'] ?? '';
                                    echo htmlspecialchars(implode("\n", array_map('trim', explode("\n", $hours)))); 
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social & Map & Stock - Two Columns -->
        <div class="col-lg-6">
            <!-- Social Media -->
            <div class="settings-card mb-4">
                <div class="card-header-custom">
                    <div class="icon-box bg-success-soft text-success">
                        <i class="bi bi-share"></i>
                    </div>
                    <h5 class="card-title mb-0">Redes Sociais</h5>
                </div>
                
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label">WhatsApp (Nº com País)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="tel" name="whatsapp_number" class="form-control" value="<?php echo htmlspecialchars($settings['whatsapp_number']['setting_value'] ?? ''); ?>" placeholder="351912345678">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Facebook URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                            <input type="url" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($settings['facebook_url']['setting_value'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Instagram URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                            <input type="url" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($settings['instagram_url']['setting_value'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <!-- Location Settings -->
            <div class="settings-card h-100">
                <div class="card-header-custom">
                    <div class="icon-box bg-danger-soft text-danger">
                        <i class="bi bi-map"></i>
                    </div>
                    <h5 class="card-title mb-0">Localização e Stock</h5>
                </div>
                
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="maps_latitude" class="form-control" value="<?php echo htmlspecialchars($settings['maps_latitude']['setting_value'] ?? ''); ?>" placeholder="38.7223">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="maps_longitude" class="form-control" value="<?php echo htmlspecialchars($settings['maps_longitude']['setting_value'] ?? ''); ?>" placeholder="-9.1393">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Google Maps Embed URL</label>
                        <textarea name="maps_embed_url" class="form-control" rows="2"><?php echo htmlspecialchars($settings['maps_embed_url']['setting_value'] ?? ''); ?></textarea>
                        <div class="form-text">Link "Incorporar um mapa" do Google Maps.</div>
                    </div>

                    <hr class="my-4 op-1">

                    <div class="mb-0">
                        <label class="form-label text-dark fw-bold">Definição de Semi-novo (Km)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-speedometer2"></i></span>
                            <input type="number" name="semi_new_max_km" class="form-control" value="<?php echo htmlspecialchars($settings['semi_new_max_km']['setting_value'] ?? '10000'); ?>" required>
                        </div>
                        <div class="form-text">Viaturas com menos Km que isto serão marcadas como "Semi-novo".</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
        <!-- Homepage Text & Statistics - Full Width -->
        <div class="col-12">
            <div class="settings-card h-100">
                <div class="card-header-custom">
                    <div class="icon-box bg-warning-soft text-warning">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h5 class="card-title mb-0">Personalização da Homepage</h5>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="form-label">Subtítulo do Hero</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <textarea name="home_hero_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($settings['home_hero_subtitle']['setting_value'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-text">Texto exibido abaixo do título principal ("O seu stand de confiança...").</div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Anos de Experiência</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                <input type="text" name="home_stats_years" class="form-control" value="<?php echo htmlspecialchars($settings['home_stats_years']['setting_value'] ?? ''); ?>" placeholder="20+">
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Clientes Satisfeitos</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-emoji-smile"></i></span>
                                <input type="text" name="home_stats_customers" class="form-control" value="<?php echo htmlspecialchars($settings['home_stats_customers']['setting_value'] ?? ''); ?>" placeholder="2000+">
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Garantia</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="text" name="home_stats_warranty" class="form-control" value="<?php echo htmlspecialchars($settings['home_stats_warranty']['setting_value'] ?? ''); ?>" placeholder="100%">
                            </div>
                        </div>

                        <div class="col-12 mb-0">
                            <label class="form-label">Texto do Rodapé</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <textarea name="footer_text" class="form-control" rows="2"><?php echo htmlspecialchars($settings['footer_text']['setting_value'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-text">Texto exibido na secção "Sobre" do rodapé.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Action Bar -->
    <div class="form-actions-bar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> As alterações afetam todo o site imediatamente.</span>
            <button type="submit" name="update_settings" class="btn btn-primary btn-lg px-5 shadow-sm">
                <i class="bi bi-save me-2"></i> Guardar Alterações
            </button>
        </div>
    </div>
</form>

<style>
/* Premium Card Styling */
.settings-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
}

.settings-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
}

.card-header-custom {
    padding: 1.5rem 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: transparent;
}

.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-primary-soft { background: #eff6ff; color: var(--primary-color); }
.bg-success-soft { background: #f0fdf4; color: #16a34a; }
.bg-danger-soft { background: #fef2f2; color: #dc2626; }

.form-label {
    font-weight: 500;
    color: #374151;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #64748b;
}

.form-control {
    border-color: #e2e8f0;
    padding: 0.65rem 1rem;
    font-size: 0.95rem;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
}

.form-text {
    font-size: 0.85rem;
    color: #9ca3af;
}

/* Footer Action Bar */
.form-actions-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: white;
    padding: 1rem 2rem 1rem calc(260px + 2rem); /* 260px sidebar + 2rem spacing */
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
    z-index: 1050; /* Above everything */
    box-sizing: border-box;
}

.form-actions-bar .btn {
    white-space: nowrap;
    min-width: fit-content;
}

@media (max-width: 992px) {
    .form-actions-bar {
        padding-left: calc(80px + 2rem);
    }
}

@media (max-width: 768px) {
    .form-actions-bar {
        padding: 1rem;
        position: sticky; /* Convert to simple sticky on mobile if fixed causes issues */
    }
    
    .form-actions-bar .container-fluid {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions-bar .text-muted {
        display: none;
    }
    
    .form-actions-bar .btn {
        width: 100%;
    }
}

/* Page Spacing for Footer */
#settingsForm {
    padding-bottom: 100px;
}
</style>

<script>
// SweetAlert2 Configuration
document.addEventListener('DOMContentLoaded', function() {
    // Check URL parameters for status
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success === 'updated') {
        Swal.fire({
            icon: 'success',
            title: 'Configurações Guardadas!',
            text: 'As alterações foram aplicadas com sucesso.',
            confirmButtonColor: 'var(--primary-color)',
            timer: 3000,
            timerProgressBar: true
        });
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    } else if (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: decodeURIComponent(error),
            confirmButtonColor: '#dc2626'
        });
    }

    // Form Loading State
    const form = document.getElementById('settingsForm');
    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type="submit"]');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
        btn.disabled = true;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
