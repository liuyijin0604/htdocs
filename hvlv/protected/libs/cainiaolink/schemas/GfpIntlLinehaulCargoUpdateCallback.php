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
	'remark' => [
		'type' => 'string',
		'required' => false,
	],
	'cargo' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Cargo',
		'o' => [
			'attributes' => [
				'totalPackages' => [
					'type' => 'unsignedInt',
					'required' => false,
				],
				'totalUnits' => [
					'type' => 'unsignedInt',
					'required' => false,
				],
				'totalVolume' => [
					'type' => 'decimal',
					'required' => true,
				],
				'totalGrossWeight' => [
					'type' => 'decimal',
					'required' => true,
				],
				'chargeWeight' => [
					'type' => 'unsignedInt',
					'required' => false,
				],
				'chargeVolume' => [
					'type' => 'decimal',
					'required' => false,
				],
			],
		],
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
									 'PL',
									 'BILL_OF_LADING',
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
								'required' => true,
							],
							'fileData' => [
								'type' => 'string',
								'required' => true,
							],
							'remark' => [
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
								'required' => true,
							],
							'size' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 '20',
									 '40',
									 '45',
									],
								],
								'required' => true,
							],
							'containerNo' => [
								'type' => 'string',
								'required' => true,
							],
						],
					],
					'required' => false,
				],
			],
		],
		'required' => false,
	],
	'palletList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\PalletList',
		'o' => [
			'attributes' => [
				'pallet' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Pallet',
					'o' => [
						'attributes' => [
							'palletNo' => [
								'type' => 'string',
								'required' => false,
							],
							'height' => [
								'type' => 'int',
								'required' => false,
							],
							'width' => [
								'type' => 'int',
								'required' => false,
							],
							'length' => [
								'type' => 'int',
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
