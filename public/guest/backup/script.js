document.addEventListener('DOMContentLoaded', () => {
    // --- State & DOM Elements ---
    const dom = {
        loading: document.getElementById('loading-screen'),
        mainContent: document.getElementById('main-content'),
        screens: {
            home: document.getElementById('home-screen'),
            brand: document.getElementById('brand-screen'),
            qorder: document.getElementById('qorder-screen'),
            akun: document.getElementById('akun-screen'),
            info: document.getElementById('info-screen'),
            cart: document.getElementById('cart-screen'),
            notif: document.getElementById('notification-screen'),
            productList: document.getElementById('product-list-screen'),
            productDetail: document.getElementById('product-detail-screen'),
        },
        navItems: document.querySelectorAll('.nav-item'),
        desktopNavLinks: document.querySelectorAll('.app-header .nav-link'),
        buttons: {
            menu: document.getElementById('menu-btn'),
            cart: document.getElementById('cart-btn'),
            notif: document.getElementById('notif-btn'),
            closeSidebar: document.getElementById('close-sidebar'),
            closeCart: document.getElementById('close-cart'),
            closeNotif: document.getElementById('close-notif'),
            brand: document.getElementById('brand-btn'),
            qorder: document.getElementById('qorder-btn'),
            akun: document.getElementById('akun-btn'),
            info: document.getElementById('info-btn'),
            categoryDesktop: document.getElementById('category-btn'),
        },
        sidebar: document.getElementById('sidebar-menu'),
        cartBadges: document.querySelectorAll('[data-cart-badge]'),
    };

    const urls = {
        sync: document.body.dataset.guestSyncUrl,
        home: document.body.dataset.guestHomeUrl,
        order: document.body.dataset.guestOrderUrl,
        productsIndex: document.body.dataset.guestProductsUrl || '/api/guest/products',
        productShowBase: document.body.dataset.guestProductShowBaseUrl || '/api/guest/products',
    };

    const state = {
        productList: {
            q: '',
            category_id: null,
            brand_id: null,
            sort: 'terbaru',
        },
        currentProduct: null,
        productsLast: [],
        ordersKey: 'guest_orders_v1',
    };

    const formatRupiah = (value) => {
        const n = Number(value || 0);
        if (Number.isNaN(n)) return 'Rp 0';
        return `Rp ${Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}`;
    };

    const normalizeUrl = (url) => {
        if (!url) return null;
        if (url.startsWith('http://') || url.startsWith('https://')) return url;
        return `${window.location.origin}${url.startsWith('/') ? '' : '/'}${url}`;
    };

    const buildQuery = (params) => {
        const usp = new URLSearchParams();
        Object.entries(params || {}).forEach(([k, v]) => {
            if (v === null || v === undefined || v === '') return;
            usp.set(k, String(v));
        });
        const qs = usp.toString();
        return qs ? `?${qs}` : '';
    };

    // --- Toast Notification System ---
    const createToastContainer = () => {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            dom.mainContent.appendChild(container);
        }
        return container;
    };

    const showToast = (message, type = 'success') => {
        const container = createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-out forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // --- Initialization ---
    const init = () => {
        // Fade out loading screen
        setTimeout(() => {
            if (dom.loading) {
                dom.loading.style.opacity = '0';
                dom.loading.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    dom.loading.style.display = 'none';
                    dom.mainContent.style.display = 'flex';
                }, 500);
            }
        }, 1000);

        setupNavigation();
        setupCart();
        setupSidebar();
        setupQOrder();
        setupProductInteractions();
        setupAkunTabs();
        setupProductListControls();
        setupCheckout();
    };

    // --- Navigation Logic ---
    const updateDesktopNav = (screenKey) => {
        if (!dom.desktopNavLinks) return;
        dom.desktopNavLinks.forEach((link) => {
            link.classList.toggle('active', link.dataset.screen === screenKey);
        });
    };

    const updateView = (screenKey) => {
        // Hide all screens
        Object.values(dom.screens).forEach(screen => {
            if (screen) screen.style.display = 'none';
        });
        
        // Reset nav active states
        dom.navItems.forEach(item => item.classList.remove('active'));

        // Show target screen
        if (dom.screens[screenKey]) {
            dom.screens[screenKey].style.display = screenKey === 'home' ? 'block' : 'flex';
        }

        // Restore search input value when returning to product list screen
        if (screenKey === 'productList') {
            const input = document.querySelector('[data-product-search]');
            if (input) input.value = state.productList.q || '';
        }

        // Map screen to nav index
        const screenToNavIndex = {
            'home': 0,
            'brand': 1,
            'qorder': 2,
            'akun': 3,
            'info': 4
        };

        const navIndex = screenToNavIndex[screenKey];
        if (navIndex !== undefined) {
            if (dom.navItems[navIndex]) dom.navItems[navIndex].classList.add('active');
            updateDesktopNav(screenKey);
        }
    };

    const navigateTo = (screenKey, addToHistory = true) => {
        updateView(screenKey);
        if (addToHistory) {
            history.pushState({ screen: screenKey }, '', `#${screenKey}`);
        }
    };

    // Handle Browser Back Button
    window.addEventListener('popstate', (event) => {
        const screenKey = event.state?.screen || 'home';
        updateView(screenKey);
    });

    const setupNavigation = () => {
        // Bottom Nav Interactions
        const handleNavClick = (btnId, screenKey) => {
            const btn = dom.buttons[btnId];
            if (btn) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    navigateTo(screenKey);
                });
            }
        };

        // Home (Index 0)
        if (dom.navItems[0]) {
            dom.navItems[0].addEventListener('click', (e) => {
                e.preventDefault();
                navigateTo('home');
            });
        }
        // Desktop Navigation Links
        if (dom.desktopNavLinks) {
            dom.desktopNavLinks.forEach((link) => {
                link.addEventListener('click', (e) => {
                    const screen = link.dataset.screen;
                    const action = link.dataset.action;
                    if (!screen && !action) return;
                    e.preventDefault();
                    if (screen) navigateTo(screen);
                    if (action === 'openProductList') openProductList({});
                });
            });
        }

        handleNavClick('brand', 'brand');
        handleNavClick('qorder', 'qorder');
        handleNavClick('akun', 'akun');
        handleNavClick('info', 'info');

        if (dom.buttons.categoryDesktop) {
            dom.buttons.categoryDesktop.addEventListener('click', (e) => {
                e.preventDefault();
                openSidebar();
            });
        }

        // Header Buttons
        if (dom.buttons.cart) {
            dom.buttons.cart.addEventListener('click', (e) => {
                e.preventDefault();
                renderCart();
                if (dom.screens.cart) dom.screens.cart.style.display = 'flex';
            });
        }
        if (dom.buttons.closeCart && dom.screens.cart) {
            dom.buttons.closeCart.addEventListener('click', () => {
                dom.screens.cart.style.display = 'none';
                // history.back(); // If we added it to history
            });
        }

        if (dom.buttons.notif && dom.screens.notif) {
            dom.buttons.notif.addEventListener('click', () => dom.screens.notif.style.display = 'flex');
        }
        if (dom.buttons.closeNotif && dom.screens.notif) {
            dom.buttons.closeNotif.addEventListener('click', () => dom.screens.notif.style.display = 'none');
        }

        const homeSearch = document.querySelector('[data-home-search]');
        const desktopSearchBtn = document.querySelector('.search-btn');
        const runSearch = () => {
            const q = (homeSearch?.value || '').trim();
            openProductList({ q });
        };
        if (homeSearch) {
            homeSearch.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    runSearch();
                }
            });
        }
        if (desktopSearchBtn) desktopSearchBtn.addEventListener('click', (e) => { e.preventDefault(); runSearch(); });
        
        const searchCategoryBtn = document.getElementById('search-category-btn');
        const categoryDropdown = document.getElementById('navbar-category-dropdown');
        const categoryList = document.querySelector('[data-navbar-categories]');
        const toggleCategoryDropdown = (show) => {
            if (!categoryDropdown) return;
            const isShown = categoryDropdown.style.display !== 'none';
            categoryDropdown.style.display = show === undefined ? (isShown ? 'none' : 'block') : (show ? 'block' : 'none');
        };
        if (searchCategoryBtn) {
            searchCategoryBtn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleCategoryDropdown();
            });
        }
        document.addEventListener('click', (e) => {
            const within = e.target.closest('.search-bar');
            const inDropdown = e.target.closest('#navbar-category-dropdown');
            if (!within && !inDropdown) toggleCategoryDropdown(false);
        });

        document.body.addEventListener('click', (e) => {
            const cat = e.target.closest('[data-category-id]');
            if (cat) {
                const categoryId = Number(cat.dataset.categoryId);
                if (!Number.isFinite(categoryId)) return;
                openProductList({ category_id: categoryId });
                return;
            }

            const brandCard = e.target.closest('[data-brand-id]');
            if (brandCard) {
                const brandId = Number(brandCard.dataset.brandId);
                if (!Number.isFinite(brandId)) return;
                openProductList({ brand_id: brandId });
                return;
            }

            const seeAll = e.target.closest('[data-action="openProductList"]');
            if (seeAll) {
                e.preventDefault();
                openProductList({});
                return;
            }

            const openCartBtn = e.target.closest('[data-action="openCart"]');
            if (openCartBtn) {
                e.preventDefault();
                renderCart();
                if (dom.screens.cart) dom.screens.cart.style.display = 'flex';
                return;
            }

            const openNotifBtn = e.target.closest('[data-action="openNotif"]');
            if (openNotifBtn) {
                e.preventDefault();
                if (dom.screens.notif) dom.screens.notif.style.display = 'flex';
                return;
            }

            const footerLink = e.target.closest('[data-action="openInfo"]');
            if (footerLink) {
                e.preventDefault();
                navigateTo('info');
                return;
            }

            const soon = e.target.closest('[data-action="comingSoon"]');
            if (soon) {
                e.preventDefault();
                showToast('Fitur ini akan segera tersedia', 'error');
            }
        });
    };

    // --- Sidebar Logic ---
    const setupSidebar = () => {
        if (!dom.sidebar) return;
        
        // Create overlay if not exists
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            dom.mainContent.appendChild(overlay);
            
            overlay.addEventListener('click', () => {
                dom.sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        const open = () => {
            dom.sidebar.classList.add('active');
            overlay.classList.add('active');
        };
        const close = () => {
            dom.sidebar.classList.remove('active');
            overlay.classList.remove('active');
        };
        window.openSidebar = open;
        window.closeSidebar = close;

        if (dom.buttons.menu) dom.buttons.menu.addEventListener('click', open);

        if (dom.buttons.closeSidebar) {
            dom.buttons.closeSidebar.addEventListener('click', close);
        }

        dom.sidebar.addEventListener('click', (e) => {
            const item = e.target.closest('[data-category-id]');
            if (!item) return;
            const categoryId = Number(item.dataset.categoryId);
            if (!Number.isFinite(categoryId)) return;
            close();
            openProductList({ category_id: categoryId });
        });
    };

    const openSidebar = () => {
        if (typeof window.openSidebar === 'function') window.openSidebar();
    };

    // --- Cart Logic ---
    const getCart = () => {
        try {
            return JSON.parse(localStorage.getItem('guest_cart_v1')) || { items: [] };
        } catch { return { items: [] }; }
    };

    const setCart = (cart) => {
        localStorage.setItem('guest_cart_v1', JSON.stringify(cart));
        updateCartBadge(cart);
    };

    const updateCartBadge = (cart) => {
        const count = cart.items.reduce((sum, item) => sum + item.quantity, 0);
        dom.cartBadges?.forEach((badge) => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    };

    const addToCart = (product, quantity = 1) => {
        const cart = getCart();
        const existing = cart.items.find(i => i.id === product.id);
        
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.items.push({ ...product, quantity });
        }
        
        setCart(cart);
        showToast('Produk berhasil ditambahkan ke keranjang');
    };

    const renderCart = () => {
        const cart = getCart();
        const container = document.querySelector('#cart-screen .cart-content');
        const template = document.querySelector('#cart-screen [data-cart-item]');
        const empty = document.querySelector('#cart-screen .cart-empty');
        const totalEl = document.querySelector('#cart-screen .total-price');

        if (!container || !template || !empty || !totalEl) {
            updateCartBadge(cart);
            return;
        }

        container.querySelectorAll('[data-cart-row="1"]').forEach((el) => el.remove());

        if (!cart.items.length) {
            empty.style.display = 'block';
            totalEl.textContent = formatRupiah(0);
            updateCartBadge(cart);
            return;
        }

        empty.style.display = 'none';

        let total = 0;
        cart.items.forEach((item) => {
            const row = template.cloneNode(true);
            row.style.display = 'flex';
            row.dataset.cartRow = '1';
            row.dataset.productId = String(item.id);

            const img = row.querySelector('[data-cart-image]');
            const name = row.querySelector('[data-cart-name]');
            const price = row.querySelector('[data-cart-price]');
            const qtyInput = row.querySelector('.qty-input');

            if (img) img.src = normalizeUrl(item.image_path) || img.src;
            if (name) name.textContent = item.name || '-';
            if (price) price.textContent = formatRupiah(item.price_1);
            if (qtyInput) qtyInput.value = String(item.quantity || 1);

            total += Number(item.price_1 || 0) * Number(item.quantity || 0);

            container.appendChild(row);
        });

        totalEl.textContent = formatRupiah(total);
        updateCartBadge(cart);
    };

    const setupCart = () => {
        updateCartBadge(getCart());

        const cartRoot = document.getElementById('cart-screen');
        if (!cartRoot) return;

        cartRoot.addEventListener('click', (e) => {
            const row = e.target.closest('[data-cart-row="1"]');
            if (!row) return;

            const productId = Number(row.dataset.productId);
            if (!Number.isFinite(productId)) return;

            const cart = getCart();
            const item = cart.items.find(i => i.id === productId);
            if (!item) return;

            if (e.target.closest('.trash-btn')) {
                cart.items = cart.items.filter(i => i.id !== productId);
                setCart(cart);
                renderCart();
                return;
            }

            const minus = e.target.closest('.qty-btn.minus');
            const plus = e.target.closest('.qty-btn.plus');
            if (minus) {
                item.quantity = Math.max(1, Number(item.quantity || 1) - 1);
                setCart(cart);
                renderCart();
                return;
            }
            if (plus) {
                item.quantity = Math.min(9999, Number(item.quantity || 1) + 1);
                setCart(cart);
                renderCart();
            }
        });

        const checkoutBtn = cartRoot.querySelector('[data-checkout-btn]');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const cart = getCart();
                if (!cart.items.length) {
                    showToast('Keranjang masih kosong', 'error');
                    return;
                }
                const form = cartRoot.querySelector('[data-checkout-form]');
                if (form) form.style.display = 'block';
                form?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    };

    // --- Checkout ---
    const getOrders = () => {
        try {
            return JSON.parse(localStorage.getItem(state.ordersKey)) || [];
        } catch { return []; }
    };

    const setOrders = (orders) => {
        localStorage.setItem(state.ordersKey, JSON.stringify(orders));
    };

    const renderOrderHistory = () => {
        const container = document.querySelector('[data-order-history]');
        if (!container) return;
        const orders = getOrders();
        container.innerHTML = '';
        if (!orders.length) {
            container.innerHTML = `<div class="empty-state"><p>Belum ada order</p></div>`;
            return;
        }
        orders.slice().reverse().forEach((o) => {
            const el = document.createElement('div');
            el.className = 'order-card';
            el.innerHTML = `
                <div class="order-row">
                    <div class="order-no">${o.order_no}</div>
                    <div class="order-total">${formatRupiah(o.grand_total)}</div>
                </div>
                <div class="order-meta">${o.created_at}</div>
            `;
            container.appendChild(el);
        });
    };

    const setupCheckout = () => {
        const cartRoot = document.getElementById('cart-screen');
        if (!cartRoot) return;

        const form = cartRoot.querySelector('[data-checkout-form]');
        const submit = cartRoot.querySelector('[data-submit-order-btn]');
        if (!form || !submit) return;

        submit.addEventListener('click', async (e) => {
            e.preventDefault();

            const cart = getCart();
            if (!cart.items.length) {
                showToast('Keranjang masih kosong', 'error');
                return;
            }

            const fullName = cartRoot.querySelector('[data-checkout-full-name]')?.value?.trim();
            const email = cartRoot.querySelector('[data-checkout-email]')?.value?.trim();
            const phone = cartRoot.querySelector('[data-checkout-phone]')?.value?.trim();
            const address = cartRoot.querySelector('[data-checkout-address]')?.value?.trim();
            const notes = cartRoot.querySelector('[data-checkout-notes]')?.value?.trim();

            if (!fullName || !email || !phone) {
                showToast('Nama, Email, dan No. HP wajib diisi', 'error');
                return;
            }

            try {
                submit.disabled = true;
                submit.classList.add('is-loading');

                const payload = {
                    customer: { full_name: fullName, email, phone, address: address || null },
                    delivery_to: fullName,
                    delivery_phone: phone,
                    delivery_address: address || null,
                    notes: notes || null,
                    items: cart.items.map((i) => ({
                        product_id: i.id,
                        quantity: i.quantity,
                    })),
                };

                const resp = await fetch(urls.order, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });

                if (!resp.ok) {
                    const text = await resp.text();
                    throw new Error(text || 'Gagal membuat order');
                }

                const data = await resp.json();
                const orders = getOrders();
                orders.push({
                    order_no: data.order_no,
                    grand_total: data.grand_total,
                    created_at: new Date().toLocaleString('id-ID'),
                });
                setOrders(orders);

                setCart({ items: [] });
                renderCart();
                renderOrderHistory();
                form.style.display = 'none';
                showToast(`Order berhasil dibuat: ${data.order_no}`);
                dom.screens.cart.style.display = 'none';
            } catch (err) {
                showToast('Gagal membuat order. Periksa data dan coba lagi.', 'error');
            } finally {
                submit.disabled = false;
                submit.classList.remove('is-loading');
            }
        });
    };

    // --- Product Interaction ---
    const setupProductInteractions = () => {
        // Delegate click for product cards to open detail
        document.body.addEventListener('click', (e) => {
            const card = e.target.closest('.product-card');
            if (card) {
                const productId = card.dataset.productId;
                if (productId) openProductDetail(Number(productId));
            }
        });

        // Close Detail
        const closeDetail = document.getElementById('close-product-detail');
        if (closeDetail && dom.screens.productDetail) {
            closeDetail.addEventListener('click', () => {
                history.back();
            });
        }

        const addDetailToCartBtn = document.querySelector('[data-detail-add-to-cart]');
        if (addDetailToCartBtn) {
            addDetailToCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!state.currentProduct) {
                    showToast('Produk belum siap', 'error');
                    return;
                }
                addToCart({
                    id: state.currentProduct.id,
                    name: state.currentProduct.name,
                    price_1: state.currentProduct.price_1,
                    image_path: state.currentProduct.image_path || state.currentProduct.photo_path,
                    brand: state.currentProduct.brand,
                }, 1);
            });
        }
    };

    const fetchProducts = async (filters) => {
        const url = `${urls.productsIndex}${buildQuery({
            q: filters.q || '',
            category_id: filters.category_id || null,
            brand_id: filters.brand_id || null,
            per_page: 20,
        })}`;
        const resp = await fetch(url);
        if (!resp.ok) throw new Error('Gagal memuat produk');
        return resp.json();
    };

    const renderProductGrid = (products) => {
        const grid = document.querySelector('[data-product-grid]');
        if (!grid) return;
        grid.innerHTML = '';

        if (!products.length) {
            grid.innerHTML = `<div class="empty-state"><p>Produk tidak ditemukan</p></div>`;
            return;
        }

        products.forEach((p) => {
            const card = document.createElement('div');
            card.className = 'product-card grid-item';
            card.dataset.productId = String(p.id);
            card.innerHTML = `
                <div class="prod-img-box">
                    <img src="${normalizeUrl(p.image_path) || 'https://placehold.co/150x150/white/black?text=Product'}" alt="Product">
                </div>
                <div class="prod-info">
                    <p class="prod-brand">${p.brand || ''}</p>
                    <p class="prod-name">${p.name || '-'}</p>
                    <p class="prod-price">${formatRupiah(p.price_1)}</p>
                </div>
            `;
            grid.appendChild(card);
        });
    };

    const applySort = (products, sort) => {
        const list = products.slice();
        if (sort === 'termurah') list.sort((a, b) => (a.price_1 || 0) - (b.price_1 || 0));
        if (sort === 'termahal') list.sort((a, b) => (b.price_1 || 0) - (a.price_1 || 0));
        return list;
    };

    const openProductList = async (overrides) => {
        state.productList = {
            ...state.productList,
            ...overrides,
        };
        navigateTo('productList');

        const input = document.querySelector('[data-product-search]');
        if (input) input.value = state.productList.q || '';

        try {
            const data = await fetchProducts(state.productList);
            const products = (data?.data || []).map((p) => ({
                ...p,
                price_1: Number(p.price_1 || 0),
            }));
            state.productsLast = products;
            renderProductGrid(applySort(products, state.productList.sort));
        } catch (err) {
            renderProductGrid([]);
            showToast('Gagal memuat produk', 'error');
        }
    };

    const fetchProductDetail = async (productId) => {
        const resp = await fetch(`${urls.productShowBase}/${productId}`);
        if (!resp.ok) throw new Error('Gagal memuat detail produk');
        return resp.json();
    };

    const renderProductDetail = (p) => {
        const img = document.querySelector('[data-detail-image]');
        const title = document.querySelector('[data-detail-title]');
        const price = document.querySelector('[data-detail-price]');
        const desc = document.querySelector('[data-detail-description]');
        const brand = document.querySelector('[data-detail-brand]');
        const weight = document.querySelector('[data-detail-weight]');

        if (img) img.src = normalizeUrl(p.photo_path) || normalizeUrl(p.images?.[0]?.image_path) || img.src;
        if (title) title.textContent = p.name || '-';
        if (price) price.textContent = formatRupiah(p.price_tiers?.[0]?.price ?? p.price_1 ?? 0);
        if (desc) desc.textContent = (p.description || '').trim() || '-';
        if (brand) brand.textContent = p.brand || '-';
        if (weight) weight.textContent = p.weight_kg ? `${p.weight_kg} kg` : '-';
    };

    const openProductDetail = async (productId) => {
        if (!Number.isFinite(productId)) return;
        navigateTo('productDetail');
        state.currentProduct = null;
        try {
            const p = await fetchProductDetail(productId);
            const primaryImage = p.photo_path || p.images?.[0]?.image_path;
            state.currentProduct = {
                ...p,
                price_1: Number(p.price_tiers?.[0]?.price ?? 0),
                image_path: primaryImage,
            };
            renderProductDetail(state.currentProduct);
        } catch (err) {
            showToast('Gagal memuat detail produk', 'error');
            history.back();
        }
    };

    const setupProductListControls = () => {
        const closeBtn = document.getElementById('close-product-list');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                history.back();
            });
        }

        const search = document.querySelector('[data-product-search]');
        if (search) {
            search.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    openProductList({ q: search.value.trim() });
                }
            });
        }

        const sort = document.getElementById('sort-select');
        if (sort) {
            sort.addEventListener('change', () => {
                state.productList.sort = sort.value;
                renderProductGrid(applySort(state.productsLast, state.productList.sort));
            });
        }
    };

    // --- QOrder Logic ---
    const setupQOrder = () => {
        // QOrder specific interactions
        const qorderScreen = document.getElementById('qorder-screen');
        if (!qorderScreen) return;

        // Search functionality
        const searchInput = qorderScreen.querySelector('[data-qorder-search]');
        const clearBtn = qorderScreen.querySelector('.qorder-clear-search');
        
        if (searchInput) {
            const filterProducts = () => {
                const term = searchInput.value.toLowerCase().trim();
                const cards = qorderScreen.querySelectorAll('.qorder-card');
                cards.forEach(card => {
                    const title = card.querySelector('.qorder-title')?.textContent?.toLowerCase() || '';
                    if (title.includes(term)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            };

            searchInput.addEventListener('input', filterProducts);

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    filterProducts();
                });
            }
        }

        qorderScreen.addEventListener('click', (e) => {
            // Handle plus/minus buttons
            const btn = e.target.closest('.qty-btn');
            if (btn) {
                const input = btn.parentElement.querySelector('input');
                let val = parseInt(input.value) || 0;
                if (btn.classList.contains('plus')) val++;
                else if (btn.classList.contains('minus') && val > 0) val--;
                input.value = val;
            }
        });

        const addBtn = document.querySelector('[data-add-to-cart-btn]');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const cards = qorderScreen.querySelectorAll('.qorder-card');
                let added = 0;
                cards.forEach((card) => {
                    const checked = card.querySelector('[data-qorder-check]')?.checked;
                    if (!checked) return;
                    const productId = Number(card.dataset.productId);
                    const qty = Number(card.querySelector('[data-qorder-qty]')?.value || 1);
                    if (!Number.isFinite(productId) || !Number.isFinite(qty) || qty < 1) return;

                    const name = card.querySelector('.qorder-title')?.textContent?.trim() || '-';
                    const priceText = card.querySelector('.tier.active span:last-child')?.textContent || '';
                    const price = Number(String(priceText).replace(/[^\d]/g, '')) || 0;
                    const img = card.querySelector('img')?.getAttribute('src') || null;

                    addToCart({ id: productId, name, price_1: price, image_path: img }, qty);
                    added += 1;
                    card.querySelector('[data-qorder-check]').checked = false;
                });
                if (!added) showToast('Pilih produk dulu', 'error');
            });
        }
    };

    const setupAkunTabs = () => {
        const screen = document.getElementById('akun-screen');
        if (!screen) return;
        const tabs = screen.querySelectorAll('.akun-tab');
        const profil = document.getElementById('akun-profil-content');
        const order = document.getElementById('akun-order-content');

        const setTab = (key) => {
            tabs.forEach((t) => t.classList.toggle('active', t.dataset.tab === key));
            if (profil) profil.style.display = key === 'profil' ? 'block' : 'none';
            if (order) order.style.display = key === 'order' ? 'block' : 'none';
            if (key === 'order') renderOrderHistory();
        };

        tabs.forEach((t) => {
            t.addEventListener('click', () => setTab(t.dataset.tab));
        });

        setTab('profil');
    };

    // Initialize
    init();
    setupQOrder();
    setupAkunTabs();
    
    const initialHash = window.location.hash.replace('#', '');
    const initialScreen = document.body.dataset.guestInitialScreen || 'home';
    const initialProductId = Number(document.body.dataset.guestInitialProductId || '');

    const startScreen = (initialHash && (dom.screens[initialHash] || initialHash === 'home')) ? initialHash : initialScreen;
    updateView(startScreen);
    history.replaceState({ screen: startScreen }, '', `#${startScreen}`);

    if (startScreen === 'productDetail' && Number.isFinite(initialProductId)) {
        openProductDetail(initialProductId);
    }

    updateCartBadge(getCart());
});
