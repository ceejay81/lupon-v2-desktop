<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer | {{ $payload['title'] }}</title>
    
    <!-- Local Phosphor Icons -->
    <script src="{{ asset('vendor/phosphor-icons/phosphor-icons-local.js') }}"></script>

    <!-- Polyfills -->
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
    </script>

    <!-- PDF.js Generic Build (More compatible than ESM in some Electron versions) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.js"></script>
    <script>
        window.pdfjsLib = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
        if (window.pdfjsLib) {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.js';
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf_viewer.min.css">
    
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
            margin: 0; padding: 0;
            background: var(--bg-body);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, sans-serif;
            height: 100vh; overflow: hidden;
            display: flex; flex-direction: column;
        }

        .pdf-toolbar {
            background: var(--bg-toolbar);
            border-bottom: 1px solid var(--border-color);
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1rem; flex-shrink: 0; z-index: 10;
        }

        .toolbar-group { display: flex; align-items: center; gap: 0.5rem; }

        .btn-tool {
            background: transparent; border: 1px solid transparent;
            color: var(--text-primary); width: 36px; height: 36px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; font-size: 1.25rem;
        }
        
        .btn-tool:hover:not(:disabled) { background: var(--btn-hover); border-color: var(--border-color); }
        .btn-tool:disabled { opacity: 0.5; cursor: not-allowed; }

        .toolbar-title-container { display: flex; flex-direction: column; margin-left: 1rem; max-width: 350px; }
        .toolbar-title { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .toolbar-subtitle { font-size: 0.7rem; color: var(--text-nav); }

        .page-nav-input {
            width: 40px; background: #111827; border: 1px solid var(--border-color);
            color: var(--text-primary); text-align: center; border-radius: 6px;
            padding: 4px; font-size: 0.85rem; font-weight: 600;
        }

        .page-nav-text { font-size: 0.85rem; color: var(--text-nav); }

        #viewerContainer {
            flex: 1; overflow: auto; position: relative;
            background: #111827; padding: 2rem 0;
            display: flex; flex-direction: column; align-items: center;
        }

        .pageContainer {
            position: relative; margin-bottom: 2rem;
            background: white; box-shadow: var(--canvas-shadow);
        }

        .pageContainer canvas { display: block; }
        
        .textLayer {
            position: absolute; left: 0; top: 0; right: 0; bottom: 0;
            overflow: hidden; opacity: 0.2; line-height: 1.0;
        }

        .loading-overlay {
            position: absolute; inset: 0; background: rgba(17, 24, 39, 0.95);
            display: flex; align-items: center; justify-content: center;
            z-index: 50; color: white;
        }

        .progress-container {
            width: 300px; background: #374151; height: 8px; border-radius: 4px;
            overflow: hidden; margin-top: 1rem;
        }
        
        .progress-bar {
            height: 100%; background: var(--accent-blue);
            transition: width 0.3s ease;
        }
        
        .error-message {
            background: #ef4444; color: white; padding: 1.5rem;
            border-radius: 12px; text-align: center; max-width: 500px;
        }

        .error-details {
            background: rgba(0, 0, 0, 0.2); padding: 0.75rem; border-radius: 6px;
            font-family: monospace; font-size: 0.8rem; margin-top: 1rem;
            text-align: left; word-break: break-all; white-space: pre-wrap;
        }
    </style>

    <script>
        window.pdfApp = function() {
            let _pdfDoc = null;

            return {
                pdfId: '{{ $payload['type'] }}_{{ $payload['id'] }}',
                pageNum: 1,
                inputPageNum: 1,
                pageCount: 0,
                scale: 1.2,
                loading: true,
                loadingPercent: 0,
                loadingStatus: 'Initializing...',
                error: false,
                errorMsg: '',
                errorDetails: '',
                isFullscreen: false,
                pdfUrl: @json($payload['url']),

                async init() {
                    console.log('Alpine: pdfApp init() starting...');
                    const startTime = Date.now();
                    
                    try {
                        console.log('Alpine: Checking for pdfjsLib...');
                        let attempts = 0;
                        while (!window.pdfjsLib && attempts < 50) {
                            await new Promise(r => setTimeout(r, 100));
                            attempts++;
                        }

                        if (!window.pdfjsLib) {
                            console.error('Alpine: PDF.js library NOT found after 5s');
                            throw new Error("PDF.js library failed to load. Check console for script errors.");
                        }
                        
                        console.log('Alpine: pdfjsLib detected:', window.pdfjsLib.version);
                        this.loadingStatus = 'Requesting PDF from server...';

                        console.log('Alpine: Starting getDocument for URL:', this.pdfUrl);
                        const loadingTask = window.pdfjsLib.getDocument({
                            url: this.pdfUrl,
                            withCredentials: true,
                            isEvalSupported: false,
                            disableFontFace: false, // Ensure fonts load
                        });
                        
                        loadingTask.onProgress = (data) => {
                            if (data.total > 0) {
                                this.loadingPercent = Math.round((data.loaded / data.total) * 100);
                                this.loadingStatus = `Downloading PDF... ${this.loadingPercent}%`;
                                // console.log(`Alpine: Download progress: ${this.loadingPercent}%`);
                            } else {
                                this.loadingStatus = `Downloading (${(data.loaded / 1024).toFixed(0)} KB)...`;
                            }
                        };

                        console.log('Alpine: Waiting for loadingTask.promise...');
                        
                        // Add a diagnostic timeout
                        const promiseTimeout = setTimeout(() => {
                            if (this.loading) {
                                console.warn('Alpine: PDF loading is taking unusually long (15s+). Check network or worker logs.');
                                this.loadingStatus = 'Still waiting for PDF.js to respond...';
                            }
                        }, 15000);

                        try {
                            _pdfDoc = await loadingTask.promise;
                            clearTimeout(promiseTimeout);
                            console.log('Alpine: loadingTask.promise RESOLVED successfully');
                        } catch (promiseErr) {
                            clearTimeout(promiseTimeout);
                            console.error('Alpine: loadingTask.promise REJECTED:', promiseErr);
                            throw promiseErr;
                        }

                        this.pageCount = _pdfDoc.numPages;
                        console.log('Alpine: Document ready. Total pages:', this.pageCount);
                        this.loadingStatus = `Rendering ${this.pageCount} pages...`;
                        
                        await this.renderAllPagesContinuous();
                        console.log(`Alpine: Initialization complete in ${Date.now() - startTime}ms`);

                    } catch (err) {
                        console.error('Detailed PDF App Error during init:', err);
                        this.error = true;
                        this.loading = false;
                        this.errorMsg = err.message || 'Failed to load PDF.';
                        this.errorDetails = `${err.name}: ${err.message}\nStatus: ${this.loadingStatus}\nStack: ${err.stack || 'No stack trace'}`;
                    }
                },

                async onScroll() {
                    const container = document.getElementById('viewerContainer');
                    if (!container) return;
                    const scrollPos = container.scrollTop;
                    const pageNodes = document.querySelectorAll('.pageContainer');
                    pageNodes.forEach(node => {
                        if (node.offsetTop <= scrollPos + (container.clientHeight / 2) && 
                            node.offsetTop + node.clientHeight > scrollPos + (container.clientHeight / 2)) {
                            this.pageNum = parseInt(node.id.split('_')[1]);
                            this.inputPageNum = this.pageNum;
                        }
                    });
                },

                async renderAllPagesContinuous() {
                    const container = document.getElementById('pagesContainer');
                    if (!container) return;
                    container.innerHTML = ''; 
                    for (let num = 1; num <= this.pageCount; num++) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'pageContainer'; wrapper.id = `pageWrapper_${num}`;
                        const canvas = document.createElement('canvas'); wrapper.appendChild(canvas);
                        const textDiv = document.createElement('div'); textDiv.className = 'textLayer'; wrapper.appendChild(textDiv);
                        container.appendChild(wrapper);
                        await this.renderPageInternal(num, canvas, textDiv, wrapper);
                        if (num === 1) this.loading = false;
                    }
                },

                async renderPageInternal(num, canvas, textDiv, wrapper) {
                    try {
                        const page = await _pdfDoc.getPage(num);
                        const viewport = page.getViewport({ scale: this.scale });
                        canvas.height = viewport.height; canvas.width = viewport.width;
                        wrapper.style.width = viewport.width + 'px'; wrapper.style.height = viewport.height + 'px';
                        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
                        
                        if (window.pdfjsLib.TextLayer) {
                             const textContent = await page.getTextContent();
                             const textLayer = new window.pdfjsLib.TextLayer({
                                 textContentSource: textContent, container: textDiv, viewport
                             });
                             await textLayer.render();
                        }
                    } catch (e) { console.error('Render Error page ' + num, e); }
                },

                reRenderForScale() { if(_pdfDoc) this.renderAllPagesContinuous(); },
                zoomIn() { this.scale = Math.min(3.0, parseFloat((this.scale + 0.2).toFixed(1))); this.reRenderForScale(); },
                zoomOut() { this.scale = Math.max(0.4, parseFloat((this.scale - 0.2).toFixed(1))); this.reRenderForScale(); },
                fitWidth() {
                    if(!_pdfDoc) return;
                    _pdfDoc.getPage(1).then(page => {
                        const container = document.getElementById('viewerContainer');
                        if (!container) return;
                        this.scale = (container.clientWidth - 40) / page.getViewport({scale:1}).width;
                        this.reRenderForScale();
                    });
                },
                toggleFullscreen() {
                    if (!document.fullscreenElement) document.body.requestFullscreen();
                    else document.exitFullscreen();
                },
                prevPage() { if (this.pageNum > 1) this.scrollToPage(--this.pageNum); },
                nextPage() { if (this.pageNum < this.pageCount) this.scrollToPage(++this.pageNum); },
                goToPage() { this.scrollToPage(this.inputPageNum); },
                scrollToPage(num) {
                    const el = document.getElementById(`pageWrapper_${num}`);
                    if(el) el.scrollIntoView({ behavior: 'smooth' });
                },
                printPdf() {
                    const iframe = document.getElementById('printFrame');
                    if (!iframe) return;
                    iframe.src = this.pdfUrl;
                    iframe.onload = () => iframe.contentWindow.print();
                }
            };
        };
    </script>
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
        </div>

        <div class="toolbar-group">
            <button @click="printPdf" class="btn-tool" title="Print Native">
                <i class="ph ph-printer"></i>
            </button>
            <button @click="toggleFullscreen" class="btn-tool" title="Toggle Fullscreen">
                <i class="ph ph-corners-out"></i>
            </button>
            <a href="{{ $payload['download_url'] }}" class="btn-tool" title="Download Original">
                <i class="ph ph-download-simple"></i>
            </a>
        </div>
    </div>

    <div id="viewerContainer" @scroll.debounce.500ms="onScroll">
        <div id="pagesContainer" style="display: flex; flex-direction: column; align-items: center; width: 100%;"></div>

        <div x-show="loading" class="loading-overlay">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                <i class="ph ph-spinner ph-spin" style="font-size: 3rem; color: var(--accent-blue);"></i>
                <div style="font-weight: 600; font-size: 1.1rem; margin-top: 1rem;" x-text="loadingStatus"></div>
                <div class="progress-container">
                    <div class="progress-bar" :style="'width: ' + loadingPercent + '%'"></div>
                </div>
            </div>
        </div>

        <div x-show="error" class="loading-overlay" style="display: none;">
            <div class="error-message">
                <i class="ph ph-warning-octagon" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <h2 style="margin: 0 0 1rem 0;">Failed to Load PDF</h2>
                <p x-text="errorMsg" style="margin-bottom: 1.5rem;"></p>
                <div class="error-details" x-text="errorDetails"></div>
            </div>
        </div>
    </div>
</body>
</html>
