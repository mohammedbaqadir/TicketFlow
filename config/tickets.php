<?php
    declare( strict_types = 1 );

    return [
        'groupings' => [
            'index' => [
                [
                    'title' => 'Pending Action',
                    'status' => [ 'in-progress' ],
                    'assignee_required' => true,
                    'no_tickets_msg' => 'You have no active tickets'
                ],
                [
                    'title' => 'Pending Response',
                    'status' => [ 'awaiting-acceptance' ],
                    'assignee_required' => true,
                    'no_tickets_msg' => 'No tickets awaiting customer response'
                ],
                [
                    'title' => 'Unassigned Tickets',
                    'status' => [ 'open' ],
                    'assignee_required' => false,
                    'no_tickets_msg' => 'No tickets need assignment'
                ],
                [
                    'title' => 'Recently Resolved',
                    'status' => [ 'resolved' ],
                    'assignee_required' => true,
                    'no_tickets_msg' => 'No recently resolved tickets'
                ]
            ],
            'my_tickets' => [
                [
                    'title' => 'Needs Your Response',
                    'status' => [ 'awaiting-acceptance' ],
                    'no_tickets_msg' => 'No tickets need your response',
                ],
                [
                    'title' => 'Active Tickets',
                    'status' => [ 'open', 'in-progress' ],
                    'no_tickets_msg' => 'You have no active tickets',
                ],
                [
                    'title' => 'Escalated',
                    'status' => [ 'escalated' ],
                    'no_tickets_msg' => 'You have no escalated tickets',
                ],
                [
                    'title' => 'Recently Resolved',
                    'status' => [ 'resolved' ],
                    'no_tickets_msg' => 'No tickets were resolved recently',
                ]
            ]
        ],
        'statuses' => [
            'open' => 'OPEN',
            'in-progress' => 'IN PROGRESS',
            'awaiting-acceptance' => 'AWAITING ACCEPTANCE',
            'escalated' => 'ESCALATED',
            'resolved' => 'RESOLVED',
        ],
        'priorities' => [
            'low' => 'LOW',
            'medium' => 'MEDIUM',
            'high' => 'HIGH',
        ],
        'priority_timeouts' => [
            'low' => 8,
            'medium' => 4,
            'high' => 2,
        ],
    ];