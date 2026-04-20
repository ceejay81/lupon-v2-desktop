const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electron', {
    platform: process.platform,
    version: process.versions.electron,

    /**
     * Open a file with the system's default application.
     * Called from the document manager when the user clicks "Open in App".
     *
     * @param {string} filePath - Absolute path to the file on disk.
     * @returns {Promise<{success?: boolean, error?: string}>}
     */
    openFile: (filePath) => ipcRenderer.invoke('open-file', filePath),
});
