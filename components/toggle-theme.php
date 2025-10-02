<?php
?>
<div class="fixed top-4 right-4 z-50">
    <button id="themeToggle" class="p-2 rounded-lg bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
        <span class="dark:hidden">🌙</span>
        <span class="hidden dark:inline">☀️</span>
    </button>
</div>

<script>
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark');
} else {
    html.classList.remove('dark');
}

themeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
});
</script>