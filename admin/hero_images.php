<?php
/**
 * ============================================
 * Gestão de Imagens Hero - Admin
 * ============================================
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Imagens Hero';
$uploadDir = __DIR__ . '/../uploads/';

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Processar Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hero_images'])) {
    try {
        $files = $_FILES['hero_images'];
        $uploadedCount = 0;
        
        // Loop through all uploaded files
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // Validate type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($files['type'][$i], $allowedTypes)) {
                    
                    // Generate unique name
                    $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $filename = 'hero_' . uniqid() . '_' . $i . '.' . $extension;
                    $uploadPath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], $uploadPath)) {
                        // Save to DB
                        dbExecute("INSERT INTO hero_images (image_path) VALUES (:path)", [':path' => $filename]);
                        $uploadedCount++;
                    }
                }
            }
        }
        
        if ($uploadedCount > 0) {
            header('Location: hero_images.php?status=success&message=Imagens carregadas com sucesso!');
            exit;
        } else {
            throw new Exception('Nenhuma imagem válida foi carregada. Tente apenas JPG, PNG ou WebP.');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Processar Delete
if (isset($_POST['delete_id'])) {
    try {
        $id = (int)$_POST['delete_id'];
        
        // Buscar imagem para apagar ficheiro
        $imgs = dbQuery("SELECT image_path FROM hero_images WHERE id = :id", [':id' => $id]);
        
        if (!empty($imgs)) {
            $path = $uploadDir . $imgs[0]['image_path'];
            if (file_exists($path)) {
                unlink($path);
            }
            
            dbExecute("DELETE FROM hero_images WHERE id = :id", [':id' => $id]);
            header('Location: hero_images.php?status=success&message=Imagem removida com sucesso!');
            exit;
        }
    } catch (Exception $e) {
        header('Location: hero_images.php?status=error&message=Erro ao apagar imagem.');
        exit;
    }
}

// Buscar imagens
$heroImages = dbQuery("SELECT * FROM hero_images ORDER BY created_at DESC");

require_once __DIR__ . '/includes/header.php';
?>

<style>
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        min-height: 250px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--primary-color);
        background: rgba(0, 51, 102, 0.05);
    }
    
    .upload-zone.has-files {
        border-style: solid;
        border-color: var(--primary-color);
        background: white;
    }

    .upload-icon {
        font-size: 3rem;
        color: #94a3b8;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .upload-zone:hover .upload-icon {
        transform: translateY(-5px);
        color: var(--primary-color);
    }

    .hero-card {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        background: white;
        height: 100%;
        position: relative;
        border: 1px solid #f1f5f9;
    }

    .hero-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .hero-image-container {
        position: relative;
        padding-top: 56.25%; /* 16:9 Aspect Ratio */
        overflow: hidden;
    }

    .hero-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .hero-card:hover .hero-image {
        transform: scale(1.05);
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .hero-card:hover .hero-overlay {
        opacity: 1;
    }
    
    /* Preview Grid in Upload Zone */
    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        width: 100%;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .preview-item {
        position: relative;
        aspect-ratio: 16/9;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Modal de Ecrã Inteiro */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 50px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        margin: auto;
        display: block;
        max-width: 90%;
        max-height: 90vh;
        object-fit: contain;
        animation: zoomIn 0.3s;
    }

    @keyframes zoomIn {
        from {transform:scale(0)}
        to {transform:scale(1)}
    }

    .close-modal {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
        z-index: 10000;
    }

    .close-modal:hover,
    .close-modal:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Imagens Hero</h1>
        <p class="page-subtitle">Gerira as imagens que aparecem no destaque da página inicial</p>
    </div>
    <div class="text-end">
        <span class="badge bg-light text-dark border p-2">
            <i class="bi bi-info-circle me-1"></i>
            Tamanho ideal: 1920x1080px
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- Upload Section -->
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">
                    <i class="bi bi-cloud-upload"></i> Carregar Novas Imagens
                </h5>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-zone" id="dropZone">
                    <div class="text-center p-5" id="uploadPrompt">
                        <i class="bi bi-images upload-icon"></i>
                        <h4>Arraste as imagens aqui</h4>
                        <p class="text-muted mb-4">Ou clique para selecionar arquivos do seu computador</p>
                        <button type="button" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-folder2-open me-2"></i> Selecionar Ficheiros
                        </button>
                    </div>
                    
                    <div class="preview-grid d-none" id="previewContainer"></div>
                    
                    <input type="file" name="hero_images[]" id="fileInput" class="d-none" accept="image/*" multiple required>
                </div>
                
                <div class="d-flex justify-content-end mt-3 d-none" id="uploadActions">
                    <button type="button" class="btn btn-outline-secondary me-2" id="btnReset">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="bi bi-upload me-2"></i> Carregar Imagens
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mb-0 fw-bold">Galeria Atual</h5>
            <span class="badge bg-primary rounded-pill ms-2"><?php echo count($heroImages); ?></span>
        </div>

        <?php if (empty($heroImages)): ?>
            <div class="admin-card text-center py-5">
                <div class="py-4">
                    <div class="mb-3">
                        <i class="bi bi-images text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h5 class="text-muted">Nenhuma imagem configurada</h5>
                    <p class="text-muted small">Carregue imagens acima para ativar o slider na página inicial.</p>
                </div>
            </div>
        <?php else: ?>
            <?php 
            $count = count($heroImages);
            $colClass = $count === 1 ? 'col-md-8 offset-md-2' : 'col-md-6 col-lg-4 col-xl-3';
            ?>
            <div class="row g-4">
                <?php foreach ($heroImages as $img): ?>
                    <div class="<?php echo $colClass; ?>">
                        <div class="hero-card">
                            <div class="hero-image-container">
                                <img src="../uploads/<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     class="hero-image" 
                                     alt="Hero Image"
                                     loading="lazy">
                                <div class="hero-overlay">
                                    <button type="button" onclick="openFullscreen('../uploads/<?php echo htmlspecialchars($img['image_path']); ?>')" 
                                            class="btn btn-light btn-sm rounded-circle shadow-sm"
                                            title="Ver Fullscreen">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button onclick="confirmDelete(<?php echo $img['id']; ?>)" 
                                            class="btn btn-danger btn-sm rounded-circle shadow-sm"
                                            title="Remover Imagem">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-3 bg-white border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-xs text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($img['created_at'])); ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <?php 
                                            $path = $uploadDir . $img['image_path'];
                                            echo file_exists($path) ? round(filesize($path) / 1024) . ' KB' : 'N/A';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" action="" class="d-none">
    <input type="hidden" name="delete_id" id="deleteId">
</form>

<!-- Modal Structure -->
<div id="imageModal" class="image-modal" onclick="closeFullscreen()">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="fullImage">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- File Upload Logic ---
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const previewContainer = document.getElementById('previewContainer');
    const uploadActions = document.getElementById('uploadActions');
    const btnReset = document.getElementById('btnReset');
    const uploadForm = document.getElementById('uploadForm');

    // Trigger file input on click
    dropZone.addEventListener('click', () => fileInput.click());

    // Drag and Drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('dragover');
    }

    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFiles(files);
    }

    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            uploadPrompt.classList.add('d-none');
            previewContainer.classList.remove('d-none');
            uploadActions.classList.remove('d-none');
            dropZone.classList.add('has-files');
            
            previewContainer.innerHTML = '';
            
            [...files].forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" onclick="openFullscreen('${e.target.result}')" 
                                    class="btn btn-primary btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm"
                                    title="Ver Fullscreen" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-arrows-fullscreen" style="font-size: 12px;"></i>
                            </button>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    btnReset.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        uploadPrompt.classList.remove('d-none');
        previewContainer.classList.add('d-none');
        uploadActions.classList.add('d-none');
        dropZone.classList.remove('has-files');
        previewContainer.innerHTML = '';
    });
    
    // Prevent dropzone click propagation from the reset button
    // (Handled by e.stopPropagation above)

    // --- Loading State ---
    uploadForm.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Carregando...';
        btn.disabled = true;
    });

    // --- Status Messages ---
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status && message) {
        Swal.fire({
            icon: status === 'success' ? 'success' : 'error',
            title: status === 'success' ? 'Sucesso!' : 'Erro!',
            text: message,
            confirmButtonColor: 'var(--primary-color)',
            timer: 3000,
            timerProgressBar: true
        });
        
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// --- Delete Confirmation ---
function confirmDelete(id) {
    Swal.fire({
        title: 'Tem a certeza?',
        text: "Esta imagem será removida permanentemente do site.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, apagar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    });
}

function openFullscreen(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("fullImage");
    modal.style.display = "flex";
    modalImg.src = src;
    document.body.style.overflow = "hidden";
}

function closeFullscreen() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeFullscreen();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
