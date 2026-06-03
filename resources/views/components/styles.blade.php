{{-- Shared styles for pill inputs and buttons --}}
<style>
    /* ── Pill input ── */
    .pill-input {
        background: rgba(237,230,255,0.55);
        border: none;
        border-radius: 50px;
        padding: 16px 20px 16px 52px;
        font-size: 14px;
        font-weight: 600;
        color: #3D1F7A;
        width: 100%;
        outline: none;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .pill-input::placeholder { color: #B39DDB; font-weight: 500; }
    .pill-input:focus {
        background: rgba(237,230,255,0.9);
        box-shadow: 0 0 0 3px rgba(139,70,211,0.20);
    }

    /* Badge styles */
    .badge-available { background: #DCFCE7; color: #166534; }
    .badge-hired { background: #FEF3C7; color: #B45309; }

    /* Menu card hover */
    .menu-card { transition: transform 0.15s ease; }
    .menu-card:active { transform: scale(0.97); }

    /* Nanny card hover */
    .nanny-card { transition: transform .15s ease; }
    .nanny-card:active { transform: scale(0.98); }

    /* Float animation for empty states */
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }
</style>
