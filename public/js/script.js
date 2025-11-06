document.addEventListener('DOMContentLoaded', function() {
    // Navegación suave
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if(targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Manejo del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        const tipoSeguro = document.getElementById('tipo-seguro').value;
        const email = document.getElementById('email').value;
        
        if(!tipoSeguro || !email) {
            alert('Por favor completa todos los campos obligatorios.');
            return;
        }
        
        // Simulación de envío
        showLoading();
        
        setTimeout(function() {
            hideLoading();
            alert('¡Gracias por tu solicitud! Nos pondremos en contacto contigo pronto con tu cotización personalizada.');
            document.querySelector('form').reset();
        }, 2000);
    });
    
    // Efecto de sombra en el navbar al hacer scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if(window.scrollY > 100) {
            navbar.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
            navbar.style.padding = '10px 0';
        } else {
            navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            navbar.style.padding = '15px 0';
        }
    });
    
    // Animación de elementos al hacer scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('.service-card, .coverage-card, #por-que-elegirnos .col-md-3');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if(elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    }
    
    // Inicializar animaciones
    function initAnimations() {
        const elements = document.querySelectorAll('.service-card, .coverage-card, #por-que-elegirnos .col-md-3');
        
        elements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        });
        
        window.addEventListener('scroll', animateOnScroll);
        animateOnScroll(); // Ejecutar una vez al cargar
    }
    
    // Mostrar/ocultar loading
    function showLoading() {
        let loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading';
        loadingDiv.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }
    
    function hideLoading() {
        const loadingDiv = document.getElementById('loading');
        if(loadingDiv) {
            loadingDiv.remove();
        }
    }
    
    // Campos condicionales del formulario
    document.getElementById('tipo-seguro').addEventListener('change', function() {
        const tipoSeguro = this.value;
        const marcaField = document.getElementById('marca');
        const anoField = document.getElementById('ano');
        
        if(tipoSeguro === 'Auto') {
            marcaField.required = true;
            anoField.required = true;
            marcaField.parentElement.style.display = 'block';
            anoField.parentElement.style.display = 'block';
        } else {
            marcaField.required = false;
            anoField.required = false;
            marcaField.parentElement.style.display = 'block';
            anoField.parentElement.style.display = 'block';
        }
    });
    
    // Inicializar la página
    initAnimations();
    
    // Auto-play del carrusel con pausa al hover
    const carousel = document.getElementById('heroCarousel');
    if(carousel) {
        const carouselInstance = new bootstrap.Carousel(carousel, {
            interval: 5000,
            pause: 'hover'
        });
    }
    
    // Efecto de contador para estadísticas (opcional)
    function initCounters() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const increment = target / 200;
            let current = 0;
            
            const updateCounter = () => {
                if(current < target) {
                    current += increment;
                    counter.innerText = Math.ceil(current);
                    setTimeout(updateCounter, 10);
                } else {
                    counter.innerText = target;
                }
            };
            
            updateCounter();
        });
    }
    
    // Lazy loading para imágenes
    if('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});