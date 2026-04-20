/**
 * Luponv2 Document Editor Toolbar & Logic
 * Handles rich text editing, auto-saving, and navigation safety.
 */

// Global State
let isDirty = false;
let autoSaveTimeout = null;
const AUTO_SAVE_DELAY = 15000; // 15 seconds

document.addEventListener("DOMContentLoaded", function () {
    // 1. Inject the HTML Ribbon at the top of the body
    const ribbonHTML = `
    <div class="ribbon" id="toolbar">
        <!-- Font Group -->
        <div class="ribbon-group">
            <div class="group-content">
                <select style="width: 120px;" onchange="cmd('fontName', this.value); this.blur();" title="Font">
                    <option value="Arial">Arial</option>
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Calibri">Calibri</option>
                    <option value="Courier New">Courier New</option>
                    <option value="Georgia">Georgia</option>
                </select>
                <select style="width: 50px;" onchange="cmd('fontSize', this.value); this.blur();" title="Font Size">
                    <option value="1">8</option>
                    <option value="2">10</option>
                    <option value="3" selected>12</option>
                    <option value="4">14</option>
                    <option value="5">18</option>
                    <option value="6">24</option>
                    <option value="7">36</option>
                </select>
                <button onclick="cmd('increaseFontSize')" title="Increase Font Size">A&#8593;</button>
                <button onclick="cmd('decreaseFontSize')" title="Decrease Font Size">A&#8595;</button>
            </div>
            <div class="group-content" style="margin-top: 4px;">
                <button id="btn-bold" onclick="cmd('bold')" title="Bold (Ctrl+B)"><b>B</b></button>
                <button id="btn-italic" onclick="cmd('italic')" title="Italic (Ctrl+I)"><i>I</i></button>
                <button id="btn-underline" onclick="cmd('underline')" title="Underline (Ctrl+U)"><u>U</u></button>
                <button id="btn-strikeThrough" onclick="cmd('strikeThrough')" title="Strikethrough"><s>ab</s></button>
                <button onclick="cmd('subscript')" title="Subscript">x&#8322;</button>
                <button onclick="cmd('superscript')" title="Superscript">x&#178;</button>
                <div style="width:1px; height:20px; background:#444; margin:0 4px;"></div>
                
                <div style="display: flex; align-items: center; gap: 2px;">
                    <span style="color:#aaa; font-size:12px; font-weight:bold; padding-bottom:2px;">A</span>
                    <div style="height:2px; width:12px; background:red; margin-top:-2px; margin-right:4px;"></div>
                    <input type="color" class="color-picker" value="#000000" title="Font Color" oninput="cmd('foreColor', this.value)">
                </div>
            </div>
            <div class="group-title">Font</div>
        </div>

        <!-- Paragraph Group -->
        <div class="ribbon-group">
            <div class="group-content">
                <button onclick="cmd('insertUnorderedList')" title="Bullets">&#9776;&#8226;</button>
                <button onclick="cmd('insertOrderedList')" title="Numbering">&#9776;&#185;</button>
                <div style="width:1px; height:20px; background:#444; margin:0 4px;"></div>
                <button onclick="cmd('outdent')" title="Decrease Indent">&#8676;</button>
                <button onclick="cmd('indent')" title="Increase Indent">&#8677;</button>
            </div>
            <div class="group-content" style="margin-top: 4px;">
                <button id="btn-justifyLeft" onclick="cmd('justifyLeft')" title="Align Left">
                    <div class="icon-align icon-left"><span></span><span></span><span></span><span></span></div>
                </button>
                <button id="btn-justifyCenter" onclick="cmd('justifyCenter')" title="Center">
                    <div class="icon-align icon-center"><span></span><span></span><span></span><span></span></div>
                </button>
                <button id="btn-justifyRight" onclick="cmd('justifyRight')" title="Align Right">
                    <div class="icon-align icon-right"><span></span><span></span><span></span><span></span></div>
                </button>
                <button id="btn-justifyFull" onclick="cmd('justifyFull')" title="Justify">
                    <div class="icon-align icon-justify"><span></span><span></span><span></span><span></span></div>
                </button>
            </div>
            <div class="group-title">Paragraph</div>
        </div>

        <!-- Insert / Document Group -->
        <div class="ribbon-group">
            <div class="group-content">
                ${window.BRANDING_UNLOCKED ? `
                <div class="upload-btn-wrapper" title="Change Header Image">
                    <button>🖼 Header</button>
                    <input type="file" onchange="previewImage(this, 'header-img')" accept="image/*" />
                </div>
                <div class="upload-btn-wrapper" title="Change Watermark Image">
                    <button>💧 Watermark</button>
                    <input type="file" onchange="previewImage(this, 'watermark-img')" accept="image/*" />
                </div>
                ` : ''}
            </div>
            <div class="group-content" style="margin-top: 4px; justify-content: center;">
                <button onclick="cmd('undo')" title="Undo (Ctrl+Z)">&#8630;</button>
                <button onclick="cmd('redo')" title="Redo (Ctrl+Y)">&#8631;</button>
                <button id="btn-save-case" onclick="saveDocumentChanges()" title="Save Changes to Database" style="background:#16a34a; border-color:#15803d; color:white; margin-left: 10px;">💾 Save Document</button>
                <button onclick="window.print()" title="Print" style="background:#0078d4; border-color:#005a9e; margin-left: 5px;">🖨 Print</button>
            </div>
            <div class="group-title">Document</div>
        </div>

        <div id="save-status" style="position: absolute; right: 10px; bottom: 5px; font-size: 11px; color: #888; font-style: italic;">
            All changes saved
        </div>
    </div>
    `;

    // Prepend the ribbon to the body
    document.body.insertAdjacentHTML('afterbegin', ribbonHTML);

    // 2. Attach Event Listeners for Toolbar State
    document.addEventListener('selectionchange', updateActiveStates);

    // 3. Setup Editor and Auto-save
    const docBody = document.getElementById('doc-body');
    if (docBody) {
        // Double-click to type anywhere (like MS Word)
        docBody.addEventListener('dblclick', function (e) {
            if (e.target !== docBody) return;
            const rect = docBody.getBoundingClientRect();
            const y = e.clientY - rect.top;
            const x = e.clientX - rect.left;

            const newBlock = document.createElement('div');
            newBlock.style.position = 'absolute';
            newBlock.style.top = y + 'px';
            newBlock.style.left = x + 'px';
            newBlock.style.minWidth = '50px';
            newBlock.style.minHeight = '1.2em';
            newBlock.innerHTML = '&nbsp;';
            docBody.appendChild(newBlock);

            const sel = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(newBlock);
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
            newBlock.focus();

            markDirty();
        });

        // Input listener to detect changes
        docBody.addEventListener('input', function () {
            markDirty();
        });
    }

    // 4. Navigation warning
    window.addEventListener('beforeunload', function (e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = ''; // Standard way to trigger the confirmation
        }
    });
});

// Run execCommand and keep focus
window.cmd = function (command, value = null) {
    document.execCommand(command, false, value);
    const editable = document.getElementById('doc-body');
    if (editable) {
        editable.focus();
        markDirty();
    }
};

// Highlight active buttons
window.updateActiveStates = function () {
    const toggleCmds = ['bold', 'italic', 'underline', 'strikeThrough',
        'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'];
    toggleCmds.forEach(function (c) {
        const btn = document.getElementById('btn-' + c);
        if (btn) btn.classList.toggle('active', document.queryCommandState(c));
    });
};

// Image Preview Logic
window.previewImage = function (input, imgId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(imgId).src = e.target.result;
            markDirty();
        }
        reader.readAsDataURL(input.files[0]);
    }
};

// Save Changes Logic (Handles both Cases and Reports)
window.saveDocumentChanges = function (isAuto = false) {
    const btn = document.getElementById('btn-save-case');
    const originalText = btn ? btn.innerHTML : "💾 Save Document";
    const saveStatus = document.getElementById('save-status');

    // Collect the entire HTML body instead of just fields
    const docBody = document.getElementById('doc-body');
    if (!docBody) return;

    const content = docBody.innerHTML;

    if (!isAuto && btn) {
        // Visual feedback for manual save
        btn.innerHTML = "⏳ Saving...";
        btn.disabled = true;
    } else {
        if (saveStatus) saveStatus.innerHTML = "<i>Autosaving...</i>";
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const payload = {
        content: content,
        type: window.DOCUMENT_TYPE,
        case_id: window.CASE_ID,
        month: window.REPORT_MONTH,
        year: window.REPORT_YEAR
    };

    return fetch(window.SAVE_ROUTE, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isDirty = false;
                if (!isAuto && btn) {
                    btn.innerHTML = "✅ Saved!";
                    btn.style.background = "#059669";
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = "#16a34a";
                        btn.disabled = false;
                    }, 2000);
                } else {
                    if (saveStatus) {
                        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        saveStatus.innerHTML = "Last autosaved at " + time;
                    }
                }
            } else {
                throw new Error(data.message || 'Saving failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (!isAuto && btn) {
                alert('Error saving changes: ' + error.message);
                btn.innerHTML = "❌ Error";
                btn.style.background = "#dc2626";
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = "#16a34a";
                    btn.disabled = false;
                }, 3000);
            } else {
                if (saveStatus) saveStatus.innerHTML = "<span style='color: #ef4444;'>Autosave failed</span>";
            }
        });
};

function markDirty() {
    isDirty = true;
    const saveStatus = document.getElementById('save-status');
    if (saveStatus) saveStatus.innerHTML = "<i>Unsaved changes...</i>";

    // Clear existing timeout
    if (autoSaveTimeout) clearTimeout(autoSaveTimeout);

    // Set new timeout for auto-save
    autoSaveTimeout = setTimeout(() => {
        if (isDirty) {
            saveDocumentChanges(true);
        }
    }, AUTO_SAVE_DELAY);
}
