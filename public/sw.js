self.addEventListener('install', (e) => {
    console.log('Service Worker: Installed');
});

self.addEventListener('activate', (e) => {
    console.log('Service Worker: Activated');
});

self.addEventListener('fetch', (e) => {
    // You can add caching later
});