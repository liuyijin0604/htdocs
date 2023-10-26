<?php
return [
	'orderCode' => [
		'type' => 'string',
		'required' => true,
	],
	'eta' => [
		'type' => 'string',
		'required' => true,
	],
	'etaTimezone' => [
		'type' => 'string',
		'required' => true,
	],
	'etd' => [
		'type' => 'string',
		'required' => true,
	],
	'etdTimezone' => [
		'type' => 'string',
		'required' => true,
	],
	'deliveryStartTime' => [
		'type' => 'string',
		'required' => false,
	],
	'deliveryStartTimezone' => [
		'type' => 'string',
		'required' => false,
	],
	'deliveryEndTime' => [
		'type' => 'string',
		'required' => false,
	],
	'deliveryEndTimezone' => [
		'type' => 'string',
		'required' => false,
	],
	'pol' => [
		'type' => 'string',
		'required' => true,
	],
	'pod' => [
		'type' => 'string',
		'required' => true,
	],
	'blno' => [
		'type' => 'string',
		'required' => true,
	],
	'hbl' => [
		'type' => 'string',
		'required' => true,
	],
	'cargoReadyDate' => [
		'type' => 'date',
		'required' => true,
	],
	'cargoReadyTimezone' => [
		'type' => 'string',
		'required' => true,
	],
	'documentList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\DocumentList',
		'o' => [
			'attributes' => [
				'document' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Document',
					'o' => [
						'attributes' => [
							'type' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'CI',
									 'PL',
									 'BILL_OF_LADING',
									 'BOOKING_CONFIRM',
									 'CONTAINER_MANIFEST',
									 'POD',
									 'TRUCK_MANIFEST',
									 'ROAD_MANIFEST',
									 'OTHER',
									],
								],
								'required' => true,
							],
							'fileType' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'PDF',
									 'XLS',
									 'XLSX',
									 'DOC',
									 'DOCX',
									 'JPG',
									 'JPEG',
									],
								],
								'required' => true,
							],
							'fileData' => [
								'type' => 'string',
								'required' => true,
							],
							'remark' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '256',
								],
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
								'required' => true,
							],
							'pod' => [
								'type' => 'string',
								'required' => true,
							],
							'eta' => [
								'type' => 'string',
								'required' => true,
							],
							'etaTimezone' => [
								'type' => 'string',
								'required' => true,
							],
							'etd' => [
								'type' => 'string',
								'required' => true,
							],
							'etdTimezone' => [
								'type' => 'string',
								'required' => true,
							],
							'ladingBillNo' => [
								'type' => 'string',
								'required' => true,
							],
							'houseBillNo' => [
								'type' => 'string',
								'required' => true,
							],
						],
					],
					'required' => true,
				],
			],
		],
		'required' => true,
	],
];
