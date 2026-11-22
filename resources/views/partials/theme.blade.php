<!-- Light / dark theme: toggled by the sun/moon button, remembered in localStorage -->
<style>
    .theme-dark-only { display: none; }
    .dark .theme-dark-only { display: block; }
    .dark .theme-light-only { display: none; }

    .dark body { background-color: #180b0d !important; color: #f0e2d1 !important; }
    .dark .bg-white { background-color: #241014 !important; }
    .dark .bg-\[\#fbf6ec\] { background-color: #180b0d !important; }
    .dark .bg-\[\#fbf6ec\]\/90 { background-color: rgba(24, 11, 13, 0.9) !important; }
    .dark .bg-\[\#f6ecd9\] { background-color: #3a1b21 !important; }
    .dark .bg-\[\#f7f1e6\] { background-color: #180b0d !important; }
    .dark .hover\:bg-\[\#f6ecd9\]:hover { background-color: #3a1b21 !important; }
    .dark .hover\:bg-\[\#fbf6ec\]:hover { background-color: #241014 !important; }

    .dark .text-\[\#5a1f28\] { color: #f0e2d1 !important; }
    .dark .text-\[\#75323d\] { color: #ddc7b6 !important; }
    .dark .text-\[\#75323d\]\/50 { color: rgba(221, 199, 182, 0.5) !important; }
    .dark .text-\[\#75323d\]\/60 { color: rgba(221, 199, 182, 0.6) !important; }
    .dark .text-\[\#75323d\]\/70 { color: rgba(221, 199, 182, 0.7) !important; }
    .dark .text-\[\#75323d\]\/75 { color: rgba(221, 199, 182, 0.75) !important; }
    .dark .text-\[\#75323d\]\/80 { color: rgba(221, 199, 182, 0.8) !important; }
    .dark .text-\[\#75323d\]\/85 { color: rgba(221, 199, 182, 0.85) !important; }

    .dark .border-\[\#e9dcc2\] { border-color: #4a232b !important; }
    .dark .border-\[\#f6ecd9\] { border-color: #3d1c23 !important; }
    .dark .divide-\[\#f6ecd9\] > :not([hidden]) ~ :not([hidden]) { border-color: #3d1c23 !important; }

    .dark input, .dark select, .dark textarea { background-color: #241014 !important; color: #f0e2d1 !important; border-color: #4a232b !important; }
    .dark input::placeholder, .dark textarea::placeholder { color: rgba(221, 199, 182, 0.4); }
    .dark select option { background-color: #241014; color: #f0e2d1; }

    .dark .bg-emerald-50 { background-color: #0c2418 !important; }
    .dark .text-emerald-900 { color: #a7f3d0 !important; }
    .dark .border-emerald-200 { border-color: #14532d !important; }
    .dark .text-emerald-700 { color: #6ee7b7 !important; }
    .dark .bg-emerald-100 { background-color: #064e3b !important; }
    .dark .text-emerald-800 { color: #a7f3d0 !important; }
    .dark .bg-rose-50 { background-color: #2a1215 !important; }
    .dark .text-rose-900 { color: #fecdd3 !important; }
    .dark .border-rose-200 { border-color: #881337 !important; }
    .dark .text-rose-700 { color: #fda4af !important; }
    .dark .hover\:bg-rose-50:hover { background-color: #2a1215 !important; }
    .dark .bg-stone-200 { background-color: #3a3733 !important; }
    .dark .text-stone-600 { color: #d6d3d1 !important; }
</style>
<script>
    (function () {
        try {
            var stored = localStorage.getItem('dlr-theme');
            var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (dark) { document.documentElement.classList.add('dark'); }
        } catch (e) { /* ignore */ }
    })();

    function dlrApplyThemeIcons() {
        var dark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('[data-theme-icon]').forEach(function (el) { el.textContent = dark ? '\u2600\uFE0F' : '\uD83C\uDF19'; });
        document.querySelectorAll('[data-theme-label]').forEach(function (el) { el.textContent = dark ? 'Light mode' : 'Dark mode'; });
    }

    function dlrToggleTheme() {
        var dark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('dlr-theme', dark ? 'dark' : 'light'); } catch (e) { /* ignore */ }
        dlrApplyThemeIcons();
    }

    document.addEventListener('DOMContentLoaded', dlrApplyThemeIcons);
</script>
