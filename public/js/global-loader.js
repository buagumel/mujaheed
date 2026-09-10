/**
 * Global Progress Bar, Action Loader & Skeleton System
 * Material Kit Theme Engine
 */

(function () {
    'use strict';

    // 1. Create Top Progress Bar Element
    let progressBar = document.getElementById('global-top-progress');
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'global-top-progress';
        document.body.appendChild(progressBar);
    }

    // 2. Create Action Overlay Loader Element
    let actionLoader = document.getElementById('global-action-loader');
    if (!actionLoader) {
        actionLoader = document.createElement('div');
        actionLoader.id = 'global-action-loader';
        actionLoader.innerHTML = `
            <div class="loader-spinner-container">
                <div class="loader-spinner"></div>
                <div class="loader-spinner-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>
            <div class="loader-text" id="global-loader-title">Processing Request...</div>
            <div class="loader-subtext" id="global-loader-subtitle">Please wait a moment</div>
        `;
        document.body.appendChild(actionLoader);
    }

    let progressTimer = null;
    let currentProgress = 0;

    window.GlobalLoader = {
        // Top Progress Bar Controls
        startProgress: function () {
            if (progressBar) {
                progressBar.style.opacity = '1';
                progressBar.style.width = '15%';
                currentProgress = 15;

                clearInterval(progressTimer);
                progressTimer = setInterval(function () {
                    if (currentProgress < 85) {
                        currentProgress += Math.random() * 10;
                        progressBar.style.width = currentProgress + '%';
                    }
                }, 200);
            }
        },

        completeProgress: function () {
            if (progressBar) {
                clearInterval(progressTimer);
                progressBar.style.width = '100%';
                setTimeout(function () {
                    progressBar.style.opacity = '0';
                    setTimeout(function () {
                        progressBar.style.width = '0%';
                    }, 300);
                }, 200);
            }
        },

        // Full Screen Overlay Action Loader Controls
        showActionLoader: function (title, subtitle) {
            const titleEl = document.getElementById('global-loader-title');
            const subEl = document.getElementById('global-loader-subtitle');
            if (titleEl && title) titleEl.innerText = title;
            if (subEl && subtitle) subEl.innerText = subtitle;
            if (actionLoader) actionLoader.classList.add('active');
            this.startProgress();
        },

        hideActionLoader: function () {
            if (actionLoader) actionLoader.classList.remove('active');
            this.completeProgress();
        },

        // Skeleton Generator Helper
        renderSkeletonCards: function (container, count, type) {
            if (!container) return;
            count = count || 4;
            type = type || 'grid';
            let html = '';
            for (let i = 0; i < count; i++) {
                html += `
                    <div class="mk-card mk-skeleton-card" style="padding:20px;">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <div class="mk-skeleton mk-skeleton-circle" style="width:40px; height:40px;"></div>
                            <div style="flex-grow:1;">
                                <div class="mk-skeleton mk-skeleton-text" style="width:60%;"></div>
                                <div class="mk-skeleton mk-skeleton-text sm" style="width:40%;"></div>
                            </div>
                        </div>
                        <div class="mk-skeleton mk-skeleton-text" style="width:80%;"></div>
                        <div class="mk-skeleton mk-skeleton-text sm" style="width:50%;"></div>
                    </div>
                `;
            }
            container.innerHTML = html;
        }
    };

    // Auto start progress on page navigation & page load
    window.addEventListener('beforeunload', function () {
        window.GlobalLoader.startProgress();
    });

    document.addEventListener('DOMContentLoaded', function () {
        window.GlobalLoader.completeProgress();

        // Attach automatic loading feedback to all standard form submissions
        const forms = document.querySelectorAll('form:not([data-no-loader])');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                // Don't trigger if invalid HTML5 validation
                if (!form.checkValidity()) return;

                // If form has [data-action-loader], show full modal overlay
                if (form.getAttribute('data-action-loader')) {
                    const title = form.getAttribute('data-loader-title') || 'Processing Transaction...';
                    const subtitle = form.getAttribute('data-loader-subtitle') || 'Please do not refresh or close the page.';
                    window.GlobalLoader.showActionLoader(title, subtitle);
                } else {
                    window.GlobalLoader.startProgress();
                }
            });
        });
    });

    // Intercept native fetch() calls for automatic top progress bar
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function () {
            window.GlobalLoader.startProgress();
            return originalFetch.apply(this, arguments)
                .then(function (response) {
                    window.GlobalLoader.completeProgress();
                    return response;
                })
                .catch(function (error) {
                    window.GlobalLoader.completeProgress();
                    throw error;
                });
        };
    }
})();
