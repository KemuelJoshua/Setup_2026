<?php

test('startup tracking dialog aligns tall content to the top of its scroll viewport', function () {
    $component = file_get_contents(
        dirname(__DIR__, 2).'/resources/js/modules/startup-tracking/components/StartupTrackingViewDialog.vue',
    );

    expect($component)
        ->not->toBeFalse()
        ->toContain('class="max-w-5xl self-start');
});
