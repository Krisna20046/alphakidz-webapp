{{--
    resources/views/partials/api-cache.blade.php
    Client-side cache untuk mengurangi API request saat ganti halaman.
    Include di layout sebelum @stack('scripts') atau di <head>.

    Usage:
        // Simpan data ke cache
        window.apiCache.set('reminders', data, 5 * 60 * 1000);

        // Baca dari cache
        const cached = window.apiCache.get('reminders');
        if (cached) { render(cached); return; }

        // Hapus satu key
        window.apiCache.delete('reminders');

        // Hapus semua cache dengan prefix
        window.apiCache.clearByPrefix('chat_');

        // Hapus semua cache
        window.apiCache.clear();

        // Ambil & fetch jika tidak ada cache (helper)
        const data = await window.apiCache.fetch('/api/chat-list', {
            ttl: 60 * 1000,
            headers: { 'Authorization': 'Bearer ' + token }
        });
--}}

<script>
(function() {
'use strict';

const PREFIX = 'ak_cache_';

/**
 * apiCache — in-memory + localStorage hybrid cache
 *
 * Strategi:
 *   - In-memory Map untuk akses super cepat selama page load
 *   - localStorage untuk persist antar halaman (karena setiap ganti halaman = reload)
 *   - TTL (time-to-live) untuk memastikan data tidak basi
 *   - Prefix 'ak_cache_' untuk menghindari bentrok dengan key lain di localStorage
 *
 * Setiap entry:
 *   { data: any, timestamp: number, ttl: number }
 */
class ApiCache {
    constructor() {
        this._memory = new Map();
        this._keys = null; // lazy load dari localStorage
    }

    // ── Public API ──────────────────────────────────────────────────────────

    /**
     * Ambil data dari cache (memory dulu, baru localStorage)
     * @param {string} key
     * @returns {any|null} — null jika tidak ada atau expired
     */
    get(key) {
        const fullKey = PREFIX + key;

        // Cek memory dulu (super cepat)
        if (this._memory.has(fullKey)) {
            const entry = this._memory.get(fullKey);
            if (!this._isExpired(entry)) {
                return entry.data;
            }
            this._memory.delete(fullKey);
        }

        // Cek localStorage
        try {
            const raw = localStorage.getItem(fullKey);
            if (raw) {
                const entry = JSON.parse(raw);
                if (!this._isExpired(entry)) {
                    // Simpan ke memory untuk akses berikutnya
                    this._memory.set(fullKey, entry);
                    return entry.data;
                }
                // Expired — hapus
                localStorage.removeItem(fullKey);
            }
        } catch (e) {
            // localStorage error (quota, dll) — silent
        }

        return null;
    }

    /**
     * Simpan data ke cache
     * @param {string} key
     * @param {any} data
     * @param {number} ttl — milliseconds (default 5 menit)
     */
    set(key, data, ttl) {
        const fullKey = PREFIX + key;
        const entry = {
            data: data,
            timestamp: Date.now(),
            ttl: ttl || (5 * 60 * 1000), // default 5 menit
        };

        // Simpan ke memory
        this._memory.set(fullKey, entry);

        // Simpan ke localStorage (try-catch untuk Private Browsing)
        try {
            localStorage.setItem(fullKey, JSON.stringify(entry));
        } catch (e) {
            // Quota exceeded — cleanup 50% oldest entries
            if (e.name === 'QuotaExceededError' || e.code === 22) {
                this._cleanup(0.5);
                try {
                    localStorage.setItem(fullKey, JSON.stringify(entry));
                } catch (_) { /* give up */ }
            }
        }
    }

    /**
     * Hapus satu key dari cache
     * @param {string} key
     */
    delete(key) {
        const fullKey = PREFIX + key;
        this._memory.delete(fullKey);
        try { localStorage.removeItem(fullKey); } catch (_) {}
    }

    /**
     * Hapus semua cache yang key-nya diawali prefix tertentu
     * Contoh: clearByPrefix('chat_') hapus chat_list, chat_room_5, dll
     * @param {string} prefix
     */
    clearByPrefix(prefix) {
        const fullPrefix = PREFIX + prefix;

        // Hapus dari memory
        for (const key of this._memory.keys()) {
            if (key.startsWith(fullPrefix)) {
                this._memory.delete(key);
            }
        }

        // Hapus dari localStorage
        try {
            const toRemove = [];
            for (let i = 0; i < localStorage.length; i++) {
                const k = localStorage.key(i);
                if (k && k.startsWith(fullPrefix)) {
                    toRemove.push(k);
                }
            }
            toRemove.forEach(k => localStorage.removeItem(k));
        } catch (_) {}
    }

    /**
     * Hapus SEMUA cache aplikasi (termasuk semua prefix)
     */
    clear() {
        this._memory.clear();
        try {
            const toRemove = [];
            for (let i = 0; i < localStorage.length; i++) {
                const k = localStorage.key(i);
                if (k && k.startsWith(PREFIX)) {
                    toRemove.push(k);
                }
            }
            toRemove.forEach(k => localStorage.removeItem(k));
        } catch (_) {}
    }

    /**
     * Helper: ambil dari cache, kalau tidak ada → fetch → simpan ke cache → return
     * @param {string|Request} input — URL atau Request object
     * @param {object} options
     * @param {number} options.ttl — TTL dalam ms (default 5 menit)
     * @param {object} options.headers — custom headers
     * @param {boolean} options.skipCache — force fetch, skip cache
     * @param {string} options.cacheKey — custom cache key (default: URL)
     * @returns {Promise<any>} response data (JSON parsed)
     */
    async fetch(input, options = {}) {
        const url = typeof input === 'string' ? input : input.url;
        const cacheKey = options.cacheKey || url;

        // Coba dari cache (kecuali skipCache=true)
        if (!options.skipCache) {
            const cached = this.get(cacheKey);
            if (cached !== null) {
                return cached;
            }
        }

        // Fetch dari network
        const headers = { 'Accept': 'application/json', ...options.headers };
        const res = await fetch(input, { headers });
        const data = await res.json();

        // Simpan ke cache kalau response sukses
        if (res.ok && data && (data.success !== false)) {
            this.set(cacheKey, data, options.ttl);
        }

        return data;
    }

    // ── Internal ────────────────────────────────────────────────────────────

    _isExpired(entry) {
        return (Date.now() - entry.timestamp) > entry.ttl;
    }

    _cleanup(fraction) {
        try {
            const entries = [];
            for (let i = 0; i < localStorage.length; i++) {
                const k = localStorage.key(i);
                if (k && k.startsWith(PREFIX)) {
                    try {
                        const v = JSON.parse(localStorage.getItem(k));
                        entries.push({ key: k, timestamp: v.timestamp || 0 });
                    } catch (_) {
                        entries.push({ key: k, timestamp: 0 });
                    }
                }
            }
            // Urutkan dari paling lama
            entries.sort((a, b) => a.timestamp - b.timestamp);
            const removeCount = Math.ceil(entries.length * fraction);
            entries.slice(0, removeCount).forEach(e => localStorage.removeItem(e.key));
        } catch (_) {}
    }
}

// Expose ke global
window.apiCache = new ApiCache();

})();
</script>
