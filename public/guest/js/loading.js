/**
 * PAS Market Loading System
 * Comprehensive loading indicators and page transitions
 */

(function() {
    'use strict';

    const LoadingSystem = {
        isLoading: false,
        loadingElements: new Set(),
        
        init() {
            this.setupLoadingIndicators();
            this.setupPageTransitions();
            this.setupAjaxLoading();
            console.log('✅ Loading System Initialized');
        },
        
        setupLoadingIndicators() {
            // Create global loading overlay
            this.createGlobalLoadingOverlay();
            
            // Create page loading indicator
            this.createPageLoadingIndicator();
            
            // Setup button loading states
            this.setupButtonLoadingStates();
        },
        
        createGlobalLoadingOverlay() {
            const overlay = document.createElement('div');
            overlay.id = 'global-loading-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(4px);
                z-index: 9999;
                display: none;
                align-items: center;
                justify-content: center;
                flex-direction: column;
            `;
            
            overlay.innerHTML = `
                <div style="
                    width: 60px;
                    height: 60px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid var(--primary-color, #ff6b35);
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin-bottom: 20px;
                "></div>
                <div style="
                    font-size: 18px;
                    font-weight: 500;
                    color: #333;
                    text-align: center;
                ">Memuat data...</div>
                <div style="
                    font-size: 14px;
                    color: #666;
                    margin-top: 8px;
                    text-align: center;
                ">Mohon tunggu sebentar</div>
            `;
            
            // Add animation styles
            const style = document.createElement('style');
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
                @keyframes slideInUp {
                    from { transform: translateY(20px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
            `;
            document.head.appendChild(style);
            
            document.body.appendChild(overlay);
            this.globalOverlay = overlay;
        },
        
        createPageLoadingIndicator() {
            const indicator = document.createElement('div');
            indicator.id = 'page-loading-indicator';
            indicator.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background: var(--primary-color, #ff6b35);
                z-index: 10000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            `;
            
            document.body.appendChild(indicator);
            this.pageIndicator = indicator;
        },
        
        setupButtonLoadingStates() {
            // Add loading state functionality to buttons
            document.addEventListener('click', (e) => {
                const button = e.target.closest('button[type="submit"], .btn-loading, .btn-submit');
                if (button && !button.disabled) {
                    // Skip login/register forms - let them submit normally
                    const form = button.closest('form');
                    if (form && (form.id === 'loginForm' || form.id === 'registerForm' || form.dataset.ajax === 'false')) {
                        return; // Don't add loading state to login/register forms or no-ajax forms
                    }
                    
                    // Auto-add loading state for submit buttons
                    if (button.type === 'submit' || button.classList.contains('btn-loading')) {
                        this.showButtonLoading(button);
                        
                        // Auto-hide after 3 seconds (safety timeout)
                        setTimeout(() => {
                            this.hideButtonLoading(button);
                        }, 3000);
                    }
                }
            });
        },
        
        setupPageTransitions() {
            // Listen for router navigation events
            if (window.PAS && window.PAS.Router) {
                // Intercept router navigation
                const originalNavigate = window.PAS.Router.navigate;
                window.PAS.Router.navigate = (path, pushState = true) => {
                    this.showPageLoading();
                    
                    // Call original navigate
                    setTimeout(() => {
                        originalNavigate.call(window.PAS.Router, path, pushState);
                        this.hidePageLoading();
                    }, 300);
                };
            }
            
            // Handle browser navigation
            window.addEventListener('beforeunload', () => {
                this.showPageLoading();
            });
            
            window.addEventListener('load', () => {
                this.hidePageLoading();
            });
        },
        
        setupAjaxLoading() {
            // Intercept XMLHttpRequest
            const originalOpen = XMLHttpRequest.prototype.open;
            const originalSend = XMLHttpRequest.prototype.send;
            
            let activeRequests = 0;
            
            XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
                this._url = url;
                return originalOpen.apply(this, arguments);
            };
            
            XMLHttpRequest.prototype.send = function(data) {
                activeRequests++;
                this.addEventListener('loadstart', () => {
                    LoadingSystem.showAjaxLoading();
                });
                
                this.addEventListener('loadend', () => {
                    activeRequests--;
                    if (activeRequests <= 0) {
                        LoadingSystem.hideAjaxLoading();
                        activeRequests = 0;
                    }
                });
                
                return originalSend.apply(this, arguments);
            };
            
            // Intercept Fetch API
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                LoadingSystem.showAjaxLoading();
                
                return originalFetch.apply(this, arguments)
                    .finally(() => {
                        LoadingSystem.hideAjaxLoading();
                    });
            };
        },
        
        showGlobalLoading(message = 'Memuat data...') {
            if (this.globalOverlay) {
                // Update message if provided
                const messageElement = this.globalOverlay.querySelector('div:last-child');
                if (messageElement) {
                    messageElement.textContent = message;
                }
                
                this.globalOverlay.style.display = 'flex';
                this.globalOverlay.style.animation = 'fadeIn 0.3s ease';
                this.isLoading = true;
            }
        },
        
        hideGlobalLoading() {
            if (this.globalOverlay) {
                this.globalOverlay.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    this.globalOverlay.style.display = 'none';
                    this.isLoading = false;
                }, 300);
            }
        },
        
        showPageLoading() {
            if (this.pageIndicator) {
                this.pageIndicator.style.transform = 'translateX(0%)';
                this.pageIndicator.style.transition = 'transform 0.3s ease';
                
                // Animate progress bar
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 30;
                    if (progress > 90) progress = 90;
                    this.pageIndicator.style.transform = `translateX(-${100 - progress}%)`;
                    
                    if (progress >= 90) {
                        clearInterval(interval);
                    }
                }, 200);
                
                this.pageLoadingInterval = interval;
            }
        },
        
        hidePageLoading() {
            if (this.pageIndicator) {
                if (this.pageLoadingInterval) {
                    clearInterval(this.pageLoadingInterval);
                }
                
                this.pageIndicator.style.transform = 'translateX(0%)';
                this.pageIndicator.style.transition = 'transform 0.1s ease';
                
                setTimeout(() => {
                    this.pageIndicator.style.transform = 'translateX(-100%)';
                    this.pageIndicator.style.transition = 'transform 0.3s ease';
                }, 100);
            }
        },
        
        showAjaxLoading() {
            this.showPageLoading();
        },
        
        hideAjaxLoading() {
            this.hidePageLoading();
        },
        
        showButtonLoading(button, text = 'Memproses...') {
            if (button.dataset.loading === 'true') return;
            
            // Save original state
            button.dataset.originalText = button.innerHTML;
            button.dataset.loading = 'true';
            button.disabled = true;
            
            // Add loading class
            button.classList.add('btn-loading');
            
            // Update button content
            button.innerHTML = `
                <span style="display: inline-block; width: 16px; height: 16px; border: 2px solid transparent; border-top: 2px solid currentColor; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 8px;"></span>
                ${text}
            `;
            
            // Add to tracking
            this.loadingElements.add(button);
        },
        
        hideButtonLoading(button, success = false) {
            if (button.dataset.loading !== 'true') return;
            
            // Restore original state
            button.innerHTML = button.dataset.originalText;
            button.disabled = false;
            button.dataset.loading = 'false';
            button.classList.remove('btn-loading');
            
            // Show success state if requested
            if (success) {
                button.classList.add('btn-success-temp');
                button.innerHTML = '<i class="bi bi-check-circle"></i> Berhasil!';
                
                setTimeout(() => {
                    button.classList.remove('btn-success-temp');
                    button.innerHTML = button.dataset.originalText;
                }, 2000);
            }
            
            // Remove from tracking
            this.loadingElements.delete(button);
        },
        
        showElementLoading(element, message = 'Loading...') {
            if (element.dataset.loading === 'true') return;
            
            element.dataset.loading = 'true';
            element.dataset.originalContent = element.innerHTML;
            
            element.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; padding: 20px;">
                    <div style="
                        width: 20px;
                        height: 20px;
                        border: 2px solid #f3f3f3;
                        border-top: 2px solid var(--primary-color, #ff6b35);
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                        margin-right: 10px;
                    "></div>
                    <span>${message}</span>
                </div>
            `;
            
            this.loadingElements.add(element);
        },
        
        hideElementLoading(element) {
            if (element.dataset.loading !== 'true') return;
            
            element.innerHTML = element.dataset.originalContent;
            element.dataset.loading = 'false';
            
            this.loadingElements.delete(element);
        },
        
        showSkeletonLoading(container, count = 3) {
            const skeletonHTML = Array(count).fill('').map(() => `
                <div style="
                    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                    background-size: 200% 100%;
                    animation: pulse 1.5s infinite;
                    border-radius: 8px;
                    height: 200px;
                    margin-bottom: 16px;
                "></div>
            `).join('');
            
            container.innerHTML = skeletonHTML;
            container.dataset.skeleton = 'true';
        },
        
        hideSkeletonLoading(container) {
            if (container.dataset.skeleton === 'true') {
                container.innerHTML = '';
                container.dataset.skeleton = 'false';
            }
        },
        
        // Utility methods
        isCurrentlyLoading() {
            return this.isLoading || this.loadingElements.size > 0;
        },
        
        hideAllLoading() {
            this.hideGlobalLoading();
            this.hidePageLoading();
            this.hideAjaxLoading();
            
            // Hide all button loading states
            this.loadingElements.forEach(element => {
                if (element.tagName === 'BUTTON') {
                    this.hideButtonLoading(element);
                } else {
                    this.hideElementLoading(element);
                }
            });
            
            this.loadingElements.clear();
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        LoadingSystem.init();
    });

    // Expose to global scope
    window.PAS = window.PAS || {};
    window.PAS.LoadingSystem = LoadingSystem;

})();