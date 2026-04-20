const { app, BrowserWindow, Tray, Menu, nativeImage, dialog, shell, ipcMain } = require('electron');
const { spawn, execSync } = require('child_process');
const path = require('path');
const net = require('net');
const http = require('http');
const fs = require('fs');
const crypto = require('crypto');
const treeKill = require('tree-kill');

// Enable Chromium Print Preview
app.commandLine.appendSwitch('enable-print-preview');

// ─── Config ───────────────────────────────────────────────────────────────────
const isDev = !app.isPackaged;

// ─── Paths ────────────────────────────────────────────────────────────────────
const rootPath = isDev
    ? path.join(__dirname, '..')
    : path.join(process.resourcesPath, 'app');

const phpBin = isDev
    ? 'php'
    : path.join(process.resourcesPath, 'php', 'php.exe');

const userDataPath = app.getPath('userData');
const dbPath = isDev
    ? path.join(rootPath, 'database', 'database.sqlite')
    : path.join(userDataPath, 'database.sqlite');
const storagePath = isDev
    ? path.join(rootPath, 'storage')
    : path.join(userDataPath, 'storage');

// ─── State ────────────────────────────────────────────────────────────────────
let mainWindow = null;
let tray = null;
let phpProcess = null;
let isQuitting = false;
let appPort = null;

// ─── Logging ──────────────────────────────────────────────────────────────────
function log(msg) {
    const timestamp = new Date().toISOString().substr(11, 8);
    console.log(`[Lupon ${timestamp}] ${msg}`);
}

function logError(msg) {
    const timestamp = new Date().toISOString().substr(11, 8);
    console.error(`[Lupon ${timestamp}] ERROR: ${msg}`);
}

// ─── Find Free Port ───────────────────────────────────────────────────────────
function findFreePort(startPort = 8000) {
    return new Promise((resolve, reject) => {
        const server = net.createServer();
        server.unref();
        server.on('error', (err) => {
            if (err.code === 'EADDRINUSE') {
                log(`Port ${startPort} is busy, trying ${startPort + 1}...`);
                resolve(findFreePort(startPort + 1));
            } else {
                reject(err);
            }
        });
        server.listen(startPort, '127.0.0.1', () => {
            const port = server.address().port;
            server.close(() => resolve(port));
        });
    });
}

// ─── Production Setup ─────────────────────────────────────────────────────────
function setupProductionEnv(port) {
    if (isDev) return;

    log('Setting up production environment...');
    log(`  rootPath: ${rootPath}`);
    log(`  userDataPath: ${userDataPath}`);
    log(`  dbPath: ${dbPath}`);
    log(`  storagePath: ${storagePath}`);
    log(`  phpBin: ${phpBin}`);

    // Verify PHP binary exists
    if (!fs.existsSync(phpBin)) {
        throw new Error(`PHP binary not found at: ${phpBin}`);
    }

    // Verify artisan exists
    const artisanPath = path.join(rootPath, 'artisan');
    if (!fs.existsSync(artisanPath)) {
        throw new Error(`Laravel artisan not found at: ${artisanPath}`);
    }

    // Ensure all writable dirs exist in AppData
    const dirs = [
        path.join(storagePath, 'framework', 'sessions'),
        path.join(storagePath, 'framework', 'cache', 'data'),
        path.join(storagePath, 'framework', 'views'),
        path.join(storagePath, 'logs'),
        path.join(storagePath, 'app', 'public'),
        path.join(storagePath, 'app', 'backups'),
        path.join(userDataPath, 'bootstrap', 'cache'),
    ];
    dirs.forEach(dir => {
        if (!fs.existsSync(dir)) {
            fs.mkdirSync(dir, { recursive: true });
            log(`  Created dir: ${dir}`);
        }
    });

    // Copy database on first run
    const sourceDb = path.join(rootPath, 'database', 'database.sqlite');
    if (!fs.existsSync(dbPath) && fs.existsSync(sourceDb)) {
        fs.copyFileSync(sourceDb, dbPath);
        log(`  Database copied to: ${dbPath}`);
    }

    // Generate or retrieve APP_KEY
    const fallbackEnvPath = path.join(userDataPath, '.env');
    let appKey = '';
    
    // Check if the fallback env path already has an APP_KEY
    if (fs.existsSync(fallbackEnvPath)) {
        const existingEnvVars = fs.readFileSync(fallbackEnvPath, 'utf8');
        const keyMatch = existingEnvVars.match(/^APP_KEY=(.*)$/m);
        if (keyMatch && keyMatch[1]) {
            appKey = keyMatch[1];
        }
    }
    
    // If no APP_KEY is found, generate a fresh random 32-byte base64 secure key
    if (!appKey) {
        log('Generating secure APP_KEY for first run...');
        appKey = 'base64:' + crypto.randomBytes(32).toString('base64');
    }

    // Write .env to the app directory (extraResources is writable in win-unpacked/installed)
    const envPath = path.join(rootPath, '.env');
    const envContent = [
        'APP_NAME=Lupon',
        'APP_ENV=production',
        `APP_KEY=${appKey}`,
        'APP_DEBUG=false',
        `APP_URL=http://127.0.0.1:${port}`,
        'DB_CONNECTION=sqlite',
        `DB_DATABASE=${dbPath.replace(/\\/g, '/')}`,
        'SESSION_DRIVER=file',
        `SESSION_FILES=${storagePath.replace(/\\/g, '/')}/framework/sessions`,
        'CACHE_STORE=file',
        `CACHE_STORE_PATH=${storagePath.replace(/\\/g, '/')}/framework/cache/data`,
        'LOG_CHANNEL=single',
        'LOG_LEVEL=error',
        `LOG_PATH=${storagePath.replace(/\\/g, '/')}/logs/laravel.log`,
        'QUEUE_CONNECTION=sync',
        'FILESYSTEM_DISK=local',
    ].join('\n');

    try {
        fs.writeFileSync(envPath, envContent);
        log('  .env written successfully');
    } catch (err) {
        // If we can't write to app dir (e.g. Program Files), write to userData
        const fallbackEnvPath = path.join(userDataPath, '.env');
        fs.writeFileSync(fallbackEnvPath, envContent);
        log(`  .env written to fallback: ${fallbackEnvPath}`);
        // Copy it into app dir if possible
        try {
            fs.copyFileSync(fallbackEnvPath, envPath);
        } catch (e) {
            logError(`Cannot write .env to app directory: ${e.message}`);
        }
    }

    log('Production environment ready.');
    return appKey;
}

// ─── Wait for Laravel to respond ──────────────────────────────────────────────
function waitForLaravel(port, retries = 40, delay = 500) {
    return new Promise((resolve, reject) => {
        const attempt = (remaining) => {
            log(`Waiting for Laravel on port ${port}... (${40 - remaining + 1}/40)`);
            http.get(`http://127.0.0.1:${port}`, (res) => {
                log(`Laravel responded with status ${res.statusCode}`);
                resolve();
            }).on('error', (err) => {
                if (remaining <= 0) {
                    reject(new Error(`Laravel server did not respond after 40 attempts. Last error: ${err.message}`));
                    return;
                }
                setTimeout(() => attempt(remaining - 1), delay);
            });
        };
        attempt(retries);
    });
}

// ─── Spawn Laravel ────────────────────────────────────────────────────────────
function startLaravel(port, appKey) {
    return new Promise((resolve, reject) => {
        log(`Starting Laravel: ${phpBin} artisan serve --port=${port} --host=127.0.0.1`);
        log(`Working directory: ${rootPath}`);

        let resolved = false;
        let phpStartOutput = '';

        phpProcess = spawn(phpBin, ['artisan', 'serve', `--port=${port}`, '--host=127.0.0.1', '--no-reload'], {
            cwd: rootPath,
            windowsHide: true,
            env: {
                ...process.env,
                APP_ENV: isDev ? 'local' : 'production',
                APP_DEBUG: isDev ? 'true' : 'false',
                DB_DATABASE: dbPath,
                ...(isDev ? {} : {
                    APP_KEY: appKey,
                    APP_STORAGE_PATH: storagePath,
                    SESSION_FILES: `${storagePath.replace(/\\/g, '/')}/framework/sessions`,
                    CACHE_STORE_PATH: `${storagePath.replace(/\\/g, '/')}/framework/cache/data`,
                    LOG_PATH: `${storagePath.replace(/\\/g, '/')}/logs/laravel.log`,
                }),
            },
        });

        phpProcess.stdout.on('data', (data) => {
            const msg = data.toString().trim();
            log(`[PHP stdout] ${msg}`);
            phpStartOutput += msg + '\n';
            if (!resolved && (msg.includes('started') || msg.includes('Development Server') || msg.includes('Server running'))) {
                resolved = true;
                resolve();
            }
        });

        phpProcess.stderr.on('data', (data) => {
            const msg = data.toString().trim();
            // artisan serve outputs normal request logs to stderr, only log errors
            if (msg.includes('Failed') || msg.includes('Error') || msg.includes('Exception')) {
                logError(`[PHP stderr] ${msg}`);
            } else {
                log(`[PHP stderr] ${msg}`);
            }
            phpStartOutput += msg + '\n';
        });

        phpProcess.on('error', (err) => {
            logError(`PHP process failed to spawn: ${err.message}`);
            if (!resolved) {
                resolved = true;
                reject(new Error(`PHP failed to start: ${err.message}`));
            }
        });

        phpProcess.on('exit', (code, signal) => {
            log(`PHP process exited with code ${code}, signal ${signal}`);
            if (!resolved && code !== 0) {
                resolved = true;
                reject(new Error(`PHP exited with code ${code}. Output:\n${phpStartOutput}`));
            }
            if (!isQuitting && resolved) {
                logError(`PHP process died unexpectedly (code ${code}). Attempting restart...`);
                // Auto-restart after 2 seconds if not quitting
                setTimeout(() => {
                    if (!isQuitting) {
                        startLaravel(port, appKey).catch(err => {
                            logError(`Restart failed: ${err.message}`);
                        });
                    }
                }, 2000);
            }
        });

        // Fallback: resolve after 8s even if no "started" message detected
        setTimeout(() => {
            if (!resolved) {
                log('Fallback timeout: Resolving startLaravel after 8s');
                resolved = true;
                resolve();
            }
        }, 8000);
    });
}

// ─── Create Window ────────────────────────────────────────────────────────────
function createWindow(port) {
    const iconPath = path.join(rootPath, 'public', 'images', 'bulalogo.ico');

    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        minWidth: 1024,
        minHeight: 600,
        show: false,
        title: 'Lupon — Barangay Justice System',
        icon: fs.existsSync(iconPath) ? iconPath : undefined,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            sandbox: true,
            preload: path.join(__dirname, 'preload.js'),
        },
    });

    const appUrl = `http://127.0.0.1:${port}`;
    log(`Loading URL: ${appUrl}`);
    mainWindow.loadURL(appUrl);

    // Security: Restrict navigation to strictly the local app URL
    mainWindow.webContents.on('will-navigate', (event, url) => {
        if (!url.startsWith(`http://127.0.0.1:${port}`)) {
            logError(`Blocked unauthorized external navigation attempt to: ${url}`);
            event.preventDefault();
        }
    });

    // Allow opening new windows ONLY if they are internal app URLs (for Reports and Previews)
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url.startsWith(`http://127.0.0.1:${port}`)) {
            log(`Allowing internal popup window for: ${url}`);
            return {
                action: 'allow',
                overrideBrowserWindowOptions: {
                    autoHideMenuBar: true,
                    width: 1024,
                    height: 800,
                    webPreferences: {
                        nodeIntegration: false,
                        contextIsolation: true,
                        // Share session with main window so authentication works
                        partition: mainWindow.webContents.session.name || 'persist:main'
                    }
                }
            };
        }
        logError(`Blocked unauthorized window creation attempt to: ${url}`);
        return { action: 'deny' };
    });

    mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription) => {
        logError(`Page failed to load: ${errorDescription} (code: ${errorCode})`);
        // Retry loading after a short delay
        setTimeout(() => {
            log('Retrying page load...');
            mainWindow.loadURL(appUrl);
        }, 2000);
    });

    mainWindow.once('ready-to-show', () => {
        log('Window ready-to-show, maximizing and displaying');
        mainWindow.maximize();
        mainWindow.show();
        mainWindow.focus();
        if (isDev) {
            mainWindow.webContents.openDevTools();
        }
    });

    // Fallback: show window after 15s even if ready-to-show hasn't fired
    setTimeout(() => {
        if (mainWindow && !mainWindow.isVisible()) {
            log('Fallback: Showing window after 15s timeout');
            mainWindow.maximize();
            mainWindow.show();
            mainWindow.focus();
        }
    }, 15000);

    // Minimize to tray instead of closing
    mainWindow.on('close', (e) => {
        if (!isQuitting) {
            e.preventDefault();
            mainWindow.hide();
        }
    });
}

// ─── System Tray ──────────────────────────────────────────────────────────────
function createTray() {
    const iconPath = path.join(rootPath, 'public', 'images', 'bulalogo.ico');
    let icon;

    try {
        icon = nativeImage.createFromPath(iconPath);
        if (icon.isEmpty()) {
            icon = nativeImage.createEmpty();
        }
    } catch (e) {
        log(`Tray icon not found at ${iconPath}, using empty icon`);
        icon = nativeImage.createEmpty();
    }

    tray = new Tray(icon);
    tray.setToolTip('Lupon — Barangay Justice System');

    const contextMenu = Menu.buildFromTemplate([
        {
            label: 'Show Lupon',
            click: () => {
                mainWindow?.show();
                mainWindow?.focus();
            },
        },
        { type: 'separator' },
        {
            label: 'Quit',
            click: () => {
                isQuitting = true;
                app.quit();
            },
        },
    ]);

    tray.setContextMenu(contextMenu);
    tray.on('double-click', () => {
        mainWindow?.show();
        mainWindow?.focus();
    });
}

// ─── Stop Servers ─────────────────────────────────────────────────────────────
function stopServers() {
    return new Promise((resolve) => {
        if (phpProcess && !phpProcess.killed) {
            log('Stopping PHP server...');
            treeKill(phpProcess.pid, 'SIGTERM', (err) => {
                if (err) {
                    log(`tree-kill warning: ${err.message}`);
                }
                phpProcess = null;
                resolve();
            });
            // Force-kill after 3 seconds if graceful shutdown fails
            setTimeout(() => {
                if (phpProcess) {
                    try { treeKill(phpProcess.pid, 'SIGKILL'); } catch (e) { }
                    phpProcess = null;
                }
                resolve();
            }, 3000);
        } else {
            resolve();
        }
    });
}

// ─── Show Fatal Error Dialog ──────────────────────────────────────────────────
function showFatalError(message) {
    logError(message);
    dialog.showErrorBox(
        'Lupon — Startup Error',
        `The application failed to start.\n\n${message}\n\nPlease try again or contact support.`
    );
}

// ─── App Lifecycle ────────────────────────────────────────────────────────────

// 0. Single Instance Lock (Must be called before whenReady)
const gotTheLock = app.requestSingleInstanceLock();

if (!gotTheLock) {
    log('Second instance detected. Quitting immediately.');
    app.quit();
} else {
    app.on('second-instance', (event, commandLine, workingDirectory) => {
        log('Second instance attempted to start. Refocusing primary window.');
        if (mainWindow) {
            if (mainWindow.isMinimized()) mainWindow.restore();
            mainWindow.show();
            mainWindow.focus();
        }
    });

    app.whenReady().then(async () => {
        try {
            log('=== Lupon Desktop Starting ===');
            log(`isDev: ${isDev}`);
            log(`isPackaged: ${app.isPackaged}`);

            // 0.5. Register IPC handlers
            ipcMain.handle('open-file', async (event, filePath) => {
                if (!filePath || typeof filePath !== 'string') {
                    return { error: 'Invalid file path.' };
                }
                if (!fs.existsSync(filePath)) {
                    return { error: `File not found: ${filePath}` };
                }
                const errorMsg = await shell.openPath(filePath);
                return errorMsg ? { error: errorMsg } : { success: true };
            });

            // 1. Find a free port
            appPort = await findFreePort(8000);
            log(`Using port: ${appPort}`);

            // 2. Setup environment
            const appKey = setupProductionEnv(appPort);

            // 3. Start Laravel
            await startLaravel(appPort, appKey);
            log('Laravel process spawned successfully');

            // 4. Wait for Laravel to respond to HTTP
            await waitForLaravel(appPort);
            log('Laravel is responding to HTTP requests');

            // 5. Create window and tray
            createWindow(appPort);
            createTray();
            log('Window and tray created');

        } catch (err) {
            showFatalError(err.message);
            await stopServers();
            app.exit(1);
        }
    });

    app.on('before-quit', () => {
        isQuitting = true;
    });

    app.on('will-quit', async (e) => {
        e.preventDefault();
        await stopServers();
        log('=== Lupon Desktop Stopped ===');
        app.exit(0);
    });

    app.on('window-all-closed', () => {
        // Keep app running in tray on Windows — do not quit
    });

    app.on('activate', () => {
        mainWindow?.show();
    });
}
