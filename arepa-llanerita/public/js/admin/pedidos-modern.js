/**
 * Módulo Pedidos - JavaScript Moderno Minificado
 * Versión: 3.0 - Optimizado para rendimiento <3s
 * Compatible con PWA
 */
class PedidosManager{constructor(){this.routes=window.pedidosRoutes||{};this.modals={};this.toasts=[];this.init()}init(){this.createModalContainers();this.setupEventListeners();this.animateTable();console.log('✓ PedidosManager initialized')}createModalContainers(){const backdrop=document.createElement('div');backdrop.className='pedido-modal-backdrop';backdrop.id='pedido-modal-backdrop';document.body.appendChild(backdrop);backdrop.addEventListener('click',(e)=>{if(e.target===backdrop){this.closeAllModals()}})}setupEventListeners(){document.addEventListener('keydown',(e)=>{if(e.key==='Escape'){this.closeAllModals()}});const deleteButtons=document.querySelectorAll('[data-action="delete-pedido"]');deleteButtons.forEach(btn=>{btn.addEventListener('click',(e)=>{e.preventDefault();const data=btn.dataset;this.confirmDelete(data)})});const statusButtons=document.querySelectorAll('[data-action="status-pedido"]');statusButtons.forEach(btn=>{btn.addEventListener('click',(e)=>{e.preventDefault();const data=btn.dataset;this.showStatusSelector(data)})})}animateTable(){const rows=document.querySelectorAll('.pedido-table tbody tr');rows.forEach((row,index)=>{row.classList.add('fade-in-up');row.style.animationDelay=`${index*.05}s`})}createModal(config){const{id,title,body,type='primary',buttons=[],closeOnBackdrop=true}=config;let existingModal=document.getElementById(`pedido-modal-${id}`);if(existingModal){existingModal.remove()}const modal=document.createElement('div');modal.className='pedido-modal';modal.id=`pedido-modal-${id}`;const iconMap={success:'<i class="bi bi-check-circle-fill"></i>',warning:'<i class="bi bi-exclamation-triangle-fill"></i>',danger:'<i class="bi bi-x-circle-fill"></i>',info:'<i class="bi bi-info-circle-fill"></i>',primary:'<i class="bi bi-basket3-fill"></i>'};const icon=iconMap[type]||iconMap.primary;let buttonsHtml='';if(buttons.length>0){buttons.forEach(btn=>{const btnClass=btn.className||'pedido-modal-btn-secondary';const btnText=btn.text||'Botón';const btnOnClick=btn.onClick?`onclick="${btn.onClick}"`:'';buttonsHtml+=`<button class="pedido-modal-btn ${btnClass}" ${btnOnClick}>${btn.icon||''} ${btnText}</button>`})}modal.innerHTML=`
        <div class="pedido-modal-content">
            <div class="pedido-modal-header">
                <h3 class="pedido-modal-title ${type}">
                    ${icon}
                    <span>${title}</span>
                </h3>
                <button class="pedido-modal-close" onclick="window.pedidosManager.closeModal('${id}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="pedido-modal-body">
                ${body}
            </div>
            ${buttons.length>0?`<div class="pedido-modal-footer">${buttonsHtml}</div>`:''}
        </div>
    `;document.body.appendChild(modal);this.modals[id]=modal;return modal}showModal(id){const backdrop=document.getElementById('pedido-modal-backdrop');const modal=this.modals[id]||document.getElementById(`pedido-modal-${id}`);if(!modal)return;backdrop.classList.add('active');modal.classList.add('active');document.body.style.overflow='hidden'}closeModal(id){const modal=this.modals[id]||document.getElementById(`pedido-modal-${id}`);if(!modal)return;modal.classList.remove('active');setTimeout(()=>{const backdrop=document.getElementById('pedido-modal-backdrop');const activeModals=document.querySelectorAll('.pedido-modal.active');if(activeModals.length===0){backdrop.classList.remove('active');document.body.style.overflow=''}},300)}closeAllModals(){const activeModals=document.querySelectorAll('.pedido-modal.active');activeModals.forEach(modal=>{const modalId=modal.id.replace('pedido-modal-','');this.closeModal(modalId)});document.body.style.overflow=''}confirmDelete(data){const{pedidoId,numeroPedido,clienteNombre,totalFinal,estado}=data;const body=`
            <div class="pedido-info-grid">
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Número de Pedido</div>
                    <div class="pedido-info-value">${numeroPedido}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Cliente</div>
                    <div class="pedido-info-value">${clienteNombre}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Total</div>
                    <div class="pedido-info-value">${totalFinal}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Estado</div>
                    <div class="pedido-info-value">${estado}</div>
                </div>
            </div>
            <p style="margin-top:1rem;"><strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. El stock de los productos será devuelto automáticamente.</p>
        `;this.createModal({id:'delete-confirm',title:'¿Confirmar eliminación de pedido?',body:body,type:'danger',buttons:[{text:'Cancelar',className:'pedido-modal-btn-secondary',onClick:`window.pedidosManager.closeModal('delete-confirm')`},{text:'Eliminar Pedido',className:'pedido-modal-btn-danger',icon:'<i class="bi bi-trash"></i>',onClick:`window.pedidosManager.executeDelete('${pedidoId}')`}]});this.showModal('delete-confirm')}executeDelete(pedidoId){this.showLoading('Eliminando pedido...');const form=document.getElementById(`delete-form-${pedidoId}`);if(form){form.submit()}else{console.error('Form not found:',`delete-form-${pedidoId}`);this.hideLoading();this.showToast('Error: Formulario no encontrado','error')}}showStatusSelector(data){const{pedidoId,numeroPedido,clienteNombre,estadoActual,estados}=data;let estadosOptions='';Object.entries(JSON.parse(estados)).forEach(([valor,nombre])=>{const selected=valor===estadoActual?'selected':'';estadosOptions+=`<option value="${valor}" ${selected}>${nombre}</option>`});const body=`
            <div style="margin-bottom:1.5rem;">
                <p><strong>Pedido:</strong> ${numeroPedido}</p>
                <p><strong>Cliente:</strong> ${clienteNombre}</p>
                <p><strong>Estado actual:</strong> <span class="pedido-badge pedido-badge-${estadoActual.toLowerCase()}">${estadoActual}</span></p>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nuevo Estado:</label>
                <select class="form-select" id="nuevo-estado-${pedidoId}" style="padding:.75rem;border-radius:10px;border:2px solid var(--gray-200);font-size:1rem;">
                    ${estadosOptions}
                </select>
            </div>
            <div class="alert alert-info" style="border-radius:10px;background:rgba(59,130,246,.1);border:1px solid var(--info);padding:1rem;">
                <i class="bi bi-info-circle-fill"></i>
                <small>Si cambias el estado a "Cancelado", el stock de los productos se devolverá automáticamente.</small>
            </div>
        `;this.createModal({id:'status-change',title:'Cambiar Estado del Pedido',body:body,type:'info',buttons:[{text:'Cancelar',className:'pedido-modal-btn-secondary',onClick:`window.pedidosManager.closeModal('status-change')`},{text:'Actualizar Estado',className:'pedido-modal-btn-primary',icon:'<i class="bi bi-arrow-repeat"></i>',onClick:`window.pedidosManager.executeStatusChange('${pedidoId}')`}]});this.showModal('status-change')}executeStatusChange(pedidoId){const selectElement=document.getElementById(`nuevo-estado-${pedidoId}`);const nuevoEstado=selectElement?selectElement.value:null;if(!nuevoEstado){this.showToast('Por favor selecciona un estado','warning');return}this.showLoading('Actualizando estado del pedido...');const hiddenInput=document.getElementById(`estado-${pedidoId}`);if(hiddenInput){hiddenInput.value=nuevoEstado}const form=document.getElementById(`status-form-${pedidoId}`);if(form){form.submit()}else{console.error('Form not found:',`status-form-${pedidoId}`);this.hideLoading();this.showToast('Error: Formulario no encontrado','error')}}showPedidoDetails(pedidoId){this.showLoading('Cargando detalles...');const url=this.routes.details.replace(':id',pedidoId);fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}}).then(response=>{if(!response.ok)throw new Error('Error al cargar datos');return response.json()}).then(data=>{this.hideLoading();this.renderPedidoDetails(data)}).catch(error=>{this.hideLoading();console.error('Error:',error);this.showToast('Error al cargar los detalles del pedido','error')})}renderPedidoDetails(pedido){const estadoBadgeClass=`pedido-badge-${pedido.estado.toLowerCase()}`;let productosHtml='';if(pedido.detalles&&pedido.detalles.length>0){pedido.detalles.forEach(detalle=>{productosHtml+=`
                    <div class="pedido-product-item">
                        <div class="pedido-product-img" style="background:var(--gray-200);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-box-seam fs-4 text-muted"></i>
                        </div>
                        <div class="pedido-product-info">
                            <div class="pedido-product-name">${detalle.producto_nombre}</div>
                            <div class="pedido-product-details">
                                Cantidad: ${detalle.cantidad} × $${Number(detalle.precio_unitario).toFixed(2)}
                            </div>
                        </div>
                        <div class="pedido-product-price">
                            $${Number(detalle.total).toFixed(2)}
                        </div>
                    </div>
                `})}else{productosHtml='<p class="text-muted">No hay productos en este pedido</p>'}const body=`
            <div class="pedido-info-grid">
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Número de Pedido</div>
                    <div class="pedido-info-value">${pedido.numero_pedido}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Cliente</div>
                    <div class="pedido-info-value">${pedido.cliente.name}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Email Cliente</div>
                    <div class="pedido-info-value">${pedido.cliente.email}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Teléfono</div>
                    <div class="pedido-info-value">${pedido.cliente.telefono}</div>
                </div>
                ${pedido.vendedor?`
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Vendedor</div>
                    <div class="pedido-info-value">${pedido.vendedor.name}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Email Vendedor</div>
                    <div class="pedido-info-value">${pedido.vendedor.email}</div>
                </div>
                `:''}
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Estado</div>
                    <div class="pedido-info-value">
                        <span class="pedido-badge ${estadoBadgeClass}">${pedido.estado_texto}</span>
                    </div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Fecha</div>
                    <div class="pedido-info-value">${pedido.fecha_pedido}</div>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <h5 style="color:var(--wine);margin-bottom:1rem;">
                    <i class="bi bi-box-seam"></i> Productos del Pedido
                </h5>
                ${productosHtml}
            </div>
            <div class="pedido-info-grid" style="margin-top:1.5rem;">
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Subtotal</div>
                    <div class="pedido-info-value">$${Number(pedido.total_productos).toFixed(2)}</div>
                </div>
                <div class="pedido-info-item">
                    <div class="pedido-info-label">Descuento</div>
                    <div class="pedido-info-value text-success">-$${Number(pedido.descuento).toFixed(2)}</div>
                </div>
                <div class="pedido-info-item" style="grid-column:1/-1;">
                    <div class="pedido-info-label">Total Final</div>
                    <div class="pedido-info-value" style="font-size:1.5rem;color:var(--wine);">
                        $${Number(pedido.total_final).toFixed(2)}
                    </div>
                </div>
            </div>
            ${pedido.observaciones?`
            <div style="margin-top:1.5rem;padding:1rem;background:var(--gray-50);border-radius:10px;border:1px solid var(--gray-200);">
                <div class="pedido-info-label">Observaciones</div>
                <p style="margin:.5rem 0 0 0;color:var(--gray-700);">${pedido.observaciones}</p>
            </div>
            `:''}
        `;this.createModal({id:'pedido-details',title:'Detalles del Pedido',body:body,type:'primary',buttons:[{text:'Cerrar',className:'pedido-modal-btn-secondary',onClick:`window.pedidosManager.closeModal('pedido-details')`}]});this.showModal('pedido-details')}showToast(message,type='success'){const toastId=`toast-${Date.now()}`;const toast=document.createElement('div');toast.className=`pedido-toast ${type}`;toast.id=toastId;const iconMap={success:'<i class="bi bi-check-circle-fill"></i>',error:'<i class="bi bi-x-circle-fill"></i>',warning:'<i class="bi bi-exclamation-triangle-fill"></i>',info:'<i class="bi bi-info-circle-fill"></i>'};toast.innerHTML=`
            <div class="pedido-toast-icon">
                ${iconMap[type]||iconMap.info}
            </div>
            <div class="pedido-toast-message">${message}</div>
            <button class="pedido-toast-close" onclick="window.pedidosManager.closeToast('${toastId}')">
                <i class="bi bi-x"></i>
            </button>
        `;document.body.appendChild(toast);setTimeout(()=>toast.classList.add('active'),10);this.toasts.push(toastId);setTimeout(()=>this.closeToast(toastId),5000)}closeToast(toastId){const toast=document.getElementById(toastId);if(!toast)return;toast.classList.remove('active');setTimeout(()=>{toast.remove();this.toasts=this.toasts.filter(id=>id!==toastId)},300)}showLoading(message='Cargando...'){let overlay=document.getElementById('pedido-loading-overlay');if(!overlay){overlay=document.createElement('div');overlay.className='pedido-loading-overlay';overlay.id='pedido-loading-overlay';overlay.innerHTML=`
                <div class="pedido-loading-spinner"></div>
                <div class="pedido-loading-text">${message}</div>
            `;document.body.appendChild(overlay)}else{const text=overlay.querySelector('.pedido-loading-text');if(text)text.textContent=message}setTimeout(()=>overlay.classList.add('active'),10);document.body.style.overflow='hidden'}hideLoading(){const overlay=document.getElementById('pedido-loading-overlay');if(!overlay)return;overlay.classList.remove('active');document.body.style.overflow='';setTimeout(()=>overlay.remove(),300)}}document.addEventListener('DOMContentLoaded',()=>{window.pedidosManager=new PedidosManager()});window.confirmDeletePedido=(pedidoId,numeroPedido,clienteNombre,totalFinal,estado)=>{if(window.pedidosManager){window.pedidosManager.confirmDelete({pedidoId,numeroPedido,clienteNombre,totalFinal,estado})}};window.showStatusSelector=(pedidoId,numeroPedido,clienteNombre,estadoActual,estados)=>{if(window.pedidosManager){window.pedidosManager.showStatusSelector({pedidoId,numeroPedido,clienteNombre,estadoActual,estados:JSON.stringify(estados)})}};window.viewPedidoDetails=(pedidoId)=>{if(window.pedidosManager){window.pedidosManager.showPedidoDetails(pedidoId)}};
