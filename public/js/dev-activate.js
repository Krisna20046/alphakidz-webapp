// Dev mode activator - DO NOT include in production
// This file only shows the activation command, does not expose easy enable method

(function() {
    const activationCommand = `(function(){const s='alphakids-dev-2024-secure',k='_ak_cfg',t=Date.now(),h=btoa(s+':'+t);localStorage.setItem(k,t+':'+h);console.log('%c✓ Dev mode enabled','color:#16A34A;font-weight:bold');location.reload()})()`;

    console.log('%c━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'color: #8B46D3');
    console.log('%c Alpha Kids Dev Mode', 'color: #8B46D3; font-size: 14px; font-weight: bold');
    console.log('%c━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'color: #8B46D3');
    console.log('');
    console.log('%cActivation (copy & paste full command):', 'font-weight: bold');
    console.log(activationCommand);
    console.log('');
    console.log('%cDisable:', 'font-weight: bold');
    console.log('  DevMode.disable()');
    console.log('');
    console.log('%cCheck status:', 'font-weight: bold');
    console.log('  DevMode.status()');
    console.log('');
    console.log('%c━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'color: #8B46D3');

    // Auto-check status on load
    if (window.DevMode) {
        setTimeout(() => {
            console.log('');
            window.DevMode.status();
        }, 100);
    }
})();
