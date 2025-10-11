/**
 * Productos Edit
 * Funcionalidad para editar productos
 */

/**
 * Vista previa de imagen
 * @param {HTMLInputElement} input - Input file de imagen
 */
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';

            // Ocultar imagen actual si existe
            const currentImage = document.getElementById('currentImage');
            if (currentImage) {
                currentImage.style.opacity = '0.5';
            }

            // Ocultar placeholder si existe
            const placeholder = document.getElementById('placeholderImage');
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}
