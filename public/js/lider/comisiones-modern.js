class ComisionesManager{constructor(){this.init()}init(){this.setupTableAnimations();this.setupEventListeners();this.setupTooltips();this.animateStats();this.setupModalHandlers()}setupTableAnimations(){const rows=document.querySelectorAll('.comisiones-table tbody tr');rows.forEach((row,index)=>{row.style.opacity='0';row.style.animation=`fadeInUp 0.6s ease-out ${index*0.05}s forwards`})}setupEventListeners(){const filterForm=document.getElementById('comisionesFilterForm');if(filterForm){filterForm.addEventListener('submit',e=>{this.showLoading()})}}setupTooltips(){const tooltipElements=document.querySelectorAll('[data-toggle="tooltip"]');tooltipElements.forEach(el=>{if(typeof bootstrap!=='undefined'&&bootstrap.Tooltip){new bootstrap.Tooltip(el)}})}animateStats(){const statCards=document.querySelectorAll('.comisiones-stat-card');statCards.forEach((card,index)=>{card.style.opacity='0';card.style.animation=`scaleIn 0.6s ease-out ${index*0.1}s forwards`})}setupModalHandlers(){document.addEventListener('keydown',e=>{if(e.key==='Escape'){this.closeAllModals()}})}showModal(modalId,type='primary'){const backdrop=this.createModalBackdrop();const modal=document.getElementById(modalId);if(!modal)return;backdrop.classList.add('show');modal.classList.add('show');if(type!=='primary'){const header=modal.querySelector('.comisiones-modal-header');if(header){header.classList.add(type)}}backdrop.addEventListener('click',e=>{if(e.target===backdrop){this.closeModal(modalId)}});const closeBtn=modal.querySelector('.comisiones-modal-close');if(closeBtn){closeBtn.addEventListener('click',()=>this.closeModal(modalId))}}closeModal(modalId){const modal=document.getElementById(modalId);const backdrop=document.querySelector('.comisiones-modal-backdrop');if(modal){modal.classList.remove('show');setTimeout(()=>{modal.style.display='none'},300)}if(backdrop){backdrop.classList.remove('show');setTimeout(()=>{backdrop.remove()},300)}}closeAllModals(){const modals=document.querySelectorAll('.comisiones-modal.show');modals.forEach(modal=>{this.closeModal(modal.id)})}createModalBackdrop(){const existing=document.querySelector('.comisiones-modal-backdrop');if(existing)existing.remove();const backdrop=document.createElement('div');backdrop.className='comisiones-modal-backdrop';document.body.appendChild(backdrop);return backdrop}showToast(message,type='success',title=''){const toast=document.createElement('div');toast.className=`comisiones-toast ${type}`;const iconMap={success:'bi-check-circle-fill',error:'bi-x-circle-fill',danger:'bi-x-circle-fill',warning:'bi-exclamation-triangle-fill',info:'bi-info-circle-fill'};const titleMap={success:'Éxito',error:'Error',danger:'Error',warning:'Advertencia',info:'Información'};const icon=iconMap[type]||iconMap.success;const toastTitle=title||titleMap[type]||titleMap.success;toast.innerHTML=`
        <div class="comisiones-toast-icon ${type}">
            <i class="bi ${icon}"></i>
        </div>
        <div class="comisiones-toast-content">
            <div class="comisiones-toast-title">${toastTitle}</div>
            <div class="comisiones-toast-message">${message}</div>
        </div>
        <button class="comisiones-toast-close" onclick="this.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    `;document.body.appendChild(toast);setTimeout(()=>toast.classList.add('show'),10);setTimeout(()=>{toast.classList.remove('show');setTimeout(()=>toast.remove(),400)},5000);return toast}showLoading(text='Cargando...'){const existing=document.querySelector('.comisiones-loading');if(existing)return;const loading=document.createElement('div');loading.className='comisiones-loading';loading.innerHTML=`
        <div class="comisiones-spinner"></div>
        <div class="comisiones-loading-text">${text}</div>
    `;document.body.appendChild(loading);setTimeout(()=>loading.classList.add('show'),10);return loading}hideLoading(){const loading=document.querySelector('.comisiones-loading');if(loading){loading.classList.remove('show');setTimeout(()=>loading.remove(),300)}}formatCurrency(amount){return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',minimumFractionDigits:0,maximumFractionDigits:0}).format(amount)}formatDate(date){return new Intl.DateTimeFormat('es-CO',{year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'}).format(new Date(date))}exportData(data,filename='comisiones'){const csv=this.convertToCSV(data);const blob=new Blob([csv],{type:'text/csv;charset=utf-8'});const link=document.createElement('a');link.href=URL.createObjectURL(blob);link.download=`${filename}_${new Date().getTime()}.csv`;link.click()}convertToCSV(data){if(!data||data.length===0)return'';const headers=Object.keys(data[0]);const csvRows=[headers.join(',')];data.forEach(row=>{const values=headers.map(header=>{const value=row[header];return typeof value==='string'?`"${value.replace(/"/g,'""')}"`:`"${value}"`});csvRows.push(values.join(','))});return csvRows.join('\n')}debounce(func,wait){let timeout;return function executedFunction(...args){const later=()=>{clearTimeout(timeout);func(...args)};clearTimeout(timeout);timeout=setTimeout(later,wait)}}showSolicitudModal(){const modalHtml=`
        <div class="comisiones-modal-backdrop show" id="solicitudBackdrop">
            <div class="comisiones-modal show" id="solicitudModal">
                <div class="comisiones-modal-header success">
                    <h3 class="comisiones-modal-title">
                        <i class="bi bi-cash-stack"></i>
                        Solicitar Pago de Comisiones
                    </h3>
                    <button class="comisiones-modal-close" onclick="comisionesManager.closeSolicitudModal()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="comisiones-modal-body">
                    <form id="solicitudForm" action="${window.location.origin}/lider/comisiones/solicitar" method="POST">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content||''}">

                        <div class="comisiones-alert info">
                            <i class="bi bi-info-circle-fill comisiones-alert-icon"></i>
                            <div class="comisiones-alert-content">
                                <div class="comisiones-alert-title">Información</div>
                                <div class="comisiones-alert-message">El monto mínimo para solicitar es de $50,000 COP. El procesamiento toma de 24 a 48 horas hábiles.</div>
                            </div>
                        </div>

                        <div class="comisiones-form-group">
                            <label class="comisiones-form-label">Monto a Solicitar</label>
                            <input type="number" name="monto" class="comisiones-form-input" min="50000" required placeholder="Ej: 100000">
                            <span class="comisiones-form-help">Mínimo: $50,000 COP</span>
                        </div>

                        <div class="comisiones-form-group">
                            <label class="comisiones-form-label">Método de Pago</label>
                            <select name="metodo_pago" class="comisiones-form-control" required>
                                <option value="">Seleccione un método</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="nequi">Nequi</option>
                                <option value="daviplata">Daviplata</option>
                                <option value="efectivo">Efectivo</option>
                            </select>
                        </div>

                        <div class="comisiones-form-group">
                            <label class="comisiones-form-label">Datos de Pago</label>
                            <input type="text" name="datos_pago" class="comisiones-form-input" required placeholder="Número de cuenta, teléfono, etc.">
                            <span class="comisiones-form-help">Ingrese el número de cuenta bancaria, teléfono Nequi/Daviplata, etc.</span>
                        </div>

                        <div class="comisiones-form-group">
                            <label class="comisiones-form-label">Observaciones (Opcional)</label>
                            <textarea name="observaciones" class="comisiones-form-input comisiones-form-textarea" placeholder="Comentarios adicionales..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="comisiones-modal-footer">
                    <button type="button" class="comisiones-btn comisiones-btn-secondary" onclick="comisionesManager.closeSolicitudModal()">
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </button>
                    <button type="submit" form="solicitudForm" class="comisiones-btn comisiones-btn-primary">
                        <i class="bi bi-send-fill"></i>
                        Enviar Solicitud
                    </button>
                </div>
            </div>
        </div>
    `;const container=document.createElement('div');container.innerHTML=modalHtml;document.body.appendChild(container.firstElementChild)}closeSolicitudModal(){const backdrop=document.getElementById('solicitudBackdrop');if(backdrop){backdrop.classList.remove('show');setTimeout(()=>backdrop.remove(),300)}}showDetalleComision(comisionId){this.showLoading('Cargando detalles...');fetch(`/lider/comisiones/${comisionId}`).then(response=>response.json()).then(data=>{this.hideLoading();this.renderDetalleModal(data)}).catch(error=>{this.hideLoading();this.showToast('Error al cargar los detalles de la comisión','error')})}renderDetalleModal(data){const modalHtml=`
        <div class="comisiones-modal-backdrop show" id="detalleBackdrop">
            <div class="comisiones-modal show large" id="detalleModal">
                <div class="comisiones-modal-header info">
                    <h3 class="comisiones-modal-title">
                        <i class="bi bi-receipt"></i>
                        Detalle de Comisión
                    </h3>
                    <button class="comisiones-modal-close" onclick="comisionesManager.closeDetalleModal()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="comisiones-modal-body">
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Tipo de Comisión</span>
                        <span class="comisiones-detail-value">
                            <span class="comisiones-badge comisiones-badge-${data.tipo}">${data.tipo_formatted}</span>
                        </span>
                    </div>
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Monto</span>
                        <span class="comisiones-detail-value highlight">${this.formatCurrency(data.monto)}</span>
                    </div>
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Porcentaje</span>
                        <span class="comisiones-detail-value">${data.porcentaje}%</span>
                    </div>
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Estado</span>
                        <span class="comisiones-detail-value">
                            <span class="comisiones-badge comisiones-badge-${data.estado}">${data.estado_formatted}</span>
                        </span>
                    </div>
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Fecha de Generación</span>
                        <span class="comisiones-detail-value">${this.formatDate(data.created_at)}</span>
                    </div>
                    ${data.fecha_pago?`
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Fecha de Pago</span>
                        <span class="comisiones-detail-value">${this.formatDate(data.fecha_pago)}</span>
                    </div>
                    `:''}
                    ${data.referido?`
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Referido</span>
                        <span class="comisiones-detail-value">${data.referido.name}</span>
                    </div>
                    `:''}
                    ${data.pedido?`
                    <div class="comisiones-detail-row">
                        <span class="comisiones-detail-label">Pedido</span>
                        <span class="comisiones-detail-value">#${String(data.pedido.id).padStart(6,'0')}</span>
                    </div>
                    `:''}
                </div>
                <div class="comisiones-modal-footer">
                    <button type="button" class="comisiones-btn comisiones-btn-primary" onclick="comisionesManager.closeDetalleModal()">
                        <i class="bi bi-check-circle"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    `;const container=document.createElement('div');container.innerHTML=modalHtml;document.body.appendChild(container.firstElementChild)}closeDetalleModal(){const backdrop=document.getElementById('detalleBackdrop');if(backdrop){backdrop.classList.remove('show');setTimeout(()=>backdrop.remove(),300)}}}let comisionesManager;document.addEventListener('DOMContentLoaded',()=>{comisionesManager=new ComisionesManager();const btnSolicitar=document.getElementById('btnSolicitarPago');if(btnSolicitar){btnSolicitar.addEventListener('click',e=>{e.preventDefault();comisionesManager.showSolicitudModal()})}const tablaBtns=document.querySelectorAll('.btn-ver-detalle');tablaBtns.forEach(btn=>{btn.addEventListener('click',e=>{e.preventDefault();const comisionId=btn.dataset.comisionId;if(comisionId){comisionesManager.showDetalleComision(comisionId)}})})});if(typeof window!=='undefined'){window.comisionesManager=comisionesManager}
