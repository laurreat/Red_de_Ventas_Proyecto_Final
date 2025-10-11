/**
 * Productos Create
 * Funcionalidad para crear productos
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
            document.getElementById('placeholderImage').style.display = 'none';
        }

        reader.readAsDataURL(input.files[0]);
    }
}
