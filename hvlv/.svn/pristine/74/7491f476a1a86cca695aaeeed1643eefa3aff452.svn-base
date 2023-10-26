<?php
return [
	'orderCode' => [
		'type' => 'string',
		'required' => true,
	],
	'exType' => [
		'type' => 'string',
		'restriction' => [
			'enum' => [
			 'EX_CUSTOMS',
			],
		],
		'required' => true,
	],
	'exSubType' => [
		'type' => 'string',
		'restriction' => [
			'enum' => [
			 'INCORRECT_HSC',
			 'CUSTOMS_INSPECTION',
			],
		],
		'required' => false,
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
	'remark' => [
		'type' => 'string',
		'restriction' => [
			'maxLength' => '256',
		],
		'required' => false,
	],
];
