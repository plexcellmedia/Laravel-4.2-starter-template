<?php

return array(
	'multi' => array(
        'admin' => array(
            'driver' => 'database',
            'table' => 'admins'
        ),
        'user' => array(
            'driver' => 'database',
            'table' => 'users'
        ),
    ),
	'reminder' => array(
		'email' => 'emails.auth.reminder',
		'table' => 'password_reminders',
		'expire' => 60,
	),
);
