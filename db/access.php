<?php
$capabilities = [
    'local/courseanalytics:courseanalytics' => [
        'riskbitmask' => RISK_SPAM,         // associated risks
        'captype' => 'write',               // read|write capability
        'contextlevel' => CONTEXT_SYSTEM,   // declares the typical context level wher this capability is checked
        'archetypes' => [                   // specifies defaults for roles with standard archetypes.
            'manager' => CAP_ALLOW,
        ],
    ],
];