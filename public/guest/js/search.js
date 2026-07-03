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
            this.prefillSearchInput();
            this.setupNavigationEnhancements();
            this.setupMobileMenu();
            console.log('✅ Search & Navigation Initialized');
        },
        
        prefillSearchInput() {
            const params = new URLSearchParams(window.location.search);
            const q = params.get('q');
            if (q) {
                const searchInputs = document.querySelectorAll('#searchInput, #searchInputMobile, #mobileProdSearch');
                searchInputs.forEach(input => {
                    input.value = q;
                });
            }
        },
        
        setupSearchFunctionality() {
            // Handle form submit (covers both Enter key on Android and button click)
            const searchForms = document.querySelectorAll('form.input-group');
            
            searchForms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const input = form.querySelector('input[type="text"]');
                    if (input && input.value.trim()) {
                        this.performSearch(input.value, input);
                    }
                });
            });
        },
        
        performSearch(query, inputElement) {
            if (!query.trim()) {
                return;
            }
            
            window.location.href = `/products?q=${encodeURIComponent(query)}`;
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
