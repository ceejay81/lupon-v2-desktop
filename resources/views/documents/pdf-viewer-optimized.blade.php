<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer | {{ $payload['title'] }}</title>
    
    <!-- Local Phosphor Icons -->
    <script src="{{ asset('vendor/phosphor-icons/phosphor-icons-local.js') }}"></script>
    <!-- Alpine.js (needed for popup windows that don't have Vite) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Polyfills for older Electron/Chromium compatibility -->
    <script>
        if (typeof URL.parse === 'undefined') {
            URL.parse = function(url, base) {
                try { return new URL(url, base); } catch (e) { return null; }
            };
        }
        if (typeof Promise.withResolvers === 'undefined') {
            Promise.withResolvers = function() {
                let resolve, reject;
                const promise = new Promise(function(res, rej) { resolve = res; reject = rej; });
                return { promise: promise, resolve: resolve, reject: reject };
            };
        }
        if (!Uint8Array.prototype.toHex) {
            Uint8Array.prototype.toHex = function() {
                return Array.prototype.map.call(this, function(byte) {
                    return ('0' + (byte & 0xFF).toString(16)).slice(-2);
                }).join('');
            };
        }
        if (!Map.prototype.getOrInsertComputed) {
            Map.prototype.getOrInsertComputed = function(key, callback) {
                if (this.has(key)) return this.get(key);
                const value = callback(key);
                this.set(key, value);
                return value;
            };
        }
    </script>

    <!-- PDF.js Core -->
    <script type="module">
        console.log('[PDF.js Module] Starting to load...');
        (async () => {
            try {
                const pdfjsLib = await import('{{ asset('vendor/pdfjs/build/pdf.mjs') }}');
                pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('vendor/pdfjs/build/pdf.worker.mjs') }}';
                window.pdfjsLib = pdfjsLib;
                console.log('[PDF.js Module] Loaded successfully, version:', pdfjsLib.version);
            } catch (e) {
                console.error('[PDF.js Module] Failed to load:', e);
            }
        })();
    </script>
    
    <link rel="stylesheet" href="{{ asset('vendor/pdfjs/web/pdf_viewer.css') }}">
    
    <style>
        :root {
            --bg-body: #111827;
            --bg-toolbar: #1f2937;
            --border-color: #374151;
            --text-primary: #f9fafb;
            --text-nav: #9ca3af;
            --accent-blue: #3b82f6;
            --btn-hover: #374151;
            --canvas-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg-body);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .pdf-toolbar {
            background: var(--bg-toolbar);
            border-bottom: 1px solid var(--border-color);
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            flex-shrink: 0;
            z-index: 10;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-tool {
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-primary);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.25rem;
        }
        
        .btn-tool:hover:not(:disabled) {
            background: var(--btn-hover);
            border-color: var(--border-color);
        }

        .btn-tool:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .toolbar-title-container {
            display: flex;
            flex-direction: column;
            margin-left: 1rem;
            max-width: 350px;
        }

        .toolbar-title {
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .toolbar-subtitle {
            font-size: 0.7rem;
            color: var(--text-nav);
        }

        .page-nav-input {
            width: 40px;
            background: #111827;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            text-align: center;
            border-radius: 6px;
            padding: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .page-nav-text {
            font-size: 0.85rem;
            color: var(--text-nav);
        }

        #viewerContainer {
            flex: 1;
            overflow: auto;
            position: relative;
            background: #111827;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Virtual scrolling: Placeholder for pages not yet rendered */
        .page-placeholder {
            position: relative;
            margin-bottom: 2rem;
            background: #1f2937;
            box-shadow: var(--canvas-shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-nav);
            font-size: 0.875rem;
        }

        .pageContainer {
            position: relative;
            margin-bottom: 2rem;
            background: white;
            box-shadow: var(--canvas-shadow);
        }

        .pageContainer canvas {
            display: block;
        }
        
        .textLayer {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            opacity: 0.2;
            line-height: 1.0;
        }

        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            color: white;
            font-size: 1.25rem;
        }
        
        .error-message {
            background: #ef4444;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
        }

        /* Render quality toggle */
        .quality-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.5rem;
            background: #111827;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .quality-btn {
            padding: 0.25rem 0.5rem;
            border: none;
            background: transparent;
            color: var(--text-nav);
            cursor: pointer;
            border-radius: 4px;
        }

        .quality-btn.active {
            background: var(--accent-blue);
            color: white;
        }
    </style>
</head>
<body x-data="pdfApp()">

    <iframe id="printFrame" style="display: none;"></iframe>

    <div class="pdf-toolbar">
        <div class="toolbar-group">
            <a href="{{ $payload['back_url'] }}" class="btn-tool" title="Back to Case">
                <i class="ph ph-arrow-left"></i>
            </a>
            <div class="toolbar-title-container">
                <div class="toolbar-title">{{ $payload['title'] }}</div>
                <div class="toolbar-subtitle">{{ $payload['file_size'] }}</div>
            </div>
        </div>

        <div class="toolbar-group">
            <button @click="prevPage" :disabled="pageNum <= 1" class="btn-tool" title="Previous Page">
                <i class="ph ph-caret-left"></i>
            </button>
            
            <div style="display: flex; align-items: center; gap: 0.5rem; margin: 0 0.5rem;">
                <input type="number" x-model.number="inputPageNum" @keydown.enter="goToPage" class="page-nav-input" min="1" :max="pageCount">
                <span class="page-nav-text">of <span x-text="pageCount"></span></span>
            </div>

            <button @click="nextPage" :disabled="pageNum >= pageCount" class="btn-tool" title="Next Page">
                <i class="ph ph-caret-right"></i>
            </button>
            
            <div style="width: 1px; height: 24px; background: var(--border-color); margin: 0 0.5rem;"></div>
            
            <button @click="zoomOut" class="btn-tool" title="Zoom Out">
                <i class="ph ph-magnifying-glass-minus"></i>
            </button>
            <span class="page-nav-text" style="width: 45px; text-align: center;" x-text="Math.round(scale * 100) + '%'"></span>
            <button @click="zoomIn" class="btn-tool" title="Zoom In">
                <i class="ph ph-magnifying-glass-plus"></i>
            </button>
            <button @click="fitWidth" class="btn-tool" title="Fit to Width">
                <i class="ph ph-arrows-out-line-horizontal"></i>
            </button>

            <div style="width: 1px; height: 24px; background: var(--border-color); margin: 0 0.5rem;"></div>

            <!-- Quality Toggle -->
            <div class="quality-toggle">
                <button 
                    @click="setQuality('draft')" 
                    :class="{'active': quality === 'draft'}"
                    class="quality-btn"
                    title="Faster, lower quality"
                >
                    Draft
                </button>
                <button 
                    @click="setQuality('high')" 
                    :class="{'active': quality === 'high'}"
                    class="quality-btn"
                    title="Slower, crisp text"
                >
                    High
                </button>
            </div>
        </div>

        <div class="toolbar-group">
            <button title="Press Ctrl+F to Search Text" class="btn-tool" onclick="alert('Press Ctrl+F (or Cmd+F on Mac) to search text.')">
                <i class="ph ph-magnifying-glass"></i>
            </button>

            <button @click="printPdf" class="btn-tool" title="Print Native">
                <i class="ph ph-printer"></i>
            </button>
            <button @click="toggleFullscreen" class="btn-tool" title="Toggle Fullscreen">
                <i class="ph" :class="isFullscreen ? 'ph-corners-in' : 'ph-corners-out'"></i>
            </button>
            <a href="{{ $payload['download_url'] }}" class="btn-tool" title="Download Original">
                <i class="ph ph-download-simple"></i>
            </a>
        </div>
    </div>

    <div id="viewerContainer" @scroll.debounce.150ms="onScroll">
        <div id="pagesContainer" style="display: flex; flex-direction: column; align-items: center; width: 100%;"></div>

        <div x-show="loading && pageCount === 0" class="loading-overlay">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <i class="ph ph-spinner ph-spin" style="font-size: 2.5rem; color: var(--accent-blue);"></i>
                <span>Loading Document... <span x-text="loadingProgress"></span></span>
            </div>
        </div>

        <div x-show="error" class="loading-overlay" style="background: rgba(17, 24, 39, 0.95);">
            <div class="error-message">
                <i class="ph ph-warning-octagon" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h3 style="margin-top: 0;">Failed to Load PDF</h3>
                <p x-text="errorMsg" style="font-size: 0.9rem; margin-bottom: 0.5rem;"></p>
                <p x-show="errorDetails" x-text="errorDetails" style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 1.5rem; font-family: monospace;"></p>
                
                <a href="{{ $payload['download_url'] }}" class="btn-tool" style="background: white; color: #ef4444; width: auto; padding: 0 1rem; margin: 0 auto; text-decoration: none;">
                    Download Original PDF
                </a>
            </div>
        </div>
    </div>

    <script>
        console.log('[Alpine] Waiting for alpine:init...');
        document.addEventListener('alpine:init', () => {
            console.log('[Alpine] alpine:init fired, registering pdfApp...');
            Alpine.data('pdfApp', () => {
                let _pdfDoc = null;
                let _pageViewports = new Map(); // Cache page viewports
                let _renderedPages = new Map(); // Track rendered pages
                let _renderQueue = []; // Queue for background rendering
                let _isRendering = false;

                return {
                    pdfId: '{{ $payload['type'] }}_{{ $payload['id'] }}',
                    pageNum: 1,
                    inputPageNum: 1,
                    pageCount: 0,
                    scale: 1.2,
                    quality: 'high', // 'draft' or 'high'
                    loading: true,
                    loadingProgress: '0%',
                    error: false,
                    errorMsg: '',
                    errorDetails: '',
                    isFullscreen: false,
                    
                    // Virtual scrolling config
                    bufferSize: 2, // Pages to render above/below viewport
                    placeholderHeight: 800, // Default height before page loads
                    
                    pdfUrl: @json($payload['url']),

                    async init() {
                        console.log('[PDF Viewer] init() started');
                        
                        // Restore saved preferences
                        const savedState = localStorage.getItem('pdfState_' + this.pdfId);
                        const savedQuality = localStorage.getItem('pdfQuality');
                        
                        if (savedQuality) this.quality = savedQuality;
                        
                        if (savedState) {
                            try {
                                const parsed = JSON.parse(savedState);
                                if (parsed.scale) this.scale = parseFloat(parsed.scale) || 1.2;
                            } catch(e) {}
                        }

                        document.addEventListener('fullscreenchange', () => {
                            this.isFullscreen = !!document.fullscreenElement;
                        });

                        try {
                            // Reset error state
                            this.error = false;
                            this.errorMsg = '';
                            this.errorDetails = '';
                            
                            // Wait for PDF.js to be available (ES module loading is async)
                            let attempts = 0;
                            while (!window.pdfjsLib && attempts < 50) {
                                await new Promise(r => setTimeout(r, 100));
                                attempts++;
                            }
                            
                            console.log('[PDF Viewer] PDF.js check - attempts:', attempts, 'pdfjsLib:', !!window.pdfjsLib);
                            
                            if (!window.pdfjsLib) {
                                throw new Error("PDF.js library not loaded after 5s. Check that pdf.mjs loaded correctly.");
                            }
                            
                            console.log('[PDF Viewer] Loading PDF from URL:', this.pdfUrl);

                            // First verify the PDF is accessible (not redirected to login)
                            try {
                                const headResponse = await fetch(this.pdfUrl, {
                                    method: 'HEAD',
                                    credentials: 'include'
                                });
                                console.log('[PDF Viewer] HEAD check status:', headResponse.status, 'Content-Type:', headResponse.headers.get('content-type'));
                                
                                if (!headResponse.ok) {
                                    if (headResponse.status === 401 || headResponse.status === 403) {
                                        throw { name: 'AuthError', status: headResponse.status, message: 'Not authenticated' };
                                    }
                                    throw new Error(`Server returned ${headResponse.status}`);
                                }
                                
                                const contentType = headResponse.headers.get('content-type') || '';
                                if (!contentType.includes('pdf')) {
                                    console.warn('[PDF Viewer] Warning: Response is not PDF:', contentType);
                                }
                            } catch (fetchErr) {
                                if (fetchErr.name === 'AuthError') throw fetchErr;
                                console.warn('[PDF Viewer] HEAD request failed:', fetchErr);
                                // Continue anyway, let PDF.js handle the error
                            }

                            const loadingTask = pdfjsLib.getDocument({
                                url: this.pdfUrl,
                                withCredentials: true
                            });
                            
                            loadingTask.onProgress = (progressData) => {
                                if (progressData.total > 0) {
                                    this.loadingProgress = Math.round((progressData.loaded / progressData.total) * 100) + "%";
                                }
                            };

                            _pdfDoc = await loadingTask.promise;
                            this.pageCount = _pdfDoc.numPages;
                            
                            // Create placeholder structure for all pages
                            this.createPageStructure();
                            
                            // Initial render of visible pages
                            this.updateVisiblePages();

                            // Restore scroll position
                            if (savedState) {
                                setTimeout(() => {
                                    try {
                                        const parsed = JSON.parse(savedState);
                                        if (parsed.scrollTop) {
                                            document.getElementById('viewerContainer').scrollTop = parsed.scrollTop;
                                        }
                                    } catch(e) {}
                                }, 100);
                            }

                        } catch (err) {
                            // Ensure error is always logged even if err is undefined or weird
                            const errorInfo = {
                                message: err?.message || 'Unknown error',
                                name: err?.name || 'Unknown',
                                status: err?.status,
                                stack: err?.stack,
                                fullError: err
                            };
                            console.error('[PDF Viewer] Load Error:', errorInfo);
                            
                            this.error = true;
                            this.loading = false;
                            
                            // Provide specific error messages based on error type
                            if (err.name === 'AuthError' || err.status === 401 || err.status === 403) {
                                this.errorMsg = 'Not authenticated in this window. Please close this window and reopen the PDF from the main application window.';
                            } else if (err.name === 'MissingPDFException') {
                                this.errorMsg = 'PDF file not found. The file may have been moved or deleted.';
                            } else if (err.name === 'InvalidPDFException') {
                                this.errorMsg = 'The PDF file is corrupted or invalid.';
                            } else if (err.name === 'UnexpectedResponseException') {
                                if (err.status === 403) {
                                    this.errorMsg = 'Authentication failed. Please close this window and try again from the main application.';
                                } else {
                                    this.errorMsg = `Server error (HTTP ${err.status}). The file may not be accessible.`;
                                }
                            } else if (err.message?.includes('network')) {
                                this.errorMsg = 'Network error while loading PDF. Check your connection.';
                            } else if (err.message?.includes('worker')) {
                                this.errorMsg = 'PDF.js worker failed to load. Check browser console.';
                            } else if (err.message?.includes('Failed to fetch')) {
                                this.errorMsg = 'Failed to load PDF from server. This may be an authentication issue.';
                            } else {
                                this.errorMsg = err.message || 'An unknown error occurred while loading the PDF.';
                            }
                            
                            this.errorDetails = `${err.name || 'Error'}: ${err.message}\nStatus: ${err.status || 'N/A'}`;
                        }
                    },

                    // Create placeholder elements for all pages (fast, no rendering)
                    createPageStructure() {
                        const container = document.getElementById('pagesContainer');
                        container.innerHTML = '';

                        for (let num = 1; num <= this.pageCount; num++) {
                            const placeholder = document.createElement('div');
                            placeholder.className = 'page-placeholder';
                            placeholder.id = `pageWrapper_${num}`;
                            placeholder.style.width = '800px'; // Default, will update
                            placeholder.style.height = this.placeholderHeight + 'px';
                            placeholder.dataset.pageNum = num;
                            placeholder.innerHTML = `<span>Page ${num}</span>`;
                            container.appendChild(placeholder);
                        }
                        
                        this.loading = false;
                    },

                    // Update which pages should be visible based on scroll
                    async updateVisiblePages() {
                        if (!_pdfDoc) return;

                        const container = document.getElementById('viewerContainer');
                        const scrollTop = container.scrollTop;
                        const viewportHeight = container.clientHeight;
                        
                        // Find current page based on scroll position
                        let currentPage = 1;
                        let accumulatedHeight = 0;
                        
                        for (let num = 1; num <= this.pageCount; num++) {
                            const wrapper = document.getElementById(`pageWrapper_${num}`);
                            const height = wrapper ? wrapper.offsetHeight : this.placeholderHeight;
                            
                            if (accumulatedHeight + height > scrollTop + viewportHeight / 2) {
                                currentPage = num;
                                break;
                            }
                            accumulatedHeight += height + 32; // + margin-bottom
                        }
                        
                        this.pageNum = currentPage;
                        this.inputPageNum = currentPage;

                        // Calculate range of pages to render
                        const startPage = Math.max(1, currentPage - this.bufferSize);
                        const endPage = Math.min(this.pageCount, currentPage + this.bufferSize + Math.ceil(viewportHeight / this.placeholderHeight));

                        // Render visible pages
                        for (let num = startPage; num <= endPage; num++) {
                            if (!_renderedPages.has(num)) {
                                await this.renderPage(num);
                            }
                        }

                        // Unload distant pages to save memory (keep 2x buffer)
                        const unloadStart = Math.max(1, currentPage - (this.bufferSize * 2));
                        const unloadEnd = Math.min(this.pageCount, currentPage + (this.bufferSize * 2));
                        
                        for (let num = 1; num <= this.pageCount; num++) {
                            if ((num < unloadStart || num > unloadEnd) && _renderedPages.has(num)) {
                                this.unloadPage(num);
                            }
                        }
                    },

                    async renderPage(num) {
                        const wrapper = document.getElementById(`pageWrapper_${num}`);
                        if (!wrapper || _renderedPages.has(num)) return;

                        try {
                            const page = await _pdfDoc.getPage(num);
                            
                            // Get viewport (cache for later)
                            let viewport = _pageViewports.get(num);
                            if (!viewport) {
                                viewport = page.getViewport({ scale: this.getRenderScale() });
                                _pageViewports.set(num, viewport);
                            }

                            // Convert placeholder to actual page container
                            wrapper.className = 'pageContainer';
                            wrapper.innerHTML = '';
                            wrapper.style.width = viewport.width + 'px';
                            wrapper.style.height = viewport.height + 'px';

                            const canvas = document.createElement('canvas');
                            canvas.id = `pageCanvas_${num}`;
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            wrapper.appendChild(canvas);

                            // Text layer is always enabled for search functionality
                            const textLayerDiv = document.createElement('div');
                            textLayerDiv.className = 'textLayer';
                            textLayerDiv.id = `textLayer_${num}`;
                            wrapper.appendChild(textLayerDiv);

                            const renderContext = {
                                canvasContext: canvas.getContext('2d'),
                                viewport: viewport
                            };

                            await page.render(renderContext).promise;

                            // Render text layer for search functionality
                            const textContent = await page.getTextContent();
                            textLayerDiv.innerHTML = '';
                            textLayerDiv.style.setProperty('--scale-factor', this.getRenderScale());
                            
                            const textLayer = new pdfjsLib.TextLayer({
                                textContentSource: textContent,
                                container: textLayerDiv,
                                viewport: viewport
                            });
                            
                            await textLayer.render();

                            _renderedPages.set(num, true);
                            
                            // Clean up page object to free memory
                            page.cleanup();
                            
                        } catch (err) {
                            console.error(`Error rendering page ${num}:`, err);
                        }
                    },

                    unloadPage(num) {
                        const wrapper = document.getElementById(`pageWrapper_${num}`);
                        if (!wrapper) return;

                        // Convert back to placeholder
                        wrapper.className = 'page-placeholder';
                        wrapper.innerHTML = `<span>Page ${num}</span>`;
                        _renderedPages.delete(num);
                    },

                    getRenderScale() {
                        return this.quality === 'draft' ? this.scale * 0.7 : this.scale;
                    },

                    setQuality(quality) {
                        this.quality = quality;
                        localStorage.setItem('pdfQuality', quality);
                        
                        // Clear all rendered pages and re-render visible ones
                        _renderedPages.clear();
                        this.createPageStructure();
                        setTimeout(() => this.updateVisiblePages(), 50);
                        
                        this.dispatchToast(`Switched to ${quality} quality`, 'info');
                    },

                    onScroll() {
                        this.saveState();
                        this.updateVisiblePages();
                    },

                    saveState() {
                        if (this.pdfId && !this.error) {
                            const container = document.getElementById('viewerContainer');
                            const state = {
                                scale: this.scale,
                                scrollTop: container.scrollTop
                            };
                            localStorage.setItem('pdfState_' + this.pdfId, JSON.stringify(state));
                        }
                    },

                    reRenderForScale() {
                        if (!_pdfDoc) return;
                        
                        // Clear viewport cache since scale changed
                        _pageViewports.clear();
                        _renderedPages.clear();
                        
                        // Reset to placeholders and re-render visible
                        this.createPageStructure();
                        setTimeout(() => this.updateVisiblePages(), 50);
                    },

                    zoomIn() {
                        if (this.scale >= 3.0) return;
                        this.scale = Math.min(3.0, parseFloat((this.scale + 0.2).toFixed(1)));
                        this.reRenderForScale();
                    },

                    zoomOut() {
                        if (this.scale <= 0.4) return;
                        this.scale = Math.max(0.4, parseFloat((this.scale - 0.2).toFixed(1)));
                        this.reRenderForScale();
                    },

                    fitWidth() {
                        if (!_pdfDoc) return;
                        const container = document.getElementById('viewerContainer');
                        _pdfDoc.getPage(1).then(page => {
                            const viewport = page.getViewport({ scale: 1.0 });
                            const desiredWidth = container.clientWidth - 40;
                            this.scale = desiredWidth / viewport.width;
                            this.reRenderForScale();
                        });
                    },

                    toggleFullscreen() {
                        if (!document.fullscreenElement) {
                            document.body.requestFullscreen().catch(err => {
                                this.dispatchToast('Cannot enable fullscreen.', 'error');
                            });
                        } else {
                            document.exitFullscreen();
                        }
                    },

                    prevPage() {
                        if (this.pageNum <= 1) return;
                        this.scrollToPage(this.pageNum - 1);
                    },

                    nextPage() {
                        if (this.pageNum >= this.pageCount) return;
                        this.scrollToPage(this.pageNum + 1);
                    },

                    goToPage() {
                        let num = parseInt(this.inputPageNum);
                        if (isNaN(num) || num < 1) num = 1;
                        if (num > this.pageCount) num = this.pageCount;
                        this.scrollToPage(num);
                    },

                    scrollToPage(num) {
                        const el = document.getElementById(`pageWrapper_${num}`);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            this.pageNum = num;
                            this.inputPageNum = num;
                            this.saveState();
                            // Render will happen via scroll event
                        }
                    },

                    printPdf() {
                        this.dispatchToast('Preparing document for printing...', 'info');
                        const iframe = document.getElementById('printFrame');
                        iframe.src = this.pdfUrl + '#toolbar=0';
                        iframe.onload = () => {
                            setTimeout(() => {
                                iframe.contentWindow.print();
                            }, 500);
                        };
                    },

                    dispatchToast(msg, type) {
                        const div = document.createElement('div');
                        div.style.position = 'fixed';
                        div.style.bottom = '20px';
                        div.style.right = '20px';
                        div.style.background = type === 'error' ? '#ef4444' : (type === 'warning' ? '#f59e0b' : '#3b82f6');
                        div.style.color = 'white';
                        div.style.padding = '10px 20px';
                        div.style.borderRadius = '8px';
                        div.style.zIndex = '9999';
                        div.style.fontSize = '0.875rem';
                        div.innerText = msg;
                        document.body.appendChild(div);
                        setTimeout(() => div.remove(), 3000);
                    }
                };
            });
        });
        
        // Fallback check if Alpine doesn't load within 3 seconds
        setTimeout(() => {
            if (!window.Alpine) {
                console.error('[Alpine] Alpine.js not detected after 3s!');
            }
        }, 3000);
    </script>
</body>
</html>
