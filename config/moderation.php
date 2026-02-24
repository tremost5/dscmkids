<?php

return [
    'blocked_words' => array_filter(array_map('trim', explode(',', (string) env(
        'MODERATION_BLOCKED_WORDS',
        'anjing,bodoh,goblok,asu,kasar'
    )))),
];

