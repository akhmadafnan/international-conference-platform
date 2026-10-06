<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Initial Global Super Administrator
    |--------------------------------------------------------------------------
    |
    | Trusted bootstrap only. This is intentionally separate from Spatie
    | edition-scoped roles and is disabled unless explicitly enabled.
    |
    */

    'super_admin' => [
        'enabled' => env('INITIAL_SUPER_ADMIN_ENABLED', false),
        'name' => env('INITIAL_SUPER_ADMIN_NAME'),
        'email' => env('INITIAL_SUPER_ADMIN_EMAIL'),
        'password' => env('INITIAL_SUPER_ADMIN_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Canonical Permission Vocabulary
    |--------------------------------------------------------------------------
    |
    | Permissions are capability vocabulary. Resource relationships, edition
    | scope, workflow state, COI and Policies/Gates remain authoritative.
    |
    */

    'permissions' => [
        'submission.admin_screen',
        'registration.configure',
        'registration.fee_exempt',
        'payment.configure',
        'payment.verify',
        'refund.execute',
        'review.assign',
        'review.submit',
        'academic.decide',
        'submission.withdraw.approve',
        'submission.contributor_change.approve',
        'schedule.publish',
        'presentation.verify',
        'presentation.exception.approve',
        'publication.decide',
        'publication.eligibility.override',
        'publication.transfer',
        'certificate.issue_manual',
        'certificate.issue_bulk',
        'certificate.revoke',
        'certificate.reissue',
        'edition.archive',
        'historical.correct',
        'edition.unarchive',
    ],

    /*
    |--------------------------------------------------------------------------
    | Edition Role Blueprints
    |--------------------------------------------------------------------------
    |
    | These are definitions only. No edition-scoped Role rows are created
    | during Phase 01 because ConferenceEdition does not yet exist.
    |
    | Resource-scoped actors such as Reviewer, Session Chair, Moderator and
    | Presenter are deliberately not modeled as broad edition roles here.
    |
    */

    'edition_role_blueprints' => [
        'front_office' => [],

        'conference_admin' => [
            'registration.configure',
            'registration.fee_exempt',
            'payment.configure',
        ],

        'finance' => [
            'payment.verify',
            'refund.execute',
        ],

        'academic_committee' => [
            'review.assign',
        ],

        'academic_decision_authority' => [
            'academic.decide',
            'publication.decide',
        ],

        'event_operations' => [
            'schedule.publish',
            'presentation.verify',
        ],

        'publication_team' => [
            'publication.transfer',
        ],

        'certificate_authority' => [
            'certificate.issue_manual',
            'certificate.issue_bulk',
            'certificate.revoke',
            'certificate.reissue',
        ],
    ],

];
