// Dev mode utility - secure dev feature gate
(function() {
    const SECRET = 'alphakids-dev-2024-secure';
    const DEV_KEY = '_ak_cfg';
    const EXPIRY_DAYS = 7;

    function verifyToken(token) {
        if (!token) return false;

        const parts = token.split(':');
        if (parts.length !== 2) return false;

        const [timestamp, hash] = parts;
        const expected = btoa(`${SECRET}:${timestamp}`);

        // Check hash valid
        if (hash !== expected) return false;

        // Check expiry
        const age = Date.now() - parseInt(timestamp);
        const maxAge = EXPIRY_DAYS * 24 * 60 * 60 * 1000;

        return age >= 0 && age < maxAge;
    }

    // Public API - no enable() method, only via one-liner
    window.DevMode = {
        isActive: function() {
            try {
                const token = localStorage.getItem(DEV_KEY);
                return verifyToken(token);
            } catch (e) {
                return false;
            }
        },

        disable: function() {
            try {
                localStorage.removeItem(DEV_KEY);
                console.log('%c✗ Dev mode disabled', 'color: #DC2626; font-weight: bold;');
                return true;
            } catch (e) {
                return false;
            }
        },

        status: function() {
            const active = this.isActive();
            console.log(`Dev mode: ${active ? '✓ ACTIVE' : '✗ INACTIVE'}`);

            if (active) {
                try {
                    const token = localStorage.getItem(DEV_KEY);
                    const timestamp = parseInt(token.split(':')[0]);
                    const age = Date.now() - timestamp;
                    const daysLeft = Math.ceil((EXPIRY_DAYS * 24 * 60 * 60 * 1000 - age) / (24 * 60 * 60 * 1000));
                    console.log(`Expires in ${daysLeft} day${daysLeft !== 1 ? 's' : ''}`);
                } catch (e) {}
            }

            return active;
        }
    };
})();
