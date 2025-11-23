console.log("welcome.js cargado");

document.addEventListener("DOMContentLoaded", function () {
  // ---------- Smooth scroll para anchors internos ----------
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (!href || href === "#") return; // no interceptar
      const target = document.querySelector(href);
      if (!target) return;               // si no hay destino, dejar que el link actúe normal
      e.preventDefault();
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });

  // ---------- Header scroll effect (con guardas) ----------
  const header = document.querySelector(".welcome-header");
  if (header) {
    const applyHeaderStyle = () => {
      if (window.scrollY > 100) {
        header.style.background = "rgba(10, 10, 10, 0.98)";
        header.style.boxShadow = "0 2px 20px rgba(0,0,0,0.5)";
      } else {
        header.style.background = "rgba(10, 10, 10, 0.8)";
        header.style.boxShadow = "none";
      }
    };
    window.addEventListener("scroll", applyHeaderStyle, { passive: true });
    applyHeaderStyle();
    // bfcache: al volver con historial, re-aplicar estado
    window.addEventListener("pageshow", (e) => {
      if (e.persisted) applyHeaderStyle();
    });
  }

  // ---------- Lazy load del mapa (solo si existe) ----------
  function loadMap() {
    const mapContainer = document.querySelector("#map-container");
    if (!mapContainer || mapContainer.querySelector("iframe")) return;

    mapContainer.innerHTML = "";
    const iframe = document.createElement("iframe");
    iframe.src =
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3792.8!2d-63.1631317!3d-17.7433968!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTfCsDQ0JzM2LjIiUyA2M8KwMDknNDcuMyJX!5e0!3m2!1ses!2sbo!4v1640995200000!5m2!1ses!2sbo";
    iframe.width = "100%";
    iframe.height = "320";
    iframe.style.border = "0";
    iframe.setAttribute("allowfullscreen", "");
    iframe.loading = "lazy";
    iframe.referrerPolicy = "no-referrer-when-downgrade";
    iframe.className = "rounded-2xl w-full h-full";
    mapContainer.appendChild(iframe);
  }

  const mapBox = document.querySelector("#map-container");
  if (mapBox) {
    if ("IntersectionObserver" in window) {
      const mapObserver = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              loadMap();
              mapObserver.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.1 }
      );
      mapObserver.observe(mapBox);
    } else {
      // Fallback si el navegador no soporta IO
      loadMap();
    }
  }

  // ---------- Estado abierto/cerrado (solo si existen los nodos) ----------
  function updateCafeStatus() {
    const now = new Date();
    const hour = now.getHours();
    const isOpen = hour >= 6 && hour < 22; // 6AM–10PM

    const statusText = document.querySelector(".cafe-status-text");
    const statusIndicator = document.querySelector(".cafe-status-indicator");

    if (statusText && statusIndicator) {
      if (isOpen) {
        statusText.textContent = "¡Abierto!";
        statusText.className = "cafe-status-text text-2xl font-bold text-green-400";
        statusIndicator.className = "cafe-status-indicator w-3 h-3 bg-green-400 rounded-full";
      } else {
        statusText.textContent = "Cerrado";
        statusText.className = "cafe-status-text text-2xl font-bold text-red-400";
        statusIndicator.className = "cafe-status-indicator w-3 h-3 bg-red-400 rounded-full";
      }
    }
  }

  // ---------- Café del día (si hay contenedores) ----------
  function updateCafeDelDia() {
    const cafes = [
      { name: "Espresso Premium", price: 28 },
      { name: "Cappuccino Especial", price: 35 },
      { name: "Latte Artesanal", price: 40 },
      { name: "Americano Doble", price: 30 },
      { name: "Mocha Deluxe", price: 45 },
      { name: "Macchiato Caramelo", price: 42 },
      { name: "Flat White", price: 38 },
      { name: "Cortado Especial", price: 32 },
    ];
    const cafeDelDia = cafes[new Date().getDate() % cafes.length];

    const cafeTitle = document.querySelector(".cafe-del-dia-title");
    if (cafeTitle) {
      cafeTitle.textContent = `${cafeDelDia.name} - Café del Día`;
    }

    const specialPrice = document.querySelector(".precio-especial");
    if (specialPrice) {
      specialPrice.innerHTML = `
        <div class="flex items-center justify-between border-t border-zinc-700 pt-4 mt-4">
          <span class="text-amber-400 font-medium">Especial del día</span>
          <span class="text-green-400 font-bold text-lg">$${cafeDelDia.price}</span>
        </div>
      `;
    }
  }

  // ---------- Animaciones de entrada (con guardas) ----------
  const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
          entry.target.classList.add("animate-in");
        }
      });
    }, observerOptions);

    document
      .querySelectorAll(".product-card, .welcome-card, .contact-item")
      .forEach((el) => {
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "all 0.6s ease-out";
        observer.observe(el);
      });
  }

  // ---------- Mobile menu toggle (si existen nodos) ----------
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const mobileMenu = document.querySelector(".mobile-menu");
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
    });
  }

  // ---------- Inits ----------
  updateCafeStatus();
  updateCafeDelDia();
  // Actualizar estado cada minuto
  setInterval(updateCafeStatus, 60000);
});
