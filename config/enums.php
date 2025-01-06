<?php
    declare( strict_types = 1 );
    return [
        'ticket_status' => [
            'open' => 'OPEN',
            'in-progress' => 'IN PROGRESS',
            'awaiting-acceptance' => 'AWAITING ACCEPTANCE',
            'escalated' => 'ESCALATED',
            'resolved' => 'RESOLVED',
        ],
        'ticket_priority' => [
            'low' => 'LOW',
            'medium' => 'MEDIUM',
            'high' => 'HIGH',
        ],
        'priority_timeout' => [
            'low' => 8,
            'medium' => 4,
            'high' => 2,
        ],
    ];