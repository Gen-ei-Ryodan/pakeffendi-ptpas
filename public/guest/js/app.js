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

            // Sales: show customer selection modal first
            if (window.PAS?.auth?.isSales) {
                const customerId = await this._pickSalesCustomer();
                if (!customerId) {
                    return; // user cancelled
                }
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
                        body: JSON.stringify({ product_id: productId, quantity, customer_id: customerId }),
                    });
                    this.summary = data.summary || this.summary;
                    this.updateUI();
                    this.showNotification('Produk ditambahkan ke keranjang!', 'success');
                } catch (_) {}
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
        
        /**
         * Show customer selection modal for sales users.
         * Returns a Promise that resolves with customer ID or null if cancelled.
         */
        _pickSalesCustomer() {
            return new Promise((resolve) => {
                const modalEl = document.getElementById('salesCustomerModal');
                if (!modalEl) { resolve(null); return; }

                // Clean up any stale backdrops from previous modal instances
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');

                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                const listEl = document.getElementById('salesCustList');
                const searchEl = document.getElementById('salesCustSearch');
                const emptyEl = document.getElementById('salesCustEmpty');
                let customers = [];
                let resolved = false;

                const finish = (value) => {
                    if (resolved) return;
                    resolved = true;
                    modal.hide();
                    resolve(value);
                };

                const renderList = (filter = '') => {
                    const q = (filter || '').trim().toLowerCase();
                    const filtered = q ? customers.filter(c =>
                        (c.full_name || '').toLowerCase().includes(q) ||
                        (c.company_name || '').toLowerCase().includes(q)
                    ) : customers;

                    if (filtered.length === 0) {
                        listEl.innerHTML = '';
                        emptyEl.classList.remove('d-none');
                    } else {
                        emptyEl.classList.add('d-none');
                        listEl.innerHTML = filtered.map(c => `
                            <button type="button" class="list-group-item list-group-item-action" data-cid="${c.id}">
                                <div class="fw-semibold">${c.full_name}</div>
                                ${c.company_name ? `<small class="text-muted">${c.company_name}</small>` : ''}
                            </button>
                        `).join('');
                    }
                };

                const onListClick = (e) => {
                    const btn = e.target.closest('[data-cid]');
                    if (btn) {
                        const cid = parseInt(btn.dataset.cid, 10);
                        finish(Number.isFinite(cid) ? cid : null);
                    }
                };

                const onSearchInput = () => renderList(searchEl.value);

                const onShown = async () => {
                    if (searchEl) searchEl.value = '';
                    try {
                        const data = await API.request(window.PAS?.urls?.myCustomers || '/cart/my-customers', { method: 'GET' });
                        customers = Array.isArray(data.customers) ? data.customers : [];
                        renderList();
                    } catch (_) {
                        listEl.innerHTML = '<div class="text-center text-danger py-3 small">Gagal memuat customer.</div>';
                    }
                };

                const onHidden = () => {
                    if (!resolved) finish(null);
                };

                const cleanup = () => {
                    listEl.removeEventListener('click', onListClick);
                    if (searchEl) searchEl.removeEventListener('input', onSearchInput);
                    modalEl.removeEventListener('shown.bs.modal', onShown);
                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                };

                // Remove any leftover listeners first, then register fresh ones
                cleanup();
                listEl.addEventListener('click', onListClick);
                if (searchEl) searchEl.addEventListener('input', onSearchInput);
                modalEl.addEventListener('shown.bs.modal', onShown);
                modalEl.addEventListener('hidden.bs.modal', onHidden);

                modal.show();
            });
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
            if (!query.trim()) {
                Cart.showNotification('Masukkan kata kunci pencarian', 'warning');
                return;
            }
            
            // Show loading
            this.showLoading();
            
            // Simulate search (replace with actual API call)
            setTimeout(() => {
                this.hideLoading();
                Cart.showNotification(`Mencari: "${query}"`, 'info');
                // Redirect to search results page or update content
                console.log('Searching for:', query);
            }, 1000);
        },
        
        showLoading() {
            const loading = document.createElement('div');
            loading.id = 'search-loading';
            loading.className = 'position-fixed top-50 start-50 translate-middle';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            loading.style.zIndex = '9999';
            document.body.appendChild(loading);
        },
        
        hideLoading() {
            const loading = document.getElementById('search-loading');
            if (loading) {
                loading.remove();
            }
        }
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

                // Add to cart button
                if (e.target.closest('.btn-add-to-cart')) {
                    e.preventDefault();
                    const button = e.target.closest('.btn-add-to-cart');
                    const productData = this.getProductData(button);
                    Cart.addItem(productData);
                }
                
                // Product card click (for detail view)
                if (e.target.closest('.product-card')) {
                    const card = e.target.closest('.product-card');
                    const productId = card.dataset.productId;
                    if (productId) {
                        this.showProductDetail(productId);
                    }
                }
            });
        },
        
        getProductData(button) {
            const card = button.closest('.product-card');
            return {
                id: card.dataset.productId,
                name: card.querySelector('.product-title')?.textContent || 'Produk',
                price: parseFloat(card.querySelector('.product-price')?.textContent?.replace(/[^0-9,-]/g, '').replace(',', '.') || 0),
                image: card.querySelector('.product-image')?.src || ''
            };
        },
        
        showProductDetail(productId) {
            // Simulate loading product detail
            console.log('Loading product detail for ID:', productId);
            // This would typically open a modal or navigate to product page
        }
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
