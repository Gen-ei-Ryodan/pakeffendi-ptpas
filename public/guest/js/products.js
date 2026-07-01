/**
 * PAS Market Product Interactions
 * Enhanced product card clicks, cart functionality, and user interactions
 */

(function() {
    'use strict';

    const PAS = window.PAS = window.PAS || {};
    const baseCart = PAS.Cart;

    const notify = (message, type = 'info') => {
        if (PAS.Notification && typeof PAS.Notification.show === 'function') {
            return PAS.Notification.show(message, type);
        }
        if (PAS.Cart && typeof PAS.Cart.showNotification === 'function') {
            const map = { error: 'danger', success: 'success', warning: 'warning', info: 'info' };
            return PAS.Cart.showNotification(message, map[type] || 'info');
        }
    };

    const ProductInteractions = {
        init() {
            this.setupProductCardClicks();
            this.setupAddToCartButtons();
            this.setupWishlistButtons();
            this.setupImageGallery();
            this.setupQuantityControls();
            this.setupSocialMediaButtons();
            console.log('✅ Product Interactions Initialized');
        },
        
        setupProductCardClicks() {
            // Handle clicks on product cards
            document.addEventListener('click', (e) => {
                const productCard = e.target.closest('.product-card');
                if (productCard && !e.target.closest('.btn, .qty-btn, .qty-input')) {
                    e.preventDefault();
                    const productId = productCard.dataset.productId;
                    if (productId) {
                        this.navigateToProductDetail(productId);
                    }
                }
            });
        },
        
        setupAddToCartButtons() {
            // Handle Add to Cart button clicks
            document.addEventListener('click', (e) => {
                const addToCartBtn = e.target.closest('.btn-add-to-cart');
                if (addToCartBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleAddToCart(addToCartBtn);
                }
            });
        },
        
        setupWishlistButtons() {
            // Handle Wishlist button clicks
            document.addEventListener('click', (e) => {
                const wishlistBtn = e.target.closest('.btn-wishlist, [data-action="wishlist"]');
                if (wishlistBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleWishlist(wishlistBtn);
                }
            });
        },
        
        setupImageGallery() {
            // Handle product image gallery clicks
            document.addEventListener('click', (e) => {
                const thumbnail = e.target.closest('.img-thumbnail');
                if (thumbnail && thumbnail.closest('.product-gallery')) {
                    e.preventDefault();
                    this.handleImageGallery(thumbnail);
                }
            });
        },
        
        setupQuantityControls() {
            // Handle quantity control buttons
            document.addEventListener('click', (e) => {
                const qtyBtn = e.target.closest('.btn-quantity');
                if (qtyBtn) {
                    e.preventDefault();
                    this.handleQuantityChange(qtyBtn);
                }
            });
            
            // Handle quantity input changes
            document.addEventListener('change', (e) => {
                const qtyInput = e.target.closest('input[type="number"]');
                if (qtyInput && qtyInput.closest('.quantity-control')) {
                    this.handleQuantityInput(qtyInput);
                }
            });
        },
        
        setupSocialMediaButtons() {
            // Handle social media button clicks
            document.addEventListener('click', (e) => {
                const socialBtn = e.target.closest('.social-share, .social-links a');
                if (socialBtn) {
                    e.preventDefault();
                    this.handleSocialShare(socialBtn);
                }
            });
        },
        
        navigateToProductDetail(productId) {
            const path = `/products/${productId}`;
            if (PAS.Router && typeof PAS.Router.navigate === 'function' && document.body?.dataset?.spa === 'true') {
                notify('Memuat detail produk...', 'info');
                this.loadProductDetailData(productId);
                PAS.Router.navigate(path);
            } else {
                window.location.href = path;
            }
        },
        
        async loadProductDetailData(productId) {
            try {
                const response = await fetch(`/api/guest/products/${productId}`);
                if (!response.ok) throw new Error('Product not found');
                const product = await response.json();
                this.updateProductDetailPage(product);
            } catch (e) {
                console.error('Failed to load product detail:', e);
                // Fallback: let the page load normally via navigation
                if (PAS.Router && typeof PAS.Router.navigate === 'function') {
                    window.location.href = `/products/${productId}`;
                }
            }
        },
        
        updateProductDetailPage(product) {
            // Update product detail page with loaded data from API
            const placeholderImg = '/guest/img/placeholder-product.svg';
            
            const titleElement = document.querySelector('h1, .product-title');
            if (titleElement) {
                titleElement.textContent = product.name || product.title || '-';
            }
            
            const priceElement = document.querySelector('.product-price, .price');
            if (priceElement) {
                const rawPrice = product.price_tiers?.[0]?.price ?? product.price_1 ?? product.price ?? 0;
                priceElement.textContent = `Rp ${Number(rawPrice).toLocaleString('id-ID')}`;
            }
            
            // Helper: get valid photo URL or placeholder
            const getPhotoUrl = (path) => {
                if (!path) return placeholderImg;
                if (path.startsWith('http')) return path;
                return `/storage/${path}`;
            };
            
            // Main product image
            const imageElement = document.querySelector('#mainProductImage, .product-main-image, [data-detail-image]');
            if (imageElement) {
                imageElement.src = getPhotoUrl(product.photo_path);
                imageElement.alt = product.name || 'Product';
            }
            
            // Detail screen image (mobile SPA)
            const detailImage = document.querySelector('[data-detail-image]');
            if (detailImage) {
                detailImage.src = getPhotoUrl(product.photo_path);
            }
            
            const descElement = document.querySelector('[data-detail-description], .product-description');
            if (descElement && product.description) {
                descElement.textContent = product.description;
            }
            
            const brandElement = document.querySelector('[data-detail-brand]');
            if (brandElement && product.brand) {
                brandElement.textContent = product.brand;
            }
            
            const weightElement = document.querySelector('[data-detail-weight]');
            if (weightElement && product.weight_kg) {
                weightElement.textContent = `${Number(product.weight_kg).toLocaleString('id-ID')} kg`;
            }
        },
        
        handleAddToCart(button) {
            const productCard =
                button.closest('.product-card') ||
                button.closest('.card[data-product-id]') ||
                button.parentElement?.closest('[data-product-id]');
            const productId = button.dataset.productId || productCard?.dataset.productId;
            
            if (!productId) {
                notify('Produk tidak valid', 'error');
                return;
            }
            
            let quantity = 1;
            const qtyInput =
                productCard?.querySelector('.quantity-control input[type="number"]') ||
                button.closest('section')?.querySelector('#quantity');
            if (qtyInput) {
                const raw = parseInt(qtyInput.value, 10);
                const min = parseInt(qtyInput.min, 10);
                const max = parseInt(qtyInput.max, 10);
                const safeMin = Number.isFinite(min) ? min : 1;
                const safeMax = Number.isFinite(max) ? max : 9999;
                quantity = Number.isFinite(raw) ? Math.max(safeMin, Math.min(safeMax, raw)) : safeMin;
                qtyInput.value = quantity;
            }

            // Get product data from DOM
            const productName = productCard?.querySelector('.product-title, .product-name')?.textContent || 'Produk';
            const productPrice = this.extractPrice(productCard);
            const productImage = productCard?.querySelector('.product-image, img')?.src || '';
            
            // Add to cart animation
            this.animateAddToCart(button, productCard);
            
            // Add to cart logic
            const product = {
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity
            };
            
            if (PAS.Cart && typeof PAS.Cart.addItem === 'function') {
                PAS.Cart.addItem(product);
            }
            
            // Update button state temporarily
            this.updateButtonState(button, 'added');
        },
        
        animateAddToCart(button, productCard) {
            // Create flying animation to cart
            const productImage = productCard?.querySelector('.product-image, img');
            if (productImage) {
                const rect = productImage.getBoundingClientRect();
                const cartButton = document.getElementById('cartBtn') || document.querySelector('[href="#cart"]');
                
                if (cartButton) {
                    const cartRect = cartButton.getBoundingClientRect();
                    
                    // Create flying element
                    const flyingElement = document.createElement('div');
                    flyingElement.style.cssText = `
                        position: fixed;
                        width: 50px;
                        height: 50px;
                        background: url(${productImage.src}) center/cover;
                        border-radius: 8px;
                        z-index: 9999;
                        pointer-events: none;
                        left: ${rect.left}px;
                        top: ${rect.top}px;
                        transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                    `;
                    
                    document.body.appendChild(flyingElement);
                    
                    // Animate to cart
                    setTimeout(() => {
                        flyingElement.style.left = `${cartRect.left + cartRect.width/2 - 25}px`;
                        flyingElement.style.top = `${cartRect.top + cartRect.height/2 - 25}px`;
                        flyingElement.style.transform = 'scale(0.3)';
                        flyingElement.style.opacity = '0.5';
                    }, 100);
                    
                    // Remove element
                    setTimeout(() => {
                        flyingElement.remove();
                    }, 900);
                }
            }
        },
        
        updateButtonState(button, state) {
            const originalHTML = button.innerHTML;
            
            if (state === 'added') {
                button.innerHTML = '<i class="bi bi-check-circle"></i> Ditambahkan!';
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
                button.disabled = true;
                
                // Reset after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                    button.disabled = false;
                }, 2000);
            }
        },
        
        handleWishlist(button) {
            const productCard = button.closest('.product-card') || button.closest('[data-product-id]');
            const productId = button.dataset.productId || productCard?.dataset.productId;
            
            if (!productId) {
                notify('Produk tidak valid', 'error');
                return;
            }
            
            // Toggle wishlist state
            const isWishlisted = button.classList.contains('active') || button.dataset.wishlisted === 'true';
            
            if (isWishlisted) {
                this.removeFromWishlist(button, productId);
            } else {
                this.addToWishlist(button, productId);
            }
        },
        
        addToWishlist(button, productId) {
            // Add to wishlist animation
            button.classList.add('active');
            button.dataset.wishlisted = 'true';
            
            // Update icon
            const icon = button.querySelector('i') || button;
            if (icon.classList.contains('bi-heart')) {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
            }
            
            notify('Ditambahkan ke wishlist!', 'success');
            
            // Save to localStorage
            this.saveWishlistItem(productId);
        },
        
        removeFromWishlist(button, productId) {
            // Remove from wishlist animation
            button.classList.remove('active');
            button.dataset.wishlisted = 'false';
            
            // Update icon
            const icon = button.querySelector('i') || button;
            if (icon.classList.contains('bi-heart-fill')) {
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart');
            }
            
            notify('Dihapus dari wishlist', 'info');
            
            // Remove from localStorage
            this.removeWishlistItem(productId);
        },
        
        handleImageGallery(thumbnail) {
            const mainImage = document.querySelector('#mainProductImage, .product-main-image');
            if (mainImage) {
                // Update main image
                mainImage.src = thumbnail.src;
                mainImage.alt = thumbnail.alt;
                
                // Update active state
                document.querySelectorAll('.img-thumbnail').forEach(img => {
                    img.classList.remove('border-primary', 'active');
                });
                thumbnail.classList.add('border-primary', 'active');
            }
        },
        
        handleQuantityChange(button) {
            const isIncrease = button.dataset.action === 'increase' || button.textContent.includes('+');
            const quantityControl = button.closest('.quantity-control');
            const input = quantityControl?.querySelector('input[type="number"]');
            
            if (input) {
                const currentValue = parseInt(input.value) || 1;
                const min = parseInt(input.min) || 1;
                const max = parseInt(input.max) || 999;
                
                let newValue = isIncrease ? currentValue + 1 : currentValue - 1;
                
                // Validate bounds
                if (newValue < min) newValue = min;
                if (newValue > max) newValue = max;
                
                input.value = newValue;
                
                // Update cart if in cart context
                if (quantityControl.closest('.cart-item')) {
                    this.updateCartItemQuantity(input);
                }
            }
        },
        
        handleQuantityInput(input) {
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || 999;
            let value = parseInt(input.value) || min;
            
            // Validate bounds
            if (value < min) value = min;
            if (value > max) value = max;
            
            input.value = value;
            
            // Update cart if in cart context
            if (input.closest('.cart-item')) {
                this.updateCartItemQuantity(input);
            }
        },
        
        updateCartItemQuantity(input) {
            const cartItem = input.closest('.cart-item');
            const itemId = cartItem?.dataset.itemId;
            const newQuantity = parseInt(input.value);
            
            if (itemId) {
                // Update cart logic
                PAS.Cart.updateQuantity(itemId, newQuantity);
                
                // Update price display
                const priceElement = cartItem.querySelector('.text-primary');
                const unitPrice = this.extractPrice(cartItem);
                const newTotal = unitPrice * newQuantity;
                
                if (priceElement) {
                    priceElement.textContent = `Rp ${newTotal.toLocaleString('id-ID')}`;
                }
            }
        },
        
        handleSocialShare(button) {
            const platform = button.dataset.platform || button.classList.contains('bi-facebook') ? 'facebook' : 
                             button.classList.contains('bi-twitter') ? 'twitter' : 
                             button.classList.contains('bi-whatsapp') ? 'whatsapp' : 
                             button.classList.contains('bi-telegram') ? 'telegram' : 'copy';
            
            const url = window.location.href;
            const text = document.title;
            
            switch(platform) {
                case 'facebook':
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'twitter':
                    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'whatsapp':
                    window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
                    break;
                case 'telegram':
                    window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
                    break;
                case 'copy':
                    navigator.clipboard.writeText(url);
                    notify('Link disalin ke clipboard!', 'success');
                    break;
            }
        },
        
        extractPrice(element) {
            const priceElement = element?.querySelector('.product-price, .price, .text-primary');
            if (priceElement) {
                const priceText = priceElement.textContent;
                const priceMatch = priceText.match(/[\d,]+/);
                if (priceMatch) {
                    return parseInt(priceMatch[0].replace(/,/g, ''));
                }
            }
            return 0;
        },
        
        saveWishlistItem(productId) {
            let wishlist = JSON.parse(localStorage.getItem('pas_wishlist') || '[]');
            if (!wishlist.includes(productId)) {
                wishlist.push(productId);
                localStorage.setItem('pas_wishlist', JSON.stringify(wishlist));
            }
        },
        
        removeWishlistItem(productId) {
            let wishlist = JSON.parse(localStorage.getItem('pas_wishlist') || '[]');
            wishlist = wishlist.filter(id => id !== productId);
            localStorage.setItem('pas_wishlist', JSON.stringify(wishlist));
        },
        
        loadWishlistState() {
            const wishlist = JSON.parse(localStorage.getItem('pas_wishlist') || '[]');
            
            document.querySelectorAll('.btn-wishlist, [data-action="wishlist"]').forEach(button => {
                const productId = button.dataset.productId || button.closest('[data-product-id]')?.dataset.productId;
                
                if (productId && wishlist.includes(productId)) {
                    button.classList.add('active');
                    button.dataset.wishlisted = 'true';
                    
                    const icon = button.querySelector('i') || button;
                    if (icon.classList.contains('bi-heart')) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    }
                }
            });
        }
    };

    // Enhanced Cart Management
    const EnhancedCart = {
        ...(baseCart || {}),
        
        init() {
            if (baseCart && typeof baseCart.init === 'function') {
                baseCart.init.call(baseCart);
            }
            
            this.setupCartEvents();
            console.log('✅ Enhanced Cart Initialized');
        },
        
        setupCartEvents() {
            // Setup cart-specific events
            document.addEventListener('click', (e) => {
                // Cart button in navbar
                const cartBtn = e.target.closest('#cartBtn, [href="#cart"]');
                if (cartBtn) {
                    e.preventDefault();
                    if (PAS.Router && typeof PAS.Router.goToCart === 'function' && document.body?.dataset?.spa === 'true') {
                        PAS.Router.goToCart();
                    } else {
                        const href = cartBtn.getAttribute('href');
                        if (href && href !== '#cart') {
                            window.location.href = href;
                            return;
                        }
                        if (!window.PAS?.auth?.loggedIn) {
                            window.location.href = `${window.PAS?.auth?.loginUrl || '/login'}?redirect=${encodeURIComponent('/cart')}`;
                            return;
                        }
                        window.location.href = '/cart';
                    }
                }
                
                // Remove from cart
                const removeBtn = e.target.closest('.btn-remove-cart');
                if (removeBtn) {
                    e.preventDefault();
                    const itemId = removeBtn.dataset.itemId;
                    if (itemId) {
                        this.removeItem(itemId);
                    }
                }
            });
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        ProductInteractions.init();
        EnhancedCart.init();
        ProductInteractions.loadWishlistState();
    });

    // Expose to global scope
    PAS.ProductInteractions = ProductInteractions;
    PAS.Cart = EnhancedCart;

})();
