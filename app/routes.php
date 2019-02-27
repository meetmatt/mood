<?php

use MeetMatt\Colla\Mood\Presentation\Http\Feedback\FeedbackFormAction;
use MeetMatt\Colla\Mood\Presentation\Http\Feedback\FeedbackHistoryAction;
use MeetMatt\Colla\Mood\Presentation\Http\Feedback\SaveFeedbackAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\CreateTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\FindTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\IndexAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\SaveTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\ShowTeamAction;

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
    [
        'method'  => 'POST',
        'pattern' => '/teams/{id}',
        'action'  => SaveTeamAction::class,
        'name'    => 'save_team'
    ],
    [
        'method'  => 'GET',
        'pattern' => '/teams/{id}/feedback/history',
        'action'  => FeedbackHistoryAction::class,
        'name'    => 'feedback_history'
    ],
    [
        'method'  => 'GET',
        'pattern' => '/feedback/{id}',
        'action'  => FeedbackFormAction::class,
        'name'    => 'feedback_form'
    ],
    [
        'method'  => 'POST',
        'pattern' => '/feedback/{id}',
        'action'  => SaveFeedbackAction::class,
        'name'    => 'save_feedback'
    ],
];