<?php
return [

	'cup' => 'tournament|tournaments',
	'invitation' => 'invitation|invitations',
	/**
	 * Forms
	 */
	'cup-form-edit' => 'Edit tournament',
	'cup-form-create' => 'Create tournament',
	'cup-form-delete' => 'Delete tournament',
	'cup-form-delete-confirm' => 'Are you sure you want to delete the tournament &quot;:name&quot;? Teams that already entered will be notified automatically.',
	/**
	 * labels
	 */
	'name-label' => 'tournament name',
	'clan_id-label' => 'organizing clan',
	'description-label' => 'description',
	'starts_at-label' => 'starts at',
	'closes_in-label' => 'entries close in',
	'closes_at-label' => 'entries close at',
	'team_size-label' => 'team size',
	'teams_max-label' => 'team limit',
	'teams_min-label' => 'required number of teams to start',
	'days-label' => 'number of days for this event',
	'entry_fee-label' => 'entry fee',
	'disputable-label' => 'disputable',
	'human_admin-label' => 'human admin',
	'use_trustworthiness-label' => 'use trustworthiness system',
	'invite_only-label' => 'invite only',
	'public-label' => 'public',
	'commercial-label' => 'commercial',
	'steam_game_id-label' => 'game',
	'award_prizes-label' => 'prizes',
	'award_honor-label' => 'honoring',
	'play_weekdays-label' => 'weekdays',
	'play_weekends-label' => 'weekends',
	'daily_play_time_starts-label' => 'opening hours',
	'daily_play_time_ends-label' => 'closing hours',
	'competition_type_id-label' => 'competition type',
	/**
	 * Help blocks
	 */
	'team_size-help' => 'number of gamers allowed per team',
	'entry_fee-help' => 'fee per team (in euro-cents)',
	'teams_min-help' => 'minimum number of teams needed before starting',
	'teams_max-help' => 'maximum number of teams allowed to participate (optional)',

	'closes_at-help' => 'entry to this cup is closed at the entered (optional) time; the system will also calculate a preparation time based on the number of participants - the first time of both will be used (the more participants the earlier the cup closes)',
	'invite_only-help' => 'make tournament invite only',
	'public-help' => 'display tournament publicly, otherwise will only show to invitees and participants',
	'commercial-help' => 'commercial or promotional tournament',
	'award_honor-help' => 'award default tournament.today honors and badges',
	'award_prizes-help' => 'award money or items as prizes',
	'play_weekdays-help' => 'if multiple days, allow to play on weekdays',
	'play_weekends-help' => 'if multiple days, allow to play on weekends',
	'daily_play_time_starts-help' => 'matches are planned starting from this hour (for each day); leave empty for all hours',
	'daily_play_time_ends-help' => 'matches are planned up to this hour (each day); leave empty for all hours',

	'disputable-help' => 'matches can be disputed (requires human admin)',
	'human_admin-help' => 'tournament is administrated by a human',
	'use_trustworthiness-help' => 'correctness of scores determine trustworthiness of gamers',

	/**
	 * Participants
	 */
	'participant' => 'participant|participants',
	'participant-team' => 'participating team|participating teams',
	'spots-open' => 'open',
	'spots-needed' => 'needed',
	'spots-open-no-limit' => 'no limit',
	'team-represents' => 'represents :name',

	/**
	 * Timing
	 */
	'starts' => 'starts',
	'time-remaining' => 'time remaining',

	/**
	 * Sign Up for tournament
	 */

	'sign-up' => 'sign up',
	'sign-up-description' => 'form your team to compete in this tournament',
	'sign-up-team' => 'team',
	'sign-up-overview' => 'overview',
	'sign-up-pay-fee' => 'pay fee',

	/**
	 * Signed up
	 */

	'you-participate' => 'you compete',
	'you-won' => 'you won',
	'you-participated' => 'you competed',
	'you-are-invited' => 'you are invited',
	'you-are-invited-description' => 'You have been invited to participate by other teams. Please note you can only join one team during a tournament. All other invitations to participate will be automatically declined.',
	'delete-team' => 'remove team',
	'delete-team-confirm' => 'Are you sure you want to remove this team?',

	/**
	 * Cup deletion
	 */
	'inform-cup-deleted' => 'Tournament :name has been deleted.'
];