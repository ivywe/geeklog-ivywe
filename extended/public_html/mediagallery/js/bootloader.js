/* boot loader */
if (using_jquery) {
    // when using jQuery
    $(window).load(function() {
        if (typeof window['boot'] == 'function') boot();
    });
} else {
    // when using MooTools
    window.addEvent('load', function() {
        if (typeof window['boot'] == 'function') boot();
    });
}
