/**
 * PAS Market SPA Router
 * Modern Single Page Application routing system
 */

(function() {
    'use strict';

    // Router Configuration
    const Router = {
        routes: {
            '/': {
                title: 'PAS Market - Belanja Online Terpercaya',
                template: 'home',
                controller: 'HomeController'
            },
            '/products': {
                title: 'Semua Produk - PAS Market',
                template: 'products',
                controller: 'ProductsController'
            },
            '/products/:id': {
                title: 'Detail Produk - PAS Market',
                template: 'product-detail',
                controller: 'ProductDetailController'
            },
            '/cart': {
                title: 'Keranjang Belanja - PAS Market',
                template: 'cart',
                controller: 'CartController'
            },
            '/login': {
                title: 'Login - PAS Market',
                template: 'login',
                controller: 'AuthController'
            },

            '/profile': {
                title: 'Profil Saya - PAS Market',
                template: 'profile',
                controller: 'ProfileController'
            },
            '/orders': {
                title: 'Pesanan Saya - PAS Market',
                template: 'orders',
                controller: 'OrdersController'
            },
            '/orders/:id': {
                title: 'Detail Pesanan - PAS Market',
                template: 'order-detail',
                controller: 'OrderDetailController'
            },
            '/about': {
                title: 'Tentang Kami - PAS Market',
                template: 'about',
                controller: 'AboutController'
            },
            '/contact': {
                title: 'Hubungi Kami - PAS Market',
                template: 'contact',
                controller: 'ContactController'
            }
        },
        
        currentRoute: null,
        isNavigating: false,
        
        init() {
            if (document.body?.dataset?.spa !== 'true') {
                return;
            }
            this.setupEventListeners();
            this.handleInitialRoute();
            console.log('✅ PAS Market Router Initialized');
        },
        
        setupEventListeners() {
            // Handle browser back/forward buttons
            window.addEventListener('popstate', (e) => {
                this.handleRouteChange(window.location.pathname, false);
            });
            
            // Handle all link clicks
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (link && this.shouldHandleLink(link)) {
                    e.preventDefault();
                    const href = link.getAttribute('href');
                    this.navigate(href);
                }
            });
            
            // Handle form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form');
            // Skip login/register forms - let them submit normally
            if (form && (form.id === 'loginForm' || form.id === 'registerForm')) {
                return; // Don't prevent default, let form submit normally
            }
            if (!form) return;

            const method = String(form.getAttribute('method') || 'GET').toUpperCase();
            if (method === 'GET') {
                return;
            }

            if (form.dataset.ajax === 'true') {
                e.preventDefault();
                this.handleFormSubmission(form);
            }
        });
        },
        
        shouldHandleLink(link) {
            // Don't handle external links, hash links, or links with target="_blank"
            const href = link.getAttribute('href');
            if (href.includes('?')) {
                return false;
            }
            const isExternal = href.startsWith('http') || 
                   href.startsWith('#') || 
                   link.hasAttribute('download') || 
                   link.getAttribute('target') === '_blank' ||
                   link.classList.contains('no-spa');
            if (isExternal) {
                return false;
            }
            // Do full page navigation for routes not registered in SPA
            if (!this.isRouteRegistered(href)) {
                return false;
            }
            return true;
        },
        
        isRouteRegistered(path) {
            // Exact match
            if (this.routes[path]) {
                return true;
            }
            // Pattern match (e.g. /products/:id)
            for (const pattern of Object.keys(this.routes)) {
                if (this.matchPattern(pattern, path)) {
                    return true;
                }
            }
            return false;
        },
        
        navigate(path, pushState = true) {
            if (this.isNavigating) return;

            if (String(path).includes('?')) {
                window.location.href = path;
                return;
            }
            
            this.isNavigating = true;
            this.showLoading();
            
            // Simulate loading delay for better UX
            setTimeout(() => {
                this.handleRouteChange(path, pushState);
                this.hideLoading();
                this.isNavigating = false;
            }, 300);
        },
        
        handleRouteChange(path, pushState = true) {
            try {
                const route = this.matchRoute(path);
                if (!route) {
                    this.handle404();
                    return;
                }
                
                this.currentRoute = route;
                this.updateDocumentTitle(route.title);
                this.loadRouteContent(route);
                
                if (pushState) {
                    window.history.pushState({ path }, route.title, path);
                }
                
                // Update active navigation
                this.updateActiveNavigation(path);
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
            } catch (error) {
                console.error('Navigation error:', error);
                this.handleError(error);
            }
        },
        
        matchRoute(path) {
            // Exact match first
            if (this.routes[path]) {
                return { ...this.routes[path], params: {} };
            }
            
            // Try pattern matching
            for (const [routePath, route] of Object.entries(this.routes)) {
                const match = this.matchPattern(routePath, path);
                if (match) {
                    return { ...route, params: match.params };
                }
            }
            
            return null;
        },
        
        matchPattern(pattern, path) {
            const patternParts = pattern.split('/');
            const pathParts = path.split('/');
            
            if (patternParts.length !== pathParts.length) {
                return null;
            }
            
            const params = {};
            
            for (let i = 0; i < patternParts.length; i++) {
                const patternPart = patternParts[i];
                const pathPart = pathParts[i];
                
                if (patternPart.startsWith(':')) {
                    // Parameter match
                    const paramName = patternPart.substring(1);
                    params[paramName] = pathPart;
                } else if (patternPart !== pathPart) {
                    // Exact match required
                    return null;
                }
            }
            
            return { params };
        },
        
        handleInitialRoute() {
            const path = window.location.pathname;
            this.handleRouteChange(path, false);
        },
        
        updateDocumentTitle(title) {
            document.title = title;
        },
        
        updateActiveNavigation(path) {
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link, .navbar-nav a').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to matching links
            document.querySelectorAll(`a[href="${path}"], a[href="${path}/"]`).forEach(link => {
                link.classList.add('active');
            });
        },
        
        loadRouteContent(route) {
            // This would typically load content via AJAX
            // For now, we'll simulate it
            console.log(`Loading ${route.template} with controller ${route.controller}`);
            
            // Trigger controller
            if (window[route.controller]) {
                window[route.controller].init(route.params);
            }
        },
        
        showLoading() {
            const loading = document.getElementById('page-loading');
            if (loading) {
                loading.style.display = 'block';
            }
        },
        
        hideLoading() {
            const loading = document.getElementById('page-loading');
            if (loading) {
                loading.style.display = 'none';
            }
        },
        
        handle404() {
            PAS.Notification.show('Halaman tidak ditemukan', 'error');
            this.navigate('/');
        },
        
        handleError(error) {
            console.error('Router error:', error);
            PAS.Notification.show('Terjadi kesalahan saat navigasi', 'error');
        },
        
        handleFormSubmission(form) {
            // Skip login/register forms
            if (form.id === 'loginForm' || form.id === 'registerForm') {
                return; // Let them submit normally
            }
            
            const action = form.getAttribute('action') || window.location.pathname;
            const method = form.getAttribute('method') || 'POST';
            
            // Get form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Simulate form submission
            this.showLoading();
            
            setTimeout(() => {
                this.hideLoading();
                PAS.Notification.show('Form berhasil disubmit!', 'success');
                
                // Handle different form types
                if (form.id === 'profileForm') {
                    this.handleProfileUpdate(data);
                }
            }, 1500);
        },
        
        handleLogin(data) {
            console.log('Login attempt:', data);
            PAS.Notification.show('Login berhasil!', 'success');
            this.navigate('/profile');
        },
        
        handleRegister(data) {
            console.log('Register attempt:', data);
            PAS.Notification.show('Registrasi berhasil!', 'success');
            this.navigate('/login');
        },
        
        handleProfileUpdate(data) {
            console.log('Profile update:', data);
            PAS.Notification.show('Profil berhasil diperbarui!', 'success');
        },
        
        // Navigation helpers
        goToHome() {
            this.navigate('/');
        },
        
        goToProducts() {
            this.navigate('/products');
        },
        
        goToCart() {
            this.navigate('/cart');
        },
        
        goToLogin() {
            this.navigate('/login');
        },
        
        
        goToProfile() {
            this.navigate('/profile');
        },
        
        goToOrders() {
            this.navigate('/orders');
        },
        
        goToAbout() {
            this.navigate('/about');
        },
        
        goToContact() {
            this.navigate('/contact');
        }
    };

    // Controllers
    window.HomeController = {
        init(params) {
            console.log('HomeController initialized');
            this.setupHomeEvents();
        },
        
        setupHomeEvents() {
            // Home-specific event setup
            console.log('Home events setup');
        }
    };

    window.ProductsController = {
        init(params) {
            console.log('ProductsController initialized');
            this.setupProductsEvents();
        },
        
        setupProductsEvents() {
            // Products-specific event setup
            console.log('Products events setup');
        }
    };

    window.ProductDetailController = {
        init(params) {
            console.log('ProductDetailController initialized with params:', params);
            this.setupProductDetailEvents(params);
        },
        
        setupProductDetailEvents(params) {
            // Product detail-specific event setup
            console.log('Product detail events setup for product:', params.id);
        }
    };

    window.CartController = {
        init(params) {
            console.log('CartController initialized');
            this.setupCartEvents();
        },
        
        setupCartEvents() {
            // Cart-specific event setup
            console.log('Cart events setup');
        }
    };

    window.AuthController = {
        init(params) {
            console.log('AuthController initialized');
            this.setupAuthEvents();
        },
        
        setupAuthEvents() {
            // Auth-specific event setup
            console.log('Auth events setup');
        }
    };

    window.ProfileController = {
        init(params) {
            console.log('ProfileController initialized');
            this.setupProfileEvents();
        },
        
        setupProfileEvents() {
            // Profile-specific event setup
            console.log('Profile events setup');
        }
    };

    window.OrdersController = {
        init(params) {
            console.log('OrdersController initialized');
            this.setupOrdersEvents();
        },
        
        setupOrdersEvents() {
            // Orders-specific event setup
            console.log('Orders events setup');
        }
    };

    window.OrderDetailController = {
        init(params) {
            console.log('OrderDetailController initialized with params:', params);
            this.setupOrderDetailEvents(params);
        },
        
        setupOrderDetailEvents(params) {
            // Order detail-specific event setup
            console.log('Order detail events setup for order:', params.id);
        }
    };

    window.AboutController = {
        init(params) {
            console.log('AboutController initialized');
            this.setupAboutEvents();
        },
        
        setupAboutEvents() {
            // About-specific event setup
            console.log('About events setup');
        }
    };

    window.ContactController = {
        init(params) {
            console.log('ContactController initialized');
            this.setupContactEvents();
        },
        
        setupContactEvents() {
            // Contact-specific event setup
            console.log('Contact events setup');
        }
    };

    // Initialize Router
    Router.init();

    // Expose to global scope
    window.PAS = window.PAS || {};
    window.PAS.Router = Router;

})();
