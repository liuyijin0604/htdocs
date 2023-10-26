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
	'port' => [
		'type' => 'string',
		'required' => true,
	],
	'atd' => [
		'type' => 'string',
		'required' => true,
	],
	'atdTimezone' => [
		'type' => 'string',
		'required' => true,
	],
	'remark' => [
		'type' => 'string',
		'required' => false,
	],
	'routeList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\RouteList',
		'o' => [
			'attributes' => [
				'route' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Route',
					'o' => [
						'attributes' => [
							'transportMode' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'AIR',
									 'OCEAN',
									 'RAILWAY',
									 'TRUCK',
									],
								],
								'required' => true,
							],
							'carrierCode' => [
								'type' => 'string',
								'required' => false,
							],
							'vessel' => [
								'type' => 'string',
								'required' => false,
							],
							'voyage' => [
								'type' => 'string',
								'required' => false,
							],
							'pol' => [
								'type' => 'string',
								'required' => false,
							],
							'pod' => [
								'type' => 'string',
								'required' => false,
							],
							'eta' => [
								'type' => 'string',
								'required' => false,
							],
							'etaTimezone' => [
								'type' => 'string',
								'required' => false,
							],
							'vesselType' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'TON',
									 'GP',
									 'HQ',
									],
								],
								'required' => false,
							],
							'vesselSize' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 '3',
									 '5',
									 '8',
									 '10',
									 '20',
									 '40',
									 '45',
									],
								],
								'required' => false,
							],
							'transportNo' => [
								'type' => 'string',
								'required' => false,
							],
						],
					],
					'required' => true,
				],
			],
		],
		'required' => false,
	],
];
