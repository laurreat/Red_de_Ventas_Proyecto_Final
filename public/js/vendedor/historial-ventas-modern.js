/**Historial Ventas Manager-JS Profesional Minificado v2.0*/
class HistorialVentasManager{constructor(){this.modals=new Map();this.currentVenta=null;this.init()}init(){this.initAnimations();this.initModals();this.initEventListeners();this.initTableAnimations()}initAnimations(){const elements=document.querySelectorAll('.fade-in-up, .scale-in, .animate-delay-1, .animate-delay-2, .animate-delay-3');const observer=new IntersectionObserver((entries)=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.style.opacity='1'}})},{threshold:.1});elements.forEach(el=>observer.observe(el))}initModals(){const modalBackdrops=document.querySelectorAll('.historial-modal-backdrop');modalBackdrops.forEach(backdrop=>{backdrop.addEventListener('click',()=>this.closeAllModals())});const modalCloses=document.querySelectorAll('.historial-modal-close');modalCloses.forEach(btn=>{btn.addEventListener('click',()=>this.closeAllModals())});document.addEventListener('keydown',(e)=>{if(e.key==='Escape'){this.closeAllModals()}})}initEventListeners(){const filterInputs=document.querySelectorAll('.historial-filter-input, .historial-filter-select');filterInputs.forEach(input=>{input.addEventListener('change',()=>this.applyFilters())});const viewButtons=document.querySelectorAll('.historial-action-btn-view');viewButtons.forEach(btn=>{btn.addEventListener('click',()=>{const ventaId=btn.dataset.id;this.verDetalle(ventaId)})});const exportButtons=document.querySelectorAll('.historial-action-btn-export');exportButtons.forEach(btn=>{btn.addEventListener('click',()=>{const ventaId=btn.dataset.id;this.exportarVenta(ventaId)})});const exportAllBtn=document.getElementById('export-all-btn');if(exportAllBtn){exportAllBtn.addEventListener('click',()=>this.exportarTodas())}}initTableAnimations(){const rows=document.querySelectorAll('.historial-table tbody tr');rows.forEach((row,index)=>{row.style.animationDelay=`${index*.05}s`;row.classList.add('fade-in-up')})}applyFilters(){const form=document.querySelector('#historial-filter-form');if(form){this.showLoading();form.submit()}}verDetalle(ventaId){this.showLoading();fetch(`/vendedor/ventas/${ventaId}`).then(r=>r.json()).then(venta=>{this.currentVenta=venta;this.mostrarDetalleModal(venta)}).catch(err=>{console.error('Error cargando venta:',err);this.showToast('Error al cargar los detalles','error')}).finally(()=>this.hideLoading())}mostrarDetalleModal(venta){const productosHtml=venta.productos?.map((p,idx)=>`
<tr>
<td>${idx+1}</td>
<td>${p.nombre||'N/A'}</td>
<td>${p.codigo||'N/A'}</td>
<td>${p.cantidad}</td>
<td>$${parseFloat(p.precio_unitario||0).toFixed(2)}</td>
<td>$${parseFloat(p.subtotal||0).toFixed(2)}</td>
</tr>`).join('')||'<tr><td colspan="6" style="text-align:center;">Sin productos</td></tr>';const body=`
<div class="historial-detail-section">
<h4 class="historial-detail-title">\ud83d\udccb Informaci\u00f3n General</h4>
<div class="historial-detail-grid">
<div class="historial-detail-item">
<p class="historial-detail-label">N\u00famero de Venta</p>
<p class="historial-detail-value">${venta.numero_venta||'N/A'}</p>
</div>
<div class="historial-detail-item">
<p class="historial-detail-label">Fecha</p>
<p class="historial-detail-value">${this.formatDate(venta.created_at)}</p>
</div>
<div class="historial-detail-item">
<p class="historial-detail-label">Estado</p>
<p class="historial-detail-value"><span class="historial-badge historial-badge-${venta.estado}">${this.capitalize(venta.estado)}</span></p>
</div>
<div class="historial-detail-item">
<p class="historial-detail-label">M\u00e9todo de Pago</p>
<p class="historial-detail-value"><span class="historial-badge historial-badge-${venta.metodo_pago}">${this.capitalize(venta.metodo_pago)}</span></p>
</div>
</div>
</div>
<div class="historial-detail-section">
<h4 class="historial-detail-title">\ud83d\udc64 Cliente</h4>
<div class="historial-detail-grid">
<div class="historial-detail-item">
<p class="historial-detail-label">Nombre</p>
<p class="historial-detail-value">${venta.cliente_data?.name||'N/A'}</p>
</div>
<div class="historial-detail-item">
<p class="historial-detail-label">Email</p>
<p class="historial-detail-value">${venta.cliente_data?.email||'N/A'}</p>
</div>
<div class="historial-detail-item">
<p class="historial-detail-label">Tel\u00e9fono</p>
<p class="historial-detail-value">${venta.cliente_data?.telefono||'N/A'}</p>
</div>
<div class="historial-detail-item">
<p class="historial-detail-label">C\u00e9dula</p>
<p class="historial-detail-value">${venta.cliente_data?.cedula||'N/A'}</p>
</div>
</div>
</div>
<div class="historial-detail-section">
<h4 class="historial-detail-title">\ud83d\udce6 Productos</h4>
<table class="historial-productos-table">
<thead>
<tr>
<th>#</th>
<th>Producto</th>
<th>C\u00f3digo</th>
<th>Cantidad</th>
<th>Precio Unit.</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody>${productosHtml}</tbody>
</table>
</div>
<div class="historial-total-summary">
<div class="historial-total-row">
<span>Subtotal:</span>
<span>$${parseFloat(venta.subtotal||0).toFixed(2)}</span>
</div>
<div class="historial-total-row">
<span>Descuento:</span>
<span>-$${parseFloat(venta.descuento||0).toFixed(2)}</span>
</div>
<div class="historial-total-row">
<span>IVA (19%):</span>
<span>$${parseFloat(venta.iva||0).toFixed(2)}</span>
</div>
<div class="historial-total-row final">
<span>Total:</span>
<span>$${parseFloat(venta.total_final||0).toFixed(2)}</span>
</div>
</div>
${venta.notas?`
<div class="historial-detail-section">
<h4 class="historial-detail-title">\ud83d\udcdd Notas</h4>
<p style="background:var(--gray-50);padding:1rem;border-radius:8px;margin:0;">${venta.notas}</p>
</div>`:''}`;this.createModal('detalle-venta',`Detalle de Venta #${venta.numero_venta||venta._id}`,body,'primary',[{text:'Cerrar',class:'historial-btn-secondary',onclick:'historialVentasManager.closeModal("detalle-venta")'},{text:'Exportar PDF',class:'historial-btn-primary',onclick:`historialVentasManager.exportarVenta('${venta._id}')`}])}createModal(id,title,body,type='primary',buttons=[]){const backdrop=document.createElement('div');backdrop.className='historial-modal-backdrop';backdrop.id=`modal-backdrop-${id}`;const modal=document.createElement('div');modal.className='historial-modal';modal.innerHTML=`
<div class="historial-modal-content">
<div class="historial-modal-header">
<h3 class="historial-modal-title">${title}</h3>
<button class="historial-modal-close" onclick="historialVentasManager.closeModal('${id}')">&times;</button>
</div>
<div class="historial-modal-body">${body}</div>
<div class="historial-modal-footer">
${buttons.map(btn=>`<button class="${btn.class}" onclick="${btn.onclick}">${btn.text}</button>`).join('')}
</div>
</div>`;document.body.appendChild(backdrop);document.body.appendChild(modal);this.modals.set(id,{backdrop,modal});setTimeout(()=>{backdrop.classList.add('active');modal.classList.add('active')},10);return{backdrop,modal}}closeModal(id){const modal=this.modals.get(id);if(modal){modal.backdrop.classList.remove('active');modal.modal.classList.remove('active');setTimeout(()=>{modal.backdrop.remove();modal.modal.remove();this.modals.delete(id)},300)}}closeAllModals(){this.modals.forEach((modal,id)=>this.closeModal(id))}exportarVenta(ventaId){this.showLoading();window.location.href=`/vendedor/ventas/${ventaId}/exportar`;setTimeout(()=>this.hideLoading(),2000)}exportarTodas(){const form=document.querySelector('#historial-filter-form');if(form){const exportForm=form.cloneNode(true);exportForm.action='/vendedor/ventas/exportar';exportForm.style.display='none';document.body.appendChild(exportForm);this.showLoading();exportForm.submit();setTimeout(()=>{exportForm.remove();this.hideLoading()},2000)}else{window.location.href='/vendedor/ventas/exportar'}}showToast(message,type='success',duration=3000){const toastId=`toast-${Date.now()}`;const toast=document.createElement('div');toast.className=`historial-toast ${type}`;toast.id=toastId;const icons={success:'\u2714\ufe0f',error:'\u274c',warning:'\u26a0\ufe0f',info:'\u2139\ufe0f'};toast.innerHTML=`
<span style="font-size:1.5rem;flex-shrink:0;">${icons[type]||icons.info}</span>
<span>${message}</span>`;document.body.appendChild(toast);setTimeout(()=>toast.classList.add('active'),10);setTimeout(()=>{toast.classList.remove('active');setTimeout(()=>toast.remove(),300)},duration)}showLoading(){const overlay=document.createElement('div');overlay.className='historial-loading-overlay';overlay.id='historial-loading';overlay.innerHTML='<div class="historial-loading-spinner"></div>';document.body.appendChild(overlay);setTimeout(()=>overlay.classList.add('active'),10)}hideLoading(){const overlay=document.getElementById('historial-loading');if(overlay){overlay.classList.remove('active');setTimeout(()=>overlay.remove(),300)}}formatDate(dateStr){if(!dateStr)return'N/A';const date=new Date(dateStr);return date.toLocaleDateString('es-CO',{year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'})}capitalize(str){if(!str)return'';return str.charAt(0).toUpperCase()+str.slice(1)}}let historialVentasManager;document.addEventListener('DOMContentLoaded',()=>{historialVentasManager=new HistorialVentasManager();if(typeof successMessage!=='undefined'&&successMessage){historialVentasManager.showToast(successMessage,'success')}if(typeof errorMessage!=='undefined'&&errorMessage){historialVentasManager.showToast(errorMessage,'error')}});
