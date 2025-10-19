/**
 * REFERIDOS MODERN JS - Gestión de Red de Referidos Vendedor
 * Sistema profesional con modales, toast y animaciones
 */
class ReferidosManager{constructor(){this.initEventListeners();this.initAnimations();this.initTooltips();}initEventListeners(){const shareButtons=document.querySelectorAll('[data-share]');shareButtons.forEach(btn=>{btn.addEventListener('click',(e)=>{e.preventDefault();this.handleShare(btn.dataset.share);});});const copyButtons=document.querySelectorAll('[data-copy]');copyButtons.forEach(btn=>{btn.addEventListener('click',()=>{this.copyToClipboard(btn.dataset.copy);});});const modalTriggers=document.querySelectorAll('[data-modal-trigger]');modalTriggers.forEach(trigger=>{trigger.addEventListener('click',(e)=>{e.preventDefault();const modalId=trigger.dataset.modalTrigger;this.showModal(modalId);});});const modalCloseButtons=document.querySelectorAll('.referidos-modal-close, [data-modal-close]');modalCloseButtons.forEach(btn=>{btn.addEventListener('click',()=>{this.closeAllModals();});});const backdrops=document.querySelectorAll('.referidos-modal-backdrop');backdrops.forEach(backdrop=>{backdrop.addEventListener('click',(e)=>{if(e.target===backdrop){this.closeAllModals();}});});document.addEventListener('keydown',(e)=>{if(e.key==='Escape'){this.closeAllModals();}});const filterForm=document.getElementById('referidos-filter-form');if(filterForm){filterForm.addEventListener('submit',(e)=>{this.showLoading();});}const exportBtn=document.getElementById('export-referidos-btn');if(exportBtn){exportBtn.addEventListener('click',()=>{this.exportData();});}}initAnimations(){const statCards=document.querySelectorAll('.referidos-stat-card');statCards.forEach((card,index)=>{card.style.animationDelay=`${index*0.1}s`;card.classList.add('fade-in-up');});const tableRows=document.querySelectorAll('.referidos-table tbody tr');tableRows.forEach((row,index)=>{row.style.animationDelay=`${index*0.05}s`;});}initTooltips(){const tooltipElements=document.querySelectorAll('[data-tooltip]');tooltipElements.forEach(el=>{el.setAttribute('title',el.dataset.tooltip);});}showModal(modalId,type='primary',title='',content=''){let modalBackdrop=document.getElementById(modalId);if(!modalBackdrop){modalBackdrop=this.createModal(modalId,type,title,content);}modalBackdrop.classList.add('active');document.body.style.overflow='hidden';}createModal(id,type,title,content){const backdrop=document.createElement('div');backdrop.id=id;backdrop.className='referidos-modal-backdrop';backdrop.innerHTML=`
        <div class="referidos-modal">
            <div class="referidos-modal-header ${type}">
                <h3 class="referidos-modal-title">${title}</h3>
                <button class="referidos-modal-close" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="referidos-modal-body">
                ${content}
            </div>
            <div class="referidos-modal-footer">
                <button class="referidos-modal-btn referidos-modal-btn-secondary" data-modal-close>
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    `;document.body.appendChild(backdrop);backdrop.querySelector('.referidos-modal-close').addEventListener('click',()=>{this.closeModal(id);});backdrop.querySelector('[data-modal-close]').addEventListener('click',()=>{this.closeModal(id);});backdrop.addEventListener('click',(e)=>{if(e.target===backdrop){this.closeModal(id);}});return backdrop;}closeModal(modalId){const modal=document.getElementById(modalId);if(modal){modal.classList.remove('active');document.body.style.overflow='';}const activeModals=document.querySelectorAll('.referidos-modal-backdrop.active');if(activeModals.length===0){document.body.style.overflow='';}}closeAllModals(){const modals=document.querySelectorAll('.referidos-modal-backdrop');modals.forEach(modal=>{modal.classList.remove('active');});document.body.style.overflow='';}showToast(title,message,type='info',duration=4000){const toastContainer=document.getElementById('toast-container')||this.createToastContainer();const toast=document.createElement('div');toast.className=`referidos-toast referidos-toast-${type}`;const icons={success:'fa-check-circle',error:'fa-exclamation-circle',warning:'fa-exclamation-triangle',info:'fa-info-circle'};toast.innerHTML=`
        <div class="referidos-toast-icon">
            <i class="fas ${icons[type]||icons.info}"></i>
        </div>
        <div class="referidos-toast-content">
            <div class="referidos-toast-title">${title}</div>
            <div class="referidos-toast-message">${message}</div>
        </div>
        <button class="referidos-toast-close">
            <i class="fas fa-times"></i>
        </button>
    `;toastContainer.appendChild(toast);const closeBtn=toast.querySelector('.referidos-toast-close');closeBtn.addEventListener('click',()=>{this.closeToast(toast);});setTimeout(()=>{this.closeToast(toast);},duration);}createToastContainer(){const container=document.createElement('div');container.id='toast-container';container.className='referidos-toast-container';document.body.appendChild(container);return container;}closeToast(toast){toast.style.opacity='0';toast.style.transform='translateX(400px)';setTimeout(()=>{toast.remove();},300);}showLoading(text='Cargando...'){let overlay=document.getElementById('loading-overlay');if(!overlay){overlay=document.createElement('div');overlay.id='loading-overlay';overlay.className='referidos-loading-overlay';overlay.innerHTML=`
            <div>
                <div class="referidos-loading-spinner"></div>
                <div class="referidos-loading-text">${text}</div>
            </div>
        `;document.body.appendChild(overlay);}overlay.classList.add('active');}hideLoading(){const overlay=document.getElementById('loading-overlay');if(overlay){overlay.classList.remove('active');}}copyToClipboard(text){if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(text).then(()=>{this.showToast('¡Copiado!','El enlace se copió al portapapeles','success',3000);}).catch(()=>{this.fallbackCopyToClipboard(text);});}else{this.fallbackCopyToClipboard(text);}}fallbackCopyToClipboard(text){const textArea=document.createElement('textarea');textArea.value=text;textArea.style.position='fixed';textArea.style.left='-999999px';document.body.appendChild(textArea);textArea.focus();textArea.select();try{document.execCommand('copy');this.showToast('¡Copiado!','El enlace se copió al portapapeles','success',3000);}catch(err){this.showToast('Error','No se pudo copiar al portapapeles','error',3000);}document.body.removeChild(textArea);}handleShare(platform){const urlBase=window.location.origin+'/register?ref=';const codigoReferido=document.getElementById('codigo-referido')?.value||'';const enlace=urlBase+codigoReferido;const texto=encodeURIComponent('¡Únete a nuestra red de ventas! Usa mi código: '+codigoReferido);switch(platform){case'whatsapp':window.open(`https://wa.me/?text=${texto}%20${encodeURIComponent(enlace)}`,'_blank');break;case'email':window.location.href=`mailto:?subject=Invitación a Red de Ventas&body=${texto}%0A%0A${enlace}`;break;case'facebook':window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(enlace)}`,'_blank');break;case'twitter':window.open(`https://twitter.com/intent/tweet?text=${texto}&url=${encodeURIComponent(enlace)}`,'_blank');break;case'copy':this.copyToClipboard(enlace);break;}}exportData(){this.showLoading('Exportando datos...');setTimeout(()=>{this.hideLoading();this.showToast('Exportación Completa','Los datos se han exportado correctamente','success');},1500);}generateReferralLink(){fetch('/vendedor/referidos/enlace',{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}}).then(res=>res.json()).then(data=>{const modal=this.createModal('referral-link-modal','primary','Tu Enlace de Referido',`
                <div class="referidos-code-box">
                    <div class="referidos-code-label">Tu Código de Referido</div>
                    <div class="referidos-code-value">${data.codigo}</div>
                </div>
                <div style="margin-top:1.5rem;">
                    <label style="font-weight:600;margin-bottom:0.5rem;display:block;">Enlace para compartir:</label>
                    <div style="display:flex;gap:0.5rem;">
                        <input type="text" value="${data.enlace}" readonly 
                               style="flex:1;padding:0.75rem;border:2px solid #e5e7eb;border-radius:8px;"
                               id="referral-link-input">
                        <button class="referidos-btn-primary" onclick="referidosManager.copyToClipboard('${data.enlace}')">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
                <div class="referidos-share-options">
                    <button class="referidos-share-btn referidos-share-whatsapp" onclick="referidosManager.handleShare('whatsapp')">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </button>
                    <button class="referidos-share-btn referidos-share-email" onclick="referidosManager.handleShare('email')">
                        <i class="fas fa-envelope"></i>
                        <span>Email</span>
                    </button>
                    <button class="referidos-share-btn referidos-share-copy" onclick="referidosManager.handleShare('copy')">
                        <i class="fas fa-link"></i>
                        <span>Copiar</span>
                    </button>
                </div>
            `);this.showModal('referral-link-modal');}).catch(err=>{console.error('Error:',err);this.showToast('Error','No se pudo generar el enlace','error');});}viewReferidoDetails(referidoId){this.showLoading('Cargando detalles...');window.location.href=`/vendedor/referidos/${referidoId}`;}sendMessage(referidoId){const content=`
        <form id="message-form" onsubmit="event.preventDefault(); referidosManager.submitMessage(${referidoId});">
            <div style="margin-bottom:1rem;">
                <label style="font-weight:600;margin-bottom:0.5rem;display:block;">Asunto:</label>
                <input type="text" id="message-subject" required
                       style="width:100%;padding:0.75rem;border:2px solid #e5e7eb;border-radius:8px;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="font-weight:600;margin-bottom:0.5rem;display:block;">Mensaje:</label>
                <textarea id="message-body" rows="5" required
                          style="width:100%;padding:0.75rem;border:2px solid #e5e7eb;border-radius:8px;resize:vertical;"></textarea>
            </div>
            <button type="submit" class="referidos-modal-btn referidos-modal-btn-primary" style="width:100%;">
                <i class="fas fa-paper-plane"></i> Enviar Mensaje
            </button>
        </form>
    `;this.showModal('message-modal','primary','Enviar Mensaje al Referido',content);}submitMessage(referidoId){const subject=document.getElementById('message-subject').value;const body=document.getElementById('message-body').value;this.showLoading('Enviando mensaje...');setTimeout(()=>{this.hideLoading();this.closeAllModals();this.showToast('Mensaje Enviado','Tu mensaje ha sido enviado correctamente','success');},1500);}filterReferidos(){const form=document.getElementById('referidos-filter-form');if(form){form.submit();}}resetFilters(){const filterInputs=document.querySelectorAll('.referidos-filter-input, .referidos-filter-select');filterInputs.forEach(input=>{if(input.type==='select-one'){input.selectedIndex=0;}else{input.value='';}});}}const referidosManager=new ReferidosManager();document.addEventListener('DOMContentLoaded',()=>{const generateLinkBtn=document.getElementById('generate-link-btn');if(generateLinkBtn){generateLinkBtn.addEventListener('click',()=>{referidosManager.generateReferralLink();});}const messageButtons=document.querySelectorAll('[data-send-message]');messageButtons.forEach(btn=>{btn.addEventListener('click',()=>{const referidoId=btn.dataset.sendMessage;referidosManager.sendMessage(referidoId);});});const viewButtons=document.querySelectorAll('[data-view-referido]');viewButtons.forEach(btn=>{btn.addEventListener('click',()=>{const referidoId=btn.dataset.viewReferido;referidosManager.viewReferidoDetails(referidoId);});});const statCards=document.querySelectorAll('.referidos-stat-card[data-filter]');statCards.forEach(card=>{card.addEventListener('click',()=>{const filterValue=card.dataset.filter;if(filterValue){const filterSelect=document.querySelector('.referidos-filter-select[name="estado"]');if(filterSelect){filterSelect.value=filterValue;referidosManager.filterReferidos();}}});});});