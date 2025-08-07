console.log('welcome.js cargado');
// Smooth scroll para navegación
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Header scroll effect
    const header = document.querySelector('.welcome-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(10, 10, 10, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.5)';
        } else {
            header.style.background = 'rgba(10, 10, 10, 0.8)';
            header.style.boxShadow = 'none';
        }
    });

    // Función para cargar el mapa - NUEVA
    function loadMap() {
        const mapContainer = document.querySelector('#map-container');
        if (mapContainer && !mapContainer.querySelector('iframe')) {
            // Remover el contenido de "Cargando..."
            mapContainer.innerHTML = '';
            
            const iframe = document.createElement('iframe');
            iframe.src = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3792.8!2d-63.1631317!3d-17.7433968!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTfCsDQ0JzM2LjIiUyA2M8KwMDknNDcuMyJX!5e0!3m2!1ses!2sbo!4v1640995200000!5m2!1ses!2sbo';
            iframe.width = '100%';
            iframe.height = '320';
            iframe.style.border = '0';
            iframe.allowFullscreen = true;
            iframe.loading = 'lazy';
            iframe.referrerPolicy = 'no-referrer-when-downgrade';
            iframe.className = 'rounded-2xl w-full h-full';
            
            mapContainer.appendChild(iframe);
        }
    }

    // Lazy loading para el mapa - MEJORADO
    const mapContainer = document.querySelector('#map-container');
    if (mapContainer) {
        const mapObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadMap();
                    mapObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        mapObserver.observe(mapContainer);
    }

    // Sistema de horarios y estado del café - NUEVO
    function updateCafeStatus() {
        const now = new Date();
        const hour = now.getHours();
        const isOpen = hour >= 6 && hour < 22; // 6 AM a 10 PM
        
        const statusElement = document.querySelector('.cafe-status');
        const statusText = document.querySelector('.cafe-status-text');
        const statusIndicator = document.querySelector('.cafe-status-indicator');
        
        if (statusElement && statusText && statusIndicator) {
            if (isOpen) {
                statusText.textContent = '¡Abierto!';
                statusText.className = 'text-2xl font-bold text-green-400';
                statusIndicator.className = 'w-3 h-3 bg-green-400 rounded-full';
            } else {
                statusText.textContent = 'Cerrado';
                statusText.className = 'text-2xl font-bold text-red-400';
                statusIndicator.className = 'w-3 h-3 bg-red-400 rounded-full';
            }
        }
    }

    // Café del día aleatorio - NUEVO
    function updateCafeDelDia() {
        const cafes = [
            { name: 'Espresso Premium', price: 28 },
            { name: 'Cappuccino Especial', price: 35 },
            { name: 'Latte Artesanal', price: 40 },
            { name: 'Americano Doble', price: 30 },
            { name: 'Mocha Deluxe', price: 45 },
            { name: 'Macchiato Caramelo', price: 42 },
            { name: 'Flat White', price: 38 },
            { name: 'Cortado Especial', price: 32 }
        ];
        
        const today = new Date().getDate();
        const cafeIndex = today % cafes.length;
        const cafeDelDia = cafes[cafeIndex];
        
        // Actualizar título del café del día
        const cafeTitle = document.querySelector('.cafe-del-dia-title');
        if (cafeTitle) {
            cafeTitle.textContent = `${cafeDelDia.name} - Café del Día`;
        }
        
        // Agregar precio especial
        const specialPrice = document.querySelector('.precio-especial');
        if (specialPrice) {
            specialPrice.innerHTML = `
                <div class="flex items-center justify-between border-t border-zinc-700 pt-4 mt-4">
                    <span class="text-amber-400 font-medium">Especial del día</span>
                    <span class="text-green-400 font-bold text-lg">$${cafeDelDia.price}</span>
                </div>
            `;
        }
    }

    // Animaciones de entrada
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observar elementos para animaciones
    document.querySelectorAll('.product-card, .welcome-card, .contact-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });

    // Mobile menu toggle - NUEVO
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Inicializar funciones
    updateCafeStatus();
    updateCafeDelDia();
    
    // Actualizar estado cada minuto
    setInterval(updateCafeStatus, 60000);
});