Pre‑Conversion Review Questions for Your AI Agent
🔍 Part 1 – Asset & Path Hardening (Critical for .exe)
Are any asset URLs hardcoded with http:// or https://?

Search all Blade/PHP files for http://, https://, asset(), secure_asset(), url(), route().

List any that would break when the app runs on a file:// protocol or a local http://localhost:8000 without internet.

Does the application rely on public_path() or storage_path() that may change in the .exe environment?

Identify every usage of public_path(), storage_path(), base_path().

For each, explain whether the path will still resolve correctly inside a packaged executable (e.g., relative to the executable’s location).

How are CSS/JS assets compiled and loaded?

Verify that Vite’s build output is used (not npm run dev).

Check that the layout files use @vite() helper or fallback to precompiled assets.

Are there any inline <script> or <link> tags pointing to external CDNs? List them.

Are there any environment‑specific config values that must be overridden for the .exe?

Examine .env and config/app.php, config/database.php.

Which variables assume a web server environment (e.g., APP_URL, ASSET_URL, SESSION_DOMAIN)?

Propose a hardened .env for desktop (e.g., APP_URL=, SESSION_DRIVER=file, LOG_CHANNEL=single).

🗄️ Part 2 – Database & Offline Readiness
Is the system ready to run on a local (non‑network) database?

Currently using MySQL. For a desktop .exe, recommend switching to SQLite or MariaDB embedded.

Have the agent scan for any MySQL‑specific features (stored procedures, full‑text indexes, foreign key constraints) that would break in SQLite.

Provide a migration plan to SQLite if needed.

Where are uploaded files (documents, signatures, watermarks) stored?

List all directories used for file storage (e.g., storage/app/public, custom upload paths).

Are these paths relative or absolute? Will they be writable in a Windows Program Files environment?

Suggest a writable location (e.g., %APPDATA%\Luponv2 or Documents\Luponv2Data) and how to configure it.

Does any part of the app assume an active internet connection?

Search for Http::, curl, file_get_contents('http'), Guzzle, or external API calls (e.g., maps, SMS, email).

If any exist, list them and recommend offline fallbacks or removal.

🖨️ Part 3 – Print & PDF (The Watermark & Layout Fixes)
Have the print CSS changes been verified in a headless environment?

The watermark fix uses @media print { min-height: 14in; overflow: visible; }.

Does the PDF generation library (if any) respect @media print? (e.g., DomPDF does not; Browsershot does).

Identify which library is used for PDF export (search for PDF::, Snappy, Browsershot, barryvdh/laravel-dompdf).

Is the .no-print utility applied to all dynamic UI elements (toasts, modals, alerts)?

Search for Livewire toast components, modal wrappers, and any x-show elements.

Verify that their parent containers (or the elements themselves) have the no-print class.

Test scenario: print while a toast is visible – does it appear on paper?

Does the watermark appear correctly on every page when a document is multi‑page?

The current watermark is absolute‑positioned relative to .paper-wrapper.

In print, min-height: 14in ensures first page is tall enough. But what about page 2, 3, etc.?

Does the watermark repeat on subsequent pages? If not, should it? (Often seals appear only on first page – acceptable.)

🧩 Part 4 – Navigation & History (Desktop Shell Integration)
Does the app rely heavily on window.history or back() buttons?

In a desktop .exe (e.g., Electron, NativePHP), the back button may behave differently.

Search for window.history, back(), pushState, replaceState, and any Livewire navigation.

Propose a navigation strategy that works without browser history (e.g., always use Livewire’s wire:navigate or custom navigation).

Are there any external links that would open a system browser?

Search for target="_blank", window.open, href="http".

For user‑friendly desktop behavior, external links should open the default browser, not break the app.

🔧 Part 5 – Build & Packaging Validation
What is the exact command to produce the production build for the .exe?

Provide a step‑by‑step checklist: composer install --no-dev, npm run build, environment hardening, database seeding, etc.

Which files/directories should be excluded from the final package (tests, node_modules, .git, etc.)?

Has the application been tested with a local web server (e.g., Laragon, XAMPP) simulating the .exe environment?

Suggest a simple smoke test: run php artisan serve --host=127.0.0.1 --port=8080 and verify all features.

Ask the agent to list any known issues when the app is not served from the root of a domain.

Are there any Windows‑specific file permission or path length issues?

Maximum path length on Windows is 260 characters. Identify any long dynamic paths (e.g., storage/logs, archive snapshots).

Suggest shortening or using \\?\ prefix if needed.

🧪 Part 6 – Final Validation Checklist for Your Agent
After answering the above, ask the agent to execute this final review:

“Run the following validation steps against the codebase and report results:

Simulate a desktop environment by setting APP_ENV=desktop and APP_URL= (empty).

Run php artisan config:clear and php artisan view:clear.

Open every layout file and confirm no external CDNs are loaded.

Open the monthly transmittal report and a summon, then simulate print (via window.print() or headless). Capture the computed height of .paper-wrapper and ensure watermark is fully visible.

Generate a PDF (if supported) and verify the watermark is not clipped.

Create a new complaint, save it, and restart the application – verify data persists.”