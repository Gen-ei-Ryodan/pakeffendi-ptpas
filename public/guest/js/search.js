/**
 * PAS Market Search & Navigation
 * Enhanced search functionality and navigation
 */

(function() {
    'use strict';

    const SearchNavigation = {
        searchResults: [],
        isSearching: false,
        searchTimeout: null,
        
        init() {
            this.setupSearchFunctionality();
            this.setupNavigationEnhancements();
            this.setupMobileMenu();
            console.log('✅ Search & Navigation Initialized');
        },
        
        setupSearchFunctionality() {
            // Setup search inputs
            const searchInputs = document.querySelectorAll('#searchInput, #searchInputMobile');
            const searchButtons = document.querySelectorAll('#searchBtn, #searchBtnMobile');
            
            searchInputs.forEach(input => {
                // Handle input with debouncing
                input.addEventListener('input', (e) => {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.performSearch(e.target.value, input);
                    }, 300);
                });
                
                // Handle Enter key
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.performSearch(e.target.value, input);
                    }
                });
            });
            
            // Setup search buttons
            searchButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const relatedInput = button.previousElementSibling || 
                                        button.parentElement.querySelector('input[type="text"]');
                    if (relatedInput && relatedInput.value.trim()) {
                        this.performSearch(relatedInput.value, relatedInput);
                    }
                });
            });
            
            // Setup search suggestions
            this.setupSearchSuggestions();
        },
        
        setupSearchSuggestions() {
            // Mock search suggestions
            const suggestions = [
                'Samsung Galaxy',
                'iPhone',
                'Laptop Gaming',
                'Headphone Wireless',
                'Smart TV',
                'Kamera Digital',
                'Mouse Gaming',
                'Keyboard Mechanical',
                'Power Bank',
                'Charger'
            ];
            
            const searchInputs = document.querySelectorAll('#searchInput, #searchInputMobile');
            
            searchInputs.forEach(input => {
                // Create suggestions dropdown
                const suggestionsContainer = document.createElement('div');
                suggestionsContainer.className = 'search-suggestions';
                suggestionsContainer.style.cssText = `
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    background: white;
                    border: 1px solid #dee2e6;
                    border-top: none;
                    border-radius: 0 0 8px 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    z-index: 1000;
                    display: none;
                    max-height: 300px;
                    overflow-y: auto;
                `;
                
                input.parentElement.style.position = 'relative';
                input.parentElement.appendChild(suggestionsContainer);
                
                // Show suggestions on focus
                input.addEventListener('focus', () => {
                    if (input.value.trim() === '') {
                        this.showSearchSuggestions(input, suggestions, suggestionsContainer);
                    }
                });
                
                // Hide suggestions on blur (with delay to allow click)
                input.addEventListener('blur', () => {
                    setTimeout(() => {
                        suggestionsContainer.style.display = 'none';
                    }, 200);
                });
                
                // Update suggestions on input
                input.addEventListener('input', () => {
                    this.updateSearchSuggestions(input, suggestions, suggestionsContainer);
                });
            });
        },
        
        showSearchSuggestions(input, suggestions, container) {
            container.innerHTML = '';
            
            suggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'search-suggestion-item';
                item.style.cssText = `
                    padding: 12px 16px;
                    cursor: pointer;
                    border-bottom: 1px solid #f8f9fa;
                    transition: background-color 0.2s;
                `;
                item.textContent = suggestion;
                
                item.addEventListener('click', () => {
                    input.value = suggestion;
                    container.style.display = 'none';
                    this.performSearch(suggestion, input);
                });
                
                item.addEventListener('mouseenter', () => {
                    item.style.backgroundColor = '#f8f9fa';
                });
                
                item.addEventListener('mouseleave', () => {
                    item.style.backgroundColor = 'transparent';
                });
                
                container.appendChild(item);
            });
            
            container.style.display = 'block';
        },
        
        updateSearchSuggestions(input, suggestions, container) {
            const query = input.value.toLowerCase().trim();
            
            if (query === '') {
                this.showSearchSuggestions(input, suggestions, container);
                return;
            }
            
            const filtered = suggestions.filter(s => 
                s.toLowerCase().includes(query)
            );
            
            if (filtered.length === 0) {
                container.style.display = 'none';
                return;
            }
            
            container.innerHTML = '';
            
            filtered.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'search-suggestion-item';
                item.style.cssText = `
                    padding: 12px 16px;
                    cursor: pointer;
                    border-bottom: 1px solid #f8f9fa;
                    transition: background-color 0.2s;
                `;
                
                // Highlight matching text
                const regex = new RegExp(`(${query})`, 'gi');
                item.innerHTML = suggestion.replace(regex, '<strong style="color: var(--primary-color);">$1</strong>');
                
                item.addEventListener('click', () => {
                    input.value = suggestion;
                    container.style.display = 'none';
                    this.performSearch(suggestion, input);
                });
                
                item.addEventListener('mouseenter', () => {
                    item.style.backgroundColor = '#f8f9fa';
                });
                
                item.addEventListener('mouseleave', () => {
                    item.style.backgroundColor = 'transparent';
                });
                
                container.appendChild(item);
            });
            
            container.style.display = 'block';
        },
        
        performSearch(query, inputElement) {
            if (!query.trim()) {
                return;
            }
            
            // Hide suggestions
            const suggestionsContainer = inputElement.parentElement.querySelector('.search-suggestions');
            if (suggestionsContainer) {
                suggestionsContainer.style.display = 'none';
            }
            
            window.location.href = `/products?q=${encodeURIComponent(query)}`;
        },
        
        generateMockResults(query) {
            // Generate mock search results
            const products = [
                { id: 1, name: 'Samsung Galaxy S24 Ultra', price: 18500000, image: 'https://via.placeholder.com/200x200/f8f9fa/333333?text=S24' },
                { id: 2, name: 'iPhone 15 Pro Max', price: 25000000, image: 'https://via.placeholder.com/200x200/f8f9fa/333333?text=iPhone' },
                { id: 3, name: 'Laptop Gaming ASUS ROG', price: 15000000, image: 'https://via.placeholder.com/200x200/f8f9fa/333333?text=ROG' },
                { id: 4, name: 'Headphone Sony WH-1000XM5', price: 4500000, image: 'https://via.placeholder.com/200x200/f8f9fa/333333?text=Sony' },
                { id: 5, name: 'Smart TV LG OLED 55 inch', price: 18000000, image: 'https://via.placeholder.com/200x200/f8f9fa/333333?text=TV' }
            ];
            
            return products.filter(product => 
                product.name.toLowerCase().includes(query.toLowerCase())
            );
        },
        
        displaySearchResults(results, query) {
            // Store results for use in products page
            sessionStorage.setItem('searchResults', JSON.stringify(results));
            sessionStorage.setItem('searchQuery', query);
        },
        
        showSearchLoading(inputElement) {
            // Add loading state to search input
            inputElement.classList.add('is-loading');
            
            // Create loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'search-loading';
            loadingIndicator.style.cssText = `
                position: absolute;
                right: 50px;
                top: 50%;
                transform: translateY(-50%);
                width: 20px;
                height: 20px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid var(--primary-color);
                border-radius: 50%;
                animation: spin 1s linear infinite;
            `;
            
            inputElement.parentElement.appendChild(loadingIndicator);
        },
        
        hideSearchLoading(inputElement) {
            inputElement.classList.remove('is-loading');
            
            // Remove loading indicator
            const loadingIndicator = inputElement.parentElement.querySelector('.search-loading');
            if (loadingIndicator) {
                loadingIndicator.remove();
            }
        },
        
        setupNavigationEnhancements() {
            // Enhance navbar functionality
            this.setupNavbarLinks();
            this.setupDropdownMenus();
            this.setupMegaMenus();
        },
        
        setupNavbarLinks() {
            // Setup enhanced navbar link functionality
            const navbarLinks = document.querySelectorAll('.navbar-nav .nav-link, .navbar-nav a');
            
            navbarLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    if (document.body?.dataset?.spa !== 'true') {
                        return;
                    }
                    // Special handling for certain links
                    const href = link.getAttribute('href');
                    
                    if (href === '#home' || href === '/') {
                        e.preventDefault();
                        PAS.Router.goToHome();
                    } else if (href === '#products' || href === '/products') {
                        e.preventDefault();
                        PAS.Router.goToProducts();
                    } else if (href === '#cart' || href === '/cart') {
                        e.preventDefault();
                        PAS.Router.goToCart();
                    } else if (href === '#profile' || href === '/profile') {
                        e.preventDefault();
                        PAS.Router.goToProfile();
                    } else if (href === '#orders' || href === '/orders') {
                        e.preventDefault();
                        PAS.Router.goToOrders();
                    } else if (href === '#login' || href === '/login') {
                        e.preventDefault();
                        PAS.Router.goToLogin();
                    } else if (href === '#register' || href === '/register') {
                        e.preventDefault();
                        PAS.Router.goToRegister();
                    } else if (href === '#about' || href === '/about') {
                        e.preventDefault();
                        PAS.Router.goToAbout();
                    } else if (href === '#contact' || href === '/contact') {
                        e.preventDefault();
                        PAS.Router.goToContact();
                    }
                });
            });
        },
        
        setupDropdownMenus() {
            // Enhanced dropdown functionality
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    const dropdownMenu = dropdown.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                        const isOpen = dropdownMenu.classList.contains('show');
                        
                        // Close all dropdowns
                        document.querySelectorAll('.dropdown-menu').forEach(menu => {
                            menu.classList.remove('show');
                        });
                        
                        // Toggle current dropdown
                        if (!isOpen) {
                            dropdownMenu.classList.add('show');
                        }
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        },
        
        setupMegaMenus() {
            // Setup mega menu functionality for desktop
            const megaMenuTriggers = document.querySelectorAll('[data-mega-menu]');
            
            megaMenuTriggers.forEach(trigger => {
                const targetId = trigger.dataset.megaMenu;
                const megaMenu = document.getElementById(targetId);
                
                if (megaMenu) {
                    trigger.addEventListener('mouseenter', () => {
                        if (window.innerWidth >= 992) { // Desktop only
                            megaMenu.style.display = 'block';
                        }
                    });
                    
                    trigger.addEventListener('mouseleave', () => {
                        if (window.innerWidth >= 992) { // Desktop only
                            setTimeout(() => {
                                if (!megaMenu.matches(':hover')) {
                                    megaMenu.style.display = 'none';
                                }
                            }, 100);
                        }
                    });
                    
                    megaMenu.addEventListener('mouseleave', () => {
                        megaMenu.style.display = 'none';
                    });
                }
            });
        },
        
        setupMobileMenu() {
            // Enhanced mobile menu functionality
            const mobileMenuToggle = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            
            if (mobileMenuToggle && navbarCollapse) {
                mobileMenuToggle.addEventListener('click', () => {
                    const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
                    mobileMenuToggle.setAttribute('aria-expanded', !isExpanded);
                    navbarCollapse.classList.toggle('show');
                });
                
                // Close mobile menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.navbar') && navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                        mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Close mobile menu when clicking on links
                navbarCollapse.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        navbarCollapse.classList.remove('show');
                        mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    });
                });
            }
        }
    };

    // Enhanced Notification System
    const EnhancedNotification = {
        notifications: [],
        
        show(message, type = 'info', duration = 3000) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 16px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: translateX(400px);
                transition: transform 0.3s ease;
                max-width: 350px;
                word-wrap: break-word;
            `;
            
            // Set background color based on type
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };
            notification.style.backgroundColor = colors[type] || colors.info;
            
            // Add icon
            const icons = {
                success: '✓',
                error: '✗',
                warning: '⚠',
                info: 'ℹ'
            };
            
            notification.innerHTML = `
                <div style="display: flex; align-items: center;">
                    <span style="margin-right: 12px; font-size: 18px;">${icons[type] || icons.info}</span>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            style="margin-left: auto; background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
                        ×
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto remove
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
            
            this.notifications.push(notification);
        },
        
        removeNotification(notification) {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
                this.notifications = this.notifications.filter(n => n !== notification);
            }, 300);
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        SearchNavigation.init();
        
        // Override existing notification system
        if (window.PAS && window.PAS.Notification) {
            window.PAS.Notification = EnhancedNotification;
        } else {
            window.PAS = window.PAS || {};
            window.PAS.Notification = EnhancedNotification;
        }
    });

    // Expose to global scope
    window.PAS = window.PAS || {};
    window.PAS.SearchNavigation = SearchNavigation;

})();
