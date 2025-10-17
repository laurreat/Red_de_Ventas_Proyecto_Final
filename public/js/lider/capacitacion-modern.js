class CapacitacionManager{constructor(){this.toastTimeout=null;this.loadingElement=null;this.currentModal=null;this.init()}init(){this.createLoadingOverlay();this.initEventListeners();document.addEventListener('keydown',e=>{if(e.key==='Escape'){this.closeModal()}})}createLoadingOverlay(){const loading=document.createElement('div');loading.className='capacitacion-loading';loading.innerHTML='<div class="capacitacion-spinner"></div>';document.body.appendChild(loading);this.loadingElement=loading}showLoading(){if(this.loadingElement){this.loadingElement.classList.add('active')}}hideLoading(){if(this.loadingElement){this.loadingElement.classList.remove('active')}}showToast(message,type='success'){this.hideToast();const toast=document.createElement('div');toast.className=`capacitacion-toast ${type}`;const icons={success:'✓',error:'✕',warning:'⚠',info:'ℹ'};toast.innerHTML=`
        <div class="capacitacion-toast-icon">${icons[type]||icons.info}</div>
        <div class="capacitacion-toast-content">
            <p class="capacitacion-toast-message">${message}</p>
        </div>
        <button class="capacitacion-toast-close" onclick="capacitacionManager.hideToast()">×</button>
    `;document.body.appendChild(toast);setTimeout(()=>toast.classList.add('show'),10);this.toastTimeout=setTimeout(()=>this.hideToast(),5000)}hideToast(){const toast=document.querySelector('.capacitacion-toast');if(toast){toast.classList.remove('show');setTimeout(()=>toast.remove(),300)}if(this.toastTimeout){clearTimeout(this.toastTimeout)}}createModal(id,title,content,footer=''){const existingModal=document.getElementById(id);if(existingModal){existingModal.remove()}const backdrop=document.createElement('div');backdrop.className='capacitacion-modal-backdrop';backdrop.id=`${id}-backdrop`;const modal=document.createElement('div');modal.className='capacitacion-modal';modal.id=id;modal.innerHTML=`
        <div class="capacitacion-modal-header">
            <h3 class="capacitacion-modal-title">${title}</h3>
            <button class="capacitacion-modal-close" onclick="capacitacionManager.closeModal('${id}')">×</button>
        </div>
        <div class="capacitacion-modal-body">${content}</div>
        ${footer?`<div class="capacitacion-modal-footer">${footer}</div>`:''}
    `;document.body.appendChild(backdrop);document.body.appendChild(modal);backdrop.addEventListener('click',()=>this.closeModal(id));return{modal,backdrop}}showModal(id){const modal=document.getElementById(id);const backdrop=document.getElementById(`${id}-backdrop`);if(modal&&backdrop){setTimeout(()=>{backdrop.classList.add('active');modal.classList.add('active')},10);this.currentModal=id}}closeModal(id=null){const modalId=id||this.currentModal;if(!modalId)return;const modal=document.getElementById(modalId);const backdrop=document.getElementById(`${modalId}-backdrop`);if(modal&&backdrop){modal.classList.remove('active');backdrop.classList.remove('active');setTimeout(()=>{modal.remove();backdrop.remove()},300)}this.currentModal=null}initEventListeners(){document.querySelectorAll('.capacitacion-module-card').forEach(card=>{card.addEventListener('click',e=>{if(!e.target.closest('button')){const moduleId=card.dataset.moduleId;if(moduleId){this.verDetalleModulo(moduleId)}}})});const deleteButtons=document.querySelectorAll('[data-action="delete"]');deleteButtons.forEach(btn=>{btn.addEventListener('click',e=>{e.stopPropagation();const moduleId=btn.dataset.moduleId;const moduleTitle=btn.dataset.moduleTitle;this.confirmarEliminar(moduleId,moduleTitle)})})}verDetalleModulo(moduleId){window.location.href=`/lider/capacitacion/${moduleId}`}editarModulo(moduleId){window.location.href=`/lider/capacitacion/${moduleId}/edit`}confirmarEliminar(moduleId,moduleTitle){const content=`
        <div style="text-align:center;padding:2rem 1rem;">
            <div style="font-size:4rem;color:var(--danger);margin-bottom:1rem;">⚠</div>
            <h4 style="margin-bottom:1rem;">¿Eliminar capacitación?</h4>
            <p style="color:var(--gray-600);margin-bottom:2rem;">
                ¿Estás seguro de eliminar "<strong>${moduleTitle}</strong>"?<br>
                Esta acción no se puede deshacer.
            </p>
        </div>
    `;const footer=`
        <button onclick="capacitacionManager.closeModal('deleteModal')" class="capacitacion-action-btn-edit">
            Cancelar
        </button>
        <button onclick="capacitacionManager.eliminarModulo('${moduleId}')" class="capacitacion-action-btn-delete">
            <i class="bi bi-trash"></i> Eliminar
        </button>
    `;this.createModal('deleteModal','Confirmar Eliminación',content,footer);this.showModal('deleteModal')}async eliminarModulo(moduleId){this.closeModal('deleteModal');this.showLoading();try{const response=await fetch(`/lider/capacitacion/${moduleId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json','Content-Type':'application/json'}});if(response.ok){this.hideLoading();this.showToast('Capacitación eliminada correctamente','success');setTimeout(()=>{location.reload()},1500)}else{throw new Error('Error al eliminar')}}catch(error){this.hideLoading();this.showToast('Error al eliminar la capacitación','error')}}asignarModulo(moduleId,vendedorIds){this.showLoading();fetch('/lider/capacitacion/asignar',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({modulo_id:moduleId,miembro_ids:vendedorIds})}).then(response=>response.json()).then(data=>{this.hideLoading();if(data.success){this.showToast('Módulo asignado correctamente','success');setTimeout(()=>location.reload(),1500)}else{this.showToast('Error al asignar módulo','error')}}).catch(()=>{this.hideLoading();this.showToast('Error de conexión','error')})}toggleTodos(checkbox){const miembroChecks=document.querySelectorAll('.miembro-check');miembroChecks.forEach(check=>{check.checked=checkbox.checked})}validarFormulario(formId){const form=document.getElementById(formId);if(!form)return false;const titulo=form.querySelector('[name="titulo"]');const descripcion=form.querySelector('[name="descripcion"]');const duracion=form.querySelector('[name="duracion"]');const nivel=form.querySelector('[name="nivel"]');if(!titulo||!titulo.value.trim()){this.showToast('El título es requerido','warning');titulo.focus();return false}if(!descripcion||!descripcion.value.trim()){this.showToast('La descripción es requerida','warning');descripcion.focus();return false}if(!duracion||!duracion.value.trim()){this.showToast('La duración es requerida','warning');duracion.focus();return false}if(!nivel||!nivel.value){this.showToast('El nivel es requerido','warning');nivel.focus();return false}return true}agregarObjetivo(){const container=document.getElementById('objetivos-container');const count=container.querySelectorAll('.objetivo-item').length;const newItem=document.createElement('div');newItem.className='objetivo-item mb-2 d-flex gap-2';newItem.innerHTML=`
        <input type="text" name="objetivos[]" class="capacitacion-form-input" placeholder="Objetivo ${count+1}" required>
        <button type="button" onclick="this.parentElement.remove()" class="capacitacion-action-btn-delete" style="flex-shrink:0;padding:.75rem 1rem;">
            <i class="bi bi-trash"></i>
        </button>
    `;container.appendChild(newItem)}agregarRecurso(){const container=document.getElementById('recursos-container');const count=container.querySelectorAll('.recurso-item').length;const newItem=document.createElement('div');newItem.className='recurso-item mb-3 p-3 border rounded';newItem.innerHTML=`
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Recurso ${count+1}</h6>
            <button type="button" onclick="this.closest('.recurso-item').remove()" class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <input type="text" name="recursos[${count}][titulo]" class="capacitacion-form-input mb-2" placeholder="Título del recurso" required>
        <input type="url" name="recursos[${count}][url]" class="capacitacion-form-input mb-2" placeholder="URL del recurso">
        <select name="recursos[${count}][tipo]" class="capacitacion-form-select">
            <option value="documento">Documento</option>
            <option value="video">Video</option>
            <option value="enlace">Enlace</option>
            <option value="presentacion">Presentación</option>
        </select>
    `;container.appendChild(newItem)}}const capacitacionManager=new CapacitacionManager();document.addEventListener('DOMContentLoaded',()=>{const cards=document.querySelectorAll('.capacitacion-module-card, .capacitacion-stat-card');cards.forEach((card,index)=>{card.style.animationDelay=`${index*0.05}s`});const progressBars=document.querySelectorAll('.capacitacion-progress-fill');progressBars.forEach(bar=>{const width=bar.style.width||'0%';bar.style.width='0%';setTimeout(()=>{bar.style.width=width},500)})});