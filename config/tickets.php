<?php
    declare( strict_types = 1 );

    return [
        'groupings' => [
            'index' => [
                [
                    'title' => 'Working On It',
                    'status' => [ 'in-progress', 'awaiting-acceptance' ],
                    'assignee_required' => true,
                    'no_tickets_msg' => 'You Are Not Currently Working on Any Tickets'
                ],
                [
                    'title' => 'Un-Assigned Tickets',
                    'status' => [ 'open' ],
                    'assignee_required' => false,
                    'no_tickets_msg' => 'There are No Un-Assigned Tickets'
                ],
                [
                    'title' => 'Resolved It',
                    'status' => [ 'resolved' ],
                    'assignee_required' => true,
                    'no_tickets_msg' => 'You did NOT Resolve Any Tickets Yet'
                ],
                [
                    'title' => 'Resolved',
                    'status' => [ 'resolved' ],
                    'assignee_required' => false,
                    'no_tickets_msg' => 'There are No Resolved Tickets'
                ]
            ],
            'my_tickets' => [
                [
                    'title' => 'Pending Action',
                    'status' => [ 'awaiting-acceptance' ],
                    'no_tickets_msg' => 'no tickets are pending action from you'
                ],
                [
                    'title' => 'On-Going',
                    'status' => [ 'open', 'in-progress', 'elevated' ],
                    'no_tickets_msg' => 'you do not have ongoing tickets'
                ],
                [
                    'title' => 'Resolved',
                    'status' => [ 'resolved' ],
                    'no_tickets_msg' => 'you do not have any closed tickets yet'
                ]
            ]
        ],
        'status' => [
            'open' => 'OPEN',
            'in-progress' => 'IN PROGRESS',
            'awaiting-acceptance' => 'AWAITING ACCEPTANCE',
            'escalated' => 'ESCALATED',
            'resolved' => 'RESOLVED',
        ],

        'priority' => [
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