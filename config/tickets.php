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
            'keys' => [
                'open', 'in-progress', 'awaiting-acceptance', 'escalated', 'resolved'
            ],
            'labels' => [
                'OPEN', 'IN PROGRESS', 'AWAITING ACCEPTANCE', 'ESCALATED', 'RESOLVED'
            ],
            'badges' => [
                'styles' => [
                    'bg-teal-300 text-teal-800 border-teal-400 dark:bg-teal-600 dark:text-teal-200 dark:border-teal-600',
                    'bg-amber-300 text-amber-800 border-amber-400 dark:bg-amber-600 dark:text-amber-200 dark:border-amber-600',
                    'bg-lime-300 text-lime-800 border-lime-400 dark:bg-lime-600 dark:text-lime-200 dark:border-lime-600',
                    'bg-rose-300 text-rose-800 border-rose-400 dark:bg-rose-600 dark:text-rose-200 dark:border-rose-600',
                    'bg-emerald-300 text-emerald-800 border-emerald-400 dark:bg-emerald-600 dark:text-emerald-200
        dark:border-emerald-600'
                ],
                'icons' => [
                    'heroicon-o-envelope-open',
                    'heroicon-o-play',
                    'heroicon-o-clock',
                    'heroicon-o-arrow-trending-up',
                    'heroicon-o-check-circle'
                ]
            ],
            'cards' => [
                'backgrounds' => [
                    'bg-teal-200 dark:bg-teal-700',
                    'bg-amber-200 dark:bg-amber-700',
                    'bg-lime-200 dark:bg-lime-700',
                    'bg-rose-200 dark:bg-rose-700',
                    'bg-emerald-200 dark:bg-emerald-700'
                ]
            ]
        ],
        'priorities' => [
            'keys' => [ 'low', 'medium', 'high' ],
            'labels' => [ 'LOW', 'MEDIUM', 'HIGH' ],
            'timeouts' => [ 8, 4, 2 ],
            'badges' => [
                'styles' => [
                    'bg-green-100 text-green-800 border-green-400 dark:bg-green-700 dark:text-green-200 dark:border-green-600',
                    'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-600',
                    'bg-red-100 text-red-800 border-red-400 dark:bg-red-700 dark:text-red-200 dark:border-red-600'
                ],
                'icons' => [
                    'heroicon-o-signal',
                    'heroicon-o-signal-slash',
                    'heroicon-o-bell-alert'
                ]
            ]
        ],
    ];