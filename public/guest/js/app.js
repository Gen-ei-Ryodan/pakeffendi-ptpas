/**
 * PAS Market Guest Application JavaScript
 * Modern, responsive functionality for guest users
 */

(function() {
    'use strict';

    // PAS Market Application Object
    window.PAS = window.PAS || {};
    
    // Cart Management
    const Cart = {
        summary: { total_items: 0 },
        _lastAddAt: new Map(),
        
        init() {
            this.refreshSummary();
        },
        
        csrfToken() {
            const el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : '';
        },
        
        async refreshSummary() {
            try {
                const data = await API.request('/cart/summary', {
                    method: 'GET',
                });
                this.summary = data.summary || { total_items: 0 };
                this.updateUI();
            } catch (_) {
                this.summary = { total_items: 0 };
                this.updateUI();
            }
        },
        
        async addItem(product) {
            if (!window.PAS?.auth?.loggedIn) {
                const from = window.location.pathname + window.location.search;
                window.location.href = `${window.PAS?.auth?.loginUrl || '/login'}?redirect=${encodeURIComponent(from)}`;
                return;
            }

            // Sales: customer must be selected on the cart page first (once per session)
            // Backend reads the selected customer from the cookie — no need to ask again
            if (window.PAS?.auth?.isSales) {
                const productId = product?.id;
                if (!productId) return;

                const rawQty = product?.quantity;
                let quantity = typeof rawQty === 'number' ? rawQty : parseInt(rawQty, 10);
                if (!Number.isFinite(quantity)) quantity = 1;
                quantity = Math.max(1, Math.min(9999, quantity));

                const now = Date.now();
                const lastAt = this._lastAddAt.get(String(productId)) || 0;
                if (now - lastAt < 700) return;
                this._lastAddAt.set(String(productId), now);

                try {
                    const data = await API.request('/cart/items', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken() },
                        body: JSON.stringify({ product_id: productId, quantity }),
                    });
                    this.summary = data.summary || this.summary;
                    this.updateUI();
                    this.showNotification('Produk ditambahkan ke keranjang!', 'success');
                } catch (err) {
                    // Backend will reject if no customer is selected (cookie missing)
                    // Show a helpful message so the user knows to go to cart page first
                    this.showNotification('Silakan pilih customer terlebih dahulu di halaman Keranjang.', 'warning');
                }
                return;
            }

            const productId = product?.id;
            if (!productId) return;

            const rawQty = product?.quantity;
            let quantity = typeof rawQty === 'number' ? rawQty : parseInt(rawQty, 10);
            if (!Number.isFinite(quantity)) quantity = 1;
            quantity = Math.max(1, Math.min(9999, quantity));

            const now = Date.now();
            const lastAt = this._lastAddAt.get(String(productId)) || 0;
            if (now - lastAt < 700) {
                return;
            }
            this._lastAddAt.set(String(productId), now);

            try {
                const data = await API.request('/cart/items', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ product_id: productId, quantity }),
                });
                this.summary = data.summary || this.summary;
                this.updateUI();
                this.showNotification('Produk ditambahkan ke keranjang!', 'success');
            } catch (_) {
            }
        },

        async removeItem(productId) {
            if (!window.PAS?.auth?.loggedIn) {
                const from = window.location.pathname + window.location.search;
                window.location.href = `${window.PAS?.auth?.loginUrl || '/login'}?redirect=${encodeURIComponent(from)}`;
                return;
            }

            if (!productId) return;

            try {
                const data = await API.request(`/cart/items/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                });
                this.summary = data.summary || this.summary;
                this.updateUI();
                this.showNotification('Produk dihapus dari keranjang!', 'info');
            } catch (_) {
            }
        },
        
        async updateQuantity(productId, quantity) {
            if (!window.PAS?.auth?.loggedIn) {
                const from = window.location.pathname + window.location.search;
                window.location.href = `${window.PAS?.auth?.loginUrl || '/login'}?redirect=${encodeURIComponent(from)}`;
                return;
            }

            if (!productId) return;

            const qty = Number.isFinite(quantity) ? quantity : parseInt(quantity) || 0;

            try {
                const data = await API.request(`/cart/items/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ quantity: qty }),
                });
                this.summary = data.summary || this.summary;
                this.updateUI();
            } catch (_) {
            }
        },
        
        updateUI() {
            const cartCount = document.getElementById('cartCount');
            const totalItems = parseInt(this.summary?.total_items || 0);
            
            if (cartCount) {
                if (totalItems > 0) {
                    cartCount.textContent = totalItems;
                    cartCount.style.display = 'block';
                } else {
                    cartCount.style.display = 'none';
                }
            }
        },
        
        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }
    };

    // Search Functionality
    const Search = {
        init() {
            this.setupEventListeners();
        },
        
        setupEventListeners() {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const searchInputMobile = document.getElementById('searchInputMobile');
            const searchBtnMobile = document.getElementById('searchBtnMobile');
            
            if (searchInput && searchBtn) {
                searchBtn.addEventListener('click', () => this.performSearch(searchInput.value));
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.performSearch(searchInput.value);
                    }
                });
            }
            
            if (searchInputMobile && searchBtnMobile) {
                searchBtnMobile.addEventListener('click', () => this.performSearch(searchInputMobile.value));
                searchInputMobile.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.performSearch(searchInputMobile.value);
                    }
                });
            }
        },
        
        performSearch(query) {
            if (!query.trim()) return;
            window.location.href = `/products?q=${encodeURIComponent(query.trim())}`;
        },
    };

    // Back to Top Button
    const BackToTop = {
        init() {
            this.button = document.getElementById('backToTop');
            if (this.button) {
                this.setupEventListeners();
            }
        },
        
        setupEventListeners() {
            // Show/hide button based on scroll position
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    this.button.style.display = 'block';
                } else {
                    this.button.style.display = 'none';
                }
            });
            
            // Scroll to top when clicked
            this.button.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    };

    // Product Card Interactions
    const ProductCards = {
        init() {
            this.setupEventListeners();
        },
        
        setupEventListeners() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.category-card')) {
                    const card = e.target.closest('.category-card');
                    const categoryId = card.dataset.categoryId;
                    if (categoryId) {
                        window.location.href = `/products?category_id=${encodeURIComponent(categoryId)}`;
                        return;
                    }
                }

                // Quantity +/- buttons
                const qtyBtn = e.target.closest('.qty-btn');
                if (qtyBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const card = qtyBtn.closest('.product-card');
                    const input = card?.querySelector('.qty-input');
                    if (!input) return;
                    const current = parseInt(input.value, 10) || 1;
                    const min = parseInt(input.min, 10) || 1;
                    const max = parseInt(input.max, 10) || 9999;
                    const action = qtyBtn.dataset.action;
                    let newVal = current;
                    if (action === 'increase') {
                        newVal = Math.min(max, current + 1);
                    } else if (action === 'decrease') {
                        newVal = Math.max(min, current - 1);
                    }
                    input.value = newVal;
                    return;
                }

                // Add to cart button
                if (e.target.closest('.btn-add-to-cart')) {
                    e.preventDefault();
                    const button = e.target.closest('.btn-add-to-cart');
                    const productData = this.getProductData(button);
                    Cart.addItem(productData);
                }
            });
        },
        
        getProductData(button) {
            const card = button.closest('.product-card');
            const qtyInput = card?.querySelector('.qty-input');
            let quantity = 1;
            if (qtyInput) {
                const raw = parseInt(qtyInput.value, 10);
                const min = parseInt(qtyInput.min, 10) || 1;
                const max = parseInt(qtyInput.max, 10) || 9999;
                quantity = Number.isFinite(raw) ? Math.max(min, Math.min(max, raw)) : 1;
            }
            return {
                id: card.dataset.productId,
                name: card.querySelector('.product-title')?.textContent || '',
                price: parseFloat(card.querySelector('.product-price')?.textContent?.replace(/[^0-9,-]/g, '').replace(',', '.') || 0),
                image: card.querySelector('.product-image')?.src || '',
                quantity: quantity
            };
        },
    };

    // Smooth Scrolling for Navigation Links
    const SmoothScroll = {
        init() {
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
        }
    };

    // API Integration Helper
    const API = {
        async request(url, options = {}) {
            try {
                const response = await fetch(url, {
                    ...options,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(options.headers || {})
                    }
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error Response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                Cart.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'danger');
                throw error;
            }
        },
        
        // Get home data
        async getHomeData() {
            return this.request(window.PAS.urls.home);
        },
        
        // Get products
        async getProducts(params = {}) {
            const queryString = new URLSearchParams(params).toString();
            return this.request(`${window.PAS.urls.products}?${queryString}`);
        },
        
        // Get product detail
        async getProductDetail(productId) {
            return this.request(`${window.PAS.urls.productShow}/${productId}`);
        }
    };

    // Initialize Application
    const App = {
        init() {
            console.log('PAS Market Guest Application Initialized');
            
            // Initialize all modules
            Cart.init();
            Search.init();
            BackToTop.init();
            ProductCards.init();
            SmoothScroll.init();
            
            // Add fade-in animation to elements
            this.addFadeInAnimation();
            
            // Setup AJAX loading for dynamic content
            this.setupAjaxLoading();
            
            // Initialize tooltips
            this.initializeTooltips();
        },
        
        addFadeInAnimation() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            });
            
            document.querySelectorAll('.card, .product-card, .category-card').forEach(el => {
                observer.observe(el);
            });
        },
        
        setupAjaxLoading() {
            // Add loading class to body during AJAX requests
            $(document).ajaxStart(() => {
                document.body.classList.add('loading');
            }).ajaxStop(() => {
                document.body.classList.remove('loading');
            });
        },
        
        initializeTooltips() {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    };

    // DOM Ready
    document.addEventListener('DOMContentLoaded', () => {
        App.init();
    });

    // Expose global functions
    window.PAS.Cart = Cart;
    window.PAS.Search = Search;
    window.PAS.API = API;

})();
