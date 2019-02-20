<?php

use MeetMatt\Colla\Mood\Presentation\Http\CreateTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\FindTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\IndexAction;
use MeetMatt\Colla\Mood\Presentation\Http\ShowTeamAction;

return [
    [
        'method'  => 'GET',
        'pattern' => '/',
        'action'  => IndexAction::class,
        'name'    => 'index',
    ],
    [
        'method'  => 'POST',
        'pattern' => '/teams',
        'action'  => CreateTeamAction::class,
        'name'    => 'create_team'
    ],
    [
        'method'  => 'POST',
        'pattern' => '/teams/find',
        'action'  => FindTeamAction::class,
        'name'    => 'find_team'
    ],
    [
        'method'  => 'GET',
        'pattern' => '/teams/{id}',
        'action'  => ShowTeamAction::class,
        'name'    => 'show_team'
    ],
    /*
    [
        'method' => 'GET',
        'pattern' => '/teams/{id}/feedback/today',
        'action' => '//page to generate or show N feedback links for today',
    ],
    [
        'method' => 'GET',
        'pattern' => '/teams/{id}/feedback/history',
        'action' => '//show team feedback history',
    ],
    [
        'method' => 'GET',
        'pattern' => '/feedback/{id}',
        'action' => '//feedback form',
    ],
    [
        'method' => 'POST',
        'pattern' => '/feedback/{id}',
        'action' => '//process feedback',
    ],
    */
];