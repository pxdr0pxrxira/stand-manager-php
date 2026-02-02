            <!-- Footer -->
            <footer style="background: transparent; padding: 1.5rem 0; text-align: center; color: var(--text-gray); font-size: 0.9rem; border-top: 1px solid #e5e7eb; margin-top: auto;">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Stand Automóvel - Painel de Administração</p>
            </footer>
        </div> <!-- .admin-content -->
    </main> <!-- .admin-main-content -->
</div> <!-- .admin-layout -->
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Image Preview
        const imageInput = document.getElementById('imagem');
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let preview = document.getElementById('imagePreview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.id = 'imagePreview';
                            imageInput.parentNode.appendChild(preview);
                        }
                        preview.innerHTML = '<img src="' + e.target.result + '" class="image-preview" alt="Preview">';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Confirm Delete with SweetAlert2
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const carId = this.dataset.id;
                const carName = this.dataset.name || 'esta viatura';
                
                Swal.fire({
                    title: 'Eliminar Viatura?',
                    html: `Tem a certeza que deseja eliminar <strong>${carName}</strong>?<br><small class="text-muted">Esta ação não pode ser revertida.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Sim, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-custom-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Criar formulário e submeter via POST
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'delete.php';
                        
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'id';
                        input.value = carId;
                        
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
