import './bootstrap';
import './hearing-calendar';

import Alpine from 'alpinejs';

// Only start Alpine manually if Livewire hasn't already started it.
// On pages with Livewire components, Livewire manages Alpine.
// On pages without Livewire (e.g. folderized-reports), we start it here.
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Alpine) {
        window.Alpine = Alpine;
        Alpine.start();
    }
});
