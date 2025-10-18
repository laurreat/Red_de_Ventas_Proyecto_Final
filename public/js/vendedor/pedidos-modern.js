/**Pedidos Manager-JS Profesional v2.1 - Fixed*/
class PedidosManager{
    constructor(){
        this.modals=new Map();
        this.toasts=[];
        this.optionsMenus=new Map();
        this.init()
    }
    
    init(){
        this.initAnimations();
        this.initModals();
        this.initEventListeners();
        this.initTableAnimations();
        this.initOptionsMenus()
    }
    
    initAnimations(){
        const elements=document.querySelectorAll('.fade-in-up, .scale-in, .animate-delay-1, .animate-delay-2, .animate-delay-3');
        const observer=new IntersectionObserver((entries)=>{
            entries.forEach(entry=>{
                if(entry.isIntersecting){
                    entry.target.style.opacity='1'
                }
            })
        },{threshold:.1});
        elements.forEach(el=>observer.observe(el))
    }
    
    initModals(){
        const modalBackdrops=document.querySelectorAll('.pedidos-modal-backdrop');
        modalBackdrops.forEach(backdrop=>{
            backdrop.addEventListener('click',()=>this.closeAllModals())
        });
        const modalCloses=document.querySelectorAll('.pedidos-modal-close');
        modalCloses.forEach(btn=>{
            btn.addEventListener('click',()=>this.closeAllModals())
        });
        document.addEventListener('keydown',(e)=>{
            if(e.key==='Escape'){
                this.closeAllModals();
                this.closeAllOptionsMenus()
            }
        })
    }
    
    initEventListeners(){
        const filterInputs=document.querySelectorAll('.pedidos-filter-input, .pedidos-filter-select');
        filterInputs.forEach(input=>{
            input.addEventListener('change',()=>this.applyFilters())
        });
        const actionBtns=document.querySelectorAll('[data-action]');
        actionBtns.forEach(btn=>{
            btn.addEventListener('click',(e)=>this.handleAction(e))
        });
        const deleteButtons=document.querySelectorAll('.pedidos-action-btn-delete');
        deleteButtons.forEach(btn=>{
            btn.addEventListener('click',(e)=>{
                e.preventDefault();
                const pedidoId=btn.dataset.id;
                const pedidoNumero=btn.dataset.numero;
                this.confirmDelete(pedidoId,pedidoNumero)
            })
        });
        
        // Close options menu when clicking outside
        document.addEventListener('click',(e)=>{
            if(!e.target.closest('.pedidos-action-btn-more') && !e.target.closest('.pedidos-options-menu')){
                this.closeAllOptionsMenus()
            }
        })
    }
    
    initTableAnimations(){
        const rows=document.querySelectorAll('.pedidos-table tbody tr');
        rows.forEach((row,index)=>{
            row.style.animationDelay=`${index*.05}s`;
            row.classList.add('fade-in-up')
        })
    }
    
    initOptionsMenus(){
        // Initialize options menus for action buttons
        document.querySelectorAll('.pedidos-action-btn-more').forEach((btn,index)=>{
            btn.addEventListener('click',(e)=>{
                e.stopPropagation();
                const row=btn.closest('tr');
                const pedidoId=row.dataset.pedidoId||this.getIdFromRow(row);
                const pedidoNumero=row.dataset.pedidoNumero||this.getNumeroFromRow(row);
                this.showOptionsMenu(btn,pedidoId,pedidoNumero)
            })
        })
    }
    
    getIdFromRow(row){
        const viewBtn=row.querySelector('.pedidos-action-btn-view');
        if(viewBtn){
            const href=viewBtn.getAttribute('href');
            const parts=href.split('/');
            return parts[parts.length-1]
        }
        return ''
    }
    
    getNumeroFromRow(row){
        const numeroCell=row.querySelector('.pedidos-order-number strong');
        return numeroCell?numeroCell.textContent:''
    }
    
    showOptionsMenu(button,pedidoId,pedidoNumero){
        this.closeAllOptionsMenus();
        
        const rect=button.getBoundingClientRect();
        const menu=document.createElement('div');
        menu.className='pedidos-options-menu';
        menu.innerHTML=`
            <button class="pedidos-option-item" onclick="window.location.href='/vendedor/pedidos/${pedidoId}'">
                <i class="bi bi-eye"></i>
                <span>Ver Detalles</span>
            </button>
            <button class="pedidos-option-item" onclick="window.location.href='/vendedor/pedidos/${pedidoId}/editar'">
                <i class="bi bi-pencil"></i>
                <span>Editar Pedido</span>
            </button>
            <button class="pedidos-option-item" onclick="pedidosManager.showStatusModal('${pedidoId}')">
                <i class="bi bi-arrow-repeat"></i>
                <span>Cambiar Estado</span>
            </button>
            <div class="pedidos-option-divider"></div>
            <button class="pedidos-option-item danger" onclick="pedidosManager.confirmDelete('${pedidoId}','${pedidoNumero}')">
                <i class="bi bi-trash"></i>
                <span>Eliminar</span>
            </button>
        `;
        
        document.body.appendChild(menu);
        
        // Position the menu
        const menuRect=menu.getBoundingClientRect();
        let top=rect.bottom+5;
        let left=rect.left-menuRect.width+rect.width;
        
        // Adjust if menu goes off-screen
        if(left<10){
            left=rect.right-menuRect.width
        }
        if(left+menuRect.width>window.innerWidth-10){
            left=window.innerWidth-menuRect.width-10
        }
        if(top+menuRect.height>window.innerHeight-10){
            top=rect.top-menuRect.height-5
        }
        
        menu.style.top=`${top}px`;
        menu.style.left=`${left}px`;
        
        setTimeout(()=>menu.classList.add('active'),10);
        
        this.optionsMenus.set(pedidoId,menu)
    }
    
    closeAllOptionsMenus(){
        this.optionsMenus.forEach(menu=>{
            menu.classList.remove('active');
            setTimeout(()=>menu.remove(),200)
        });
        this.optionsMenus.clear()
    }
    
    applyFilters(){
        const form=document.querySelector('#pedidos-filter-form');
        if(form){
            this.showLoading();
            form.submit()
        }
    }
    
    handleAction(e){
        const action=e.currentTarget.dataset.action;
        const id=e.currentTarget.dataset.id;
        switch(action){
            case'view':
                window.location.href=`/vendedor/pedidos/${id}`;
                break;
            case'edit':
                window.location.href=`/vendedor/pedidos/${id}/editar`;
                break;
            case'delete':
                this.confirmDelete(id);
                break;
            case'update-status':
                this.showStatusModal(id);
                break
        }
    }
    
    createModal(id,title,body,type='primary',buttons=[]){
        const backdrop=document.createElement('div');
        backdrop.className='pedidos-modal-backdrop';
        backdrop.id=`modal-backdrop-${id}`;
        
        const modal=document.createElement('div');
        modal.className='pedidos-modal';
        modal.innerHTML=`
            <div class="pedidos-modal-content">
                <div class="pedidos-modal-header">
                    <h3 class="pedidos-modal-title">
                        ${this.getIconForType(type)}
                        ${title}
                    </h3>
                    <button class="pedidos-modal-close" onclick="pedidosManager.closeModal('${id}')">&times;</button>
                </div>
                <div class="pedidos-modal-body">${body}</div>
                <div class="pedidos-modal-footer">
                    ${buttons.map(btn=>`<button class="${btn.class}" onclick="${btn.onclick}">${btn.text}</button>`).join('')}
                </div>
            </div>
        `;
        
        document.body.appendChild(backdrop);
        document.body.appendChild(modal);
        this.modals.set(id,{backdrop,modal});
        
        setTimeout(()=>{
            backdrop.classList.add('active');
            modal.classList.add('active')
        },10);
        
        return{backdrop,modal}
    }
    
    showModal(id){
        const modal=this.modals.get(id);
        if(modal){
            modal.backdrop.classList.add('active');
            modal.modal.classList.add('active')
        }
    }
    
    closeModal(id){
        const modal=this.modals.get(id);
        if(modal){
            modal.backdrop.classList.remove('active');
            modal.modal.classList.remove('active');
            setTimeout(()=>{
                modal.backdrop.remove();
                modal.modal.remove();
                this.modals.delete(id)
            },300)
        }
    }
    
    closeAllModals(){
        this.modals.forEach((modal,id)=>this.closeModal(id))
    }
    
    confirmDelete(pedidoId,pedidoNumero='este pedido'){
        this.closeAllOptionsMenus();
        this.createModal(
            'confirm-delete',
            '¿Eliminar Pedido?',
            `<p>¿Estás seguro de que deseas eliminar ${pedidoNumero}? Esta acción no se puede deshacer.</p>`,
            'danger',
            [
                {text:'Cancelar',class:'pedidos-btn-secondary',onclick:'pedidosManager.closeModal("confirm-delete")'},
                {text:'Eliminar',class:'pedidos-btn-primary',onclick:`pedidosManager.deletePedido('${pedidoId}')`}
            ]
        )
    }
    
    deletePedido(id){
        this.closeAllModals();
        this.showLoading();
        
        const form=document.createElement('form');
        form.method='POST';
        form.action=`/vendedor/pedidos/${id}`;
        form.innerHTML=`
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit()
    }
    
    showStatusModal(id){
        const body=`
            <div class="pedidos-form-group">
                <label class="pedidos-form-label">Nuevo Estado:</label>
                <select id="nuevo-estado" class="pedidos-form-select">
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="procesando">Procesando</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="pedidos-form-group" id="motivo-group" style="display:none;margin-top:1rem;">
                <label class="pedidos-form-label">Motivo:</label>
                <textarea id="motivo-cancelacion" class="pedidos-form-textarea" placeholder="Ingrese el motivo..."></textarea>
            </div>
        `;
        
        this.createModal(
            'update-status',
            'Actualizar Estado',
            body,
            'info',
            [
                {text:'Cancelar',class:'pedidos-btn-secondary',onclick:'pedidosManager.closeModal("update-status")'},
                {text:'Actualizar',class:'pedidos-btn-primary',onclick:`pedidosManager.updateStatus('${id}')`}
            ]
        );
        
        setTimeout(()=>{
            const select=document.getElementById('nuevo-estado');
            const motivoGroup=document.getElementById('motivo-group');
            select.addEventListener('change',(e)=>{
                motivoGroup.style.display=e.target.value==='cancelado'?'block':'none'
            })
        },100)
    }
    
    updateStatus(id){
        const estado=document.getElementById('nuevo-estado').value;
        const motivo=document.getElementById('motivo-cancelacion')?.value||'';
        
        if(estado==='cancelado'&&!motivo){
            this.showToast('Por favor ingrese el motivo de cancelación','warning');
            return
        }
        
        this.closeAllModals();
        this.showLoading();
        
        const form=document.createElement('form');
        form.method='POST';
        form.action=`/vendedor/pedidos/${id}/update-estado`;
        form.innerHTML=`
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="estado" value="${estado}">
            <input type="hidden" name="motivo_cancelacion" value="${motivo}">
        `;
        document.body.appendChild(form);
        form.submit()
    }
    
    showToast(message,type='success',duration=3000){
        const toastId=`toast-${Date.now()}`;
        const toast=document.createElement('div');
        toast.className=`pedidos-toast ${type}`;
        toast.id=toastId;
        toast.innerHTML=`
            <span class="pedidos-toast-icon">${this.getIconForType(type)}</span>
            <span class="pedidos-toast-message">${message}</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(()=>toast.classList.add('active'),10);
        setTimeout(()=>{
            toast.classList.remove('active');
            setTimeout(()=>toast.remove(),300)
        },duration);
        
        this.toasts.push(toast)
    }
    
    showLoading(){
        const overlay=document.createElement('div');
        overlay.className='pedidos-loading-overlay';
        overlay.id='loading-overlay';
        overlay.innerHTML='<div class="pedidos-loading-spinner"></div>';
        document.body.appendChild(overlay);
        setTimeout(()=>overlay.classList.add('active'),10)
    }
    
    hideLoading(){
        const overlay=document.getElementById('loading-overlay');
        if(overlay){
            overlay.classList.remove('active');
            setTimeout(()=>overlay.remove(),300)
        }
    }
    
    getIconForType(type){
        const icons={
            success:'✔️',
            error:'❌',
            warning:'⚠️',
            info:'ℹ️',
            danger:'🗑️',
            primary:'📝'
        };
        return icons[type]||icons.info
    }
}

// Initialize
let pedidosManager;
document.addEventListener('DOMContentLoaded',()=>{
    pedidosManager=new PedidosManager();
    
    if(typeof successMessage!=='undefined'&&successMessage){
        pedidosManager.showToast(successMessage,'success')
    }
    if(typeof errorMessage!=='undefined'&&errorMessage){
        pedidosManager.showToast(errorMessage,'error')
    }
});

// Add CSS for options menu
const style=document.createElement('style');
style.textContent=`
.pedidos-options-menu{
    position:fixed;
    background:white;
    border-radius:var(--radius-md);
    box-shadow:0 10px 40px rgba(0,0,0,0.15);
    padding:0.5rem 0;
    min-width:200px;
    z-index:10000;
    opacity:0;
    transform:scale(0.95) translateY(-10px);
    transition:all 0.2s ease;
    border:1px solid var(--gray-200);
}
.pedidos-options-menu.active{
    opacity:1;
    transform:scale(1) translateY(0);
}
.pedidos-option-item{
    width:100%;
    display:flex;
    align-items:center;
    gap:0.75rem;
    padding:0.75rem 1rem;
    border:none;
    background:none;
    cursor:pointer;
    transition:all 0.2s ease;
    font-size:0.9rem;
    color:var(--gray-800);
    text-align:left;
}
.pedidos-option-item:hover{
    background:var(--gray-50);
    color:var(--wine-primary);
}
.pedidos-option-item.danger{
    color:var(--danger);
}
.pedidos-option-item.danger:hover{
    background:rgba(220,53,69,0.1);
}
.pedidos-option-item i{
    font-size:1.1rem;
    width:20px;
    text-align:center;
}
.pedidos-option-divider{
    height:1px;
    background:var(--gray-200);
    margin:0.5rem 0;
}
`;
document.head.appendChild(style);
