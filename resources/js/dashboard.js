document.addEventListener('DOMContentLoaded', function() {
    // Actualizar café del día
    function updateCafeDelDia() {
        const cafes = [
            { name: 'Cappuccino Especial', price: 35 },
            { name: 'Latte Artesanal', price: 40 },
            { name: 'Espresso Premium', price: 28 },
            { name: 'Americano Doble', price: 30 },
            { name: 'Mocha Deluxe', price: 45 }
        ];
        
        const today = new Date().getDate();
        const cafeIndex = today % cafes.length;
        const cafeDelDia = cafes[cafeIndex];
        
        const nameElement = document.querySelector('.cafe-del-dia-name');
        const priceElement = document.querySelector('.cafe-del-dia-price');
        
        if (nameElement && priceElement) {
            nameElement.textContent = cafeDelDia.name;
            priceElement.textContent = `$${cafeDelDia.price}`;
        }
    }

    // Actualizar reloj en tiempo real
    function updateClock() {
        const clockElements = document.querySelectorAll('.current-time');
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        clockElements.forEach(element => {
            element.textContent = timeString;
        });
    }

    // Animaciones de entrada
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Inicializar funciones
    updateCafeDelDia();
    updateClock();
    
    // Actualizar reloj cada minuto
    setInterval(updateClock, 60000);
});