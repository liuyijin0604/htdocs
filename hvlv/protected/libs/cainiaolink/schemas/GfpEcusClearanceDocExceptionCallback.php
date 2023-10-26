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
	'subStatus' => [
		'type' => 'unsignedByte',
		'restriction' => [
			'enum' => [
			 'EX_DOC_MISSING',
			 'EX_DOC_ERROR',
			 'EX_QTY',
			 'EX_DAMAGE',
			 'EX_OTHER',
			],
		],
		'required' => true,
	],
	'remark' => [
		'type' => 'string',
		'restriction' => [
			'maxLength' => '256',
		],
		'required' => false,
	],
];
