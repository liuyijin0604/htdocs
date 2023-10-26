<?php
return [
	'orderCode' => [
		'type' => 'string',
		'required' => true,
	],
	'operateTime' => [
		'type' => 'string',
		'required' => true,
	],
	'operateTimezone' => [
		'type' => 'string',
		'required' => true,
	],
	'operator' => [
		'type' => 'string',
		'required' => false,
	],
	'country' => [
		'type' => 'string',
		'required' => false,
	],
	'state' => [
		'type' => 'string',
		'required' => false,
	],
	'city' => [
		'type' => 'string',
		'required' => false,
	],
	'address' => [
		'type' => 'string',
		'required' => false,
	],
	'remark' => [
		'type' => 'string',
		'restriction' => [
			'maxLength' => '256',
		],
		'required' => false,
	],
];
