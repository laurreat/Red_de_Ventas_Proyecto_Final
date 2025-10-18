/**
 * Header Manager - Gestión moderna de notificaciones y perfil
 * Vendedor Dashboard - Compatible con sistema de modales del dashboard
 */

class HeaderManager{
    constructor(){
        this.notifications=[
            {id:1,type:'success',icon:'bi-check-circle-fill',title:'Venta registrada',text:'Nueva venta por $125,000 registrada exitosamente',time:'Hace 5 minutos',unread:true},
            {id:2,type:'info',icon:'bi-cash-coin',title:'Comisión recibida',text:'Se ha acreditado $15,000 en comisiones',time:'Hace 1 hora',unread:true},
            {id:3,type:'warning',icon:'bi-exclamation-triangle-fill',title:'Meta mensual',text:'Estás al 75% de tu meta del mes',time:'Hace 3 horas',unread:false},
            {id:4,type:'wine',icon:'bi-people-fill',title:'Nuevo referido',text:'Juan Pérez se unió usando tu código',time:'Hace 1 día',unread:false},
            {id:5,type:'info',icon:'bi-cart-check-fill',title:'Pedido entregado',text:'Pedido #12345 entregado al cliente',time:'Hace 2 días',unread:false}
        ];
        this.init();
    }

    init(){
        this.renderNotifications();
        this.attachEventListeners();
        this.updateNotificationBadge();
        this.fixDropdownZIndex();
    }

    fixDropdownZIndex(){
        // Asegurar que los dropdowns tengan z-index correcto
        const dropdowns=document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dd=>{
            dd.style.zIndex='1090';
            dd.style.position='absolute';
        });
    }

    renderNotifications(filter='all'){
        const container=document.getElementById('notificationsContainer');
        if(!container)return;
        
        let filtered=this.notifications;
        if(filter==='unread'){
            filtered=this.notifications.filter(n=>n.unread);
        }
        
        if(filtered.length===0){
            container.innerHTML=`
                <div class="notifications-empty">
                    <i class="bi bi-bell-slash"></i>
                    <p>No hay notificaciones</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML=filtered.map(notification=>`
            <div class="notification-item ${notification.unread?'unread':''}" data-id="${notification.id}">
                <div class="notification-icon ${notification.type}">
                    <i class="bi ${notification.icon}"></i>
                    ${notification.unread?'<span class="notification-badge"></span>':''}
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-text">${notification.text}</div>
                    <div class="notification-time">
                        <i class="bi bi-clock"></i>
                        ${notification.time}
                    </div>
                </div>
            </div>
        `).join('');
        
        // Re-attach eventos después de renderizar
        this.attachNotificationItemEvents();
    }

    attachEventListeners(){
        // Filtros de notificaciones
        document.querySelectorAll('.notifications-filter-btn').forEach(btn=>{
            btn.addEventListener('click',()=>{
                document.querySelectorAll('.notifications-filter-btn').forEach(b=>b.classList.remove('active'));
                btn.classList.add('active');
                const filter=btn.dataset.filter;
                this.renderNotifications(filter);
            });
        });

        // Marcar todas como leídas
        const markAllBtn=document.getElementById('markAllReadBtn');
        if(markAllBtn){
            markAllBtn.addEventListener('click',()=>{
                this.markAllAsRead();
            });
        }

        // Eventos de items de notificación
        this.attachNotificationItemEvents();

        // Prevenir cierre del dropdown al hacer click dentro
        document.querySelectorAll('.notifications-dropdown, .profile-dropdown').forEach(dropdown=>{
            dropdown.addEventListener('click',e=>{
                // Solo prevenir si no es el botón de cerrar o link
                if(!e.target.closest('a[href]') && !e.target.closest('.dropdown-item')){
                    e.stopPropagation();
                }
            });
        });
    }

    attachNotificationItemEvents(){
        document.querySelectorAll('.notification-item').forEach(item=>{
            item.addEventListener('click',()=>{
                const id=parseInt(item.dataset.id);
                this.markAsRead(id);
                this.showNotificationDetail(id);
            });
        });
    }

    markAsRead(id){
        const notification=this.notifications.find(n=>n.id===id);
        if(notification && notification.unread){
            notification.unread=false;
            this.updateNotificationBadge();
            this.renderNotifications();
            this.showToast('Notificación marcada como leída','success');
        }
    }

    markAllAsRead(){
        let count=this.notifications.filter(n=>n.unread).length;
        if(count>0){
            this.notifications.forEach(n=>n.unread=false);
            this.updateNotificationBadge();
            this.renderNotifications();
            this.showToast(`${count} notificación${count>1?'es':''} marcada${count>1?'s':''} como leída${count>1?'s':''}`, 'success');
        }
    }

    updateNotificationBadge(){
        const unreadCount=this.notifications.filter(n=>n.unread).length;
        const badge=document.querySelector('.badge-indicator');
        const headerBadge=document.querySelector('.notifications-header .badge');
        
        if(badge){
            if(unreadCount>0){
                badge.textContent=unreadCount>9?'9+':unreadCount;
                badge.style.display='flex';
            }else{
                badge.style.display='none';
            }
        }
        
        if(headerBadge){
            headerBadge.textContent=`${unreadCount} nueva${unreadCount!==1?'s':''}`;
        }
    }

    showNotificationDetail(id){
        const notification=this.notifications.find(n=>n.id===id);
        if(!notification)return;
        
        // Si existe DashboardManager, usar su sistema de modales
        if(typeof dashboardManager!=='undefined' && dashboardManager.createModal){
            const modalBody=`
                <div style="padding: 1rem 0;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="notification-icon ${notification.type}" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="bi ${notification.icon}"></i>
                        </div>
                        <div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem;">${notification.title}</div>
                            <div style="font-size: 0.875rem; color: var(--gray-500);">
                                <i class="bi bi-clock"></i> ${notification.time}
                            </div>
                        </div>
                    </div>
                    <p style="margin: 0;">${notification.text}</p>
                </div>
            `;
            const modalFooter=`
                <button class="vendedor-btn vendedor-btn-wine" onclick="dashboardManager.closeModal('notification-detail')">
                    Cerrar
                </button>
            `;
            dashboardManager.createModal('notification-detail', notification.title, modalBody, modalFooter, notification.type);
            dashboardManager.showModal('notification-detail');
        } else {
            console.log('Mostrando detalle de notificación:',notification);
        }
    }

    showToast(message,type='info'){
        // Si existe DashboardManager, usar su sistema de toasts
        if(typeof dashboardManager!=='undefined' && dashboardManager.showToast){
            dashboardManager.showToast(message, type);
        } else if(typeof window.showToast==='function'){
            window.showToast(message,type);
        } else {
            console.log(`Toast [${type}]: ${message}`);
        }
    }

    refreshNotifications(){
        console.log('Actualizando notificaciones...');
        // Aquí puedes agregar llamada AJAX para obtener notificaciones reales
        setTimeout(()=>{
            this.showToast('Notificaciones actualizadas','success');
        },500);
    }
}

// Inicializar cuando el DOM esté listo
if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded',()=>{
        window.headerManager=new HeaderManager();
    });
}else{
    window.headerManager=new HeaderManager();
}

// Funciones globales de utilidad
function copyReferralCode(){
    const code=document.querySelector('[data-referral-code]')?.dataset.referralCode;
    if(code){
        navigator.clipboard.writeText(code).then(()=>{
            if(typeof dashboardManager!=='undefined' && dashboardManager.showToast){
                dashboardManager.showToast('Código de referido copiado','success');
            } else if(typeof window.showToast==='function'){
                window.showToast('Código de referido copiado','success');
            } else {
                alert('Código copiado: '+code);
            }
        }).catch(()=>{
            if(typeof dashboardManager!=='undefined' && dashboardManager.showToast){
                dashboardManager.showToast('Error al copiar código','error');
            }
        });
    }
}

// Animaciones de entrada para dropdowns (compatible con Bootstrap)
document.addEventListener('shown.bs.dropdown',function(e){
    const dropdown=e.target.querySelector('.dropdown-menu');
    if(dropdown){
        dropdown.style.animation='fadeInDown 0.2s ease';
        dropdown.style.zIndex='1090';
    }
});

// Asegurar z-index correcto al mostrar dropdown
document.addEventListener('show.bs.dropdown',function(e){
    const dropdown=e.target.querySelector('.dropdown-menu');
    if(dropdown){
        dropdown.style.zIndex='1090';
        dropdown.style.position='absolute';
    }
});
