<?php
return [
	'flOrderCode' => [
		'type' => 'string',
		'required' => false,
	],
	'status' => [
		'type' => 'unsignedByte',
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
	'etd' => [
		'type' => 'string',
		'required' => false,
	],
	'etdTimezone' => [
		'type' => 'string',
		'required' => false,
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
		'required' => false,
	],
	'pod' => [
		'type' => 'string',
		'required' => false,
	],
	'blno' => [
		'type' => 'string',
		'required' => false,
	],
	'cargoReadyDate' => [
		'type' => 'date',
		'required' => false,
	],
	'cargoReadyTimezone' => [
		'type' => 'string',
		'required' => false,
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
								'required' => false,
							],
							'url' => [
								'type' => 'string',
								'required' => false,
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
								'required' => false,
							],
							'fileData' => [
								'type' => 'string',
								'required' => false,
							],
						],
					],
					'required' => false,
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
								'restriction' => [
									'enum' => [
									 'AIR',
									 'OCEAN',
									 'RAILWAY',
									 'TRUCK',
									],
								],
								'required' => false,
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
							'etd' => [
								'type' => 'string',
								'required' => false,
							],
							'etdTimezone' => [
								'type' => 'string',
								'required' => false,
							],
							'ladingBillNo' => [
								'type' => 'string',
								'required' => false,
							],
							'houseBillNo' => [
								'type' => 'string',
								'required' => false,
							],
						],
					],
					'required' => false,
				],
			],
		],
		'required' => false,
	],
	'containerList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\ContainerList',
		'o' => [
			'attributes' => [
				'container' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Container',
					'o' => [
						'attributes' => [
							'type' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'GP',
									 'HQ',
									],
								],
								'required' => false,
							],
							'size' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 '40',
									 '45',
									 '20',
									],
								],
								'required' => false,
							],
							'containerNo' => [
								'type' => 'string',
								'required' => false,
							],
						],
					],
					'required' => false,
				],
			],
		],
		'required' => false,
	],
];
