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
					'type' => 'unsignedByte',
					'required' => false,
				],
				'totalUnits' => [
					'type' => 'unsignedByte',
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
					'type' => 'unsignedByte',
					'required' => true,
				],
				'chargeVolume' => [
					'type' => 'decimal',
					'required' => true,
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
								'type' => 'unsignedInt',
								'required' => false,
							],
							'height' => [
								'type' => 'unsignedByte',
								'required' => true,
							],
							'width' => [
								'type' => 'unsignedShort',
								'required' => true,
							],
							'length' => [
								'type' => 'unsignedByte',
								'required' => true,
							],
						],
					],
					'required' => true,
				],
			],
		],
		'required' => false,
	],
	'itemOrderList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\ItemOrderList',
		'o' => [
			'attributes' => [
				'itemOrder' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\ItemOrder',
					'o' => [
						'attributes' => [
							'itemOrderCode' => [
								'type' => 'string',
								'required' => true,
							],
							'itemOrderLineList' => [
								'type' => 'object',
								'def' => 'CainiaoLink\\Schemas\\ItemOrderLineList',
								'o' => [
									'attributes' => [
										'itemOrderLine' => [
											'type' => 'object',
											'def' => 'CainiaoLink\\Schemas\\ItemOrderLine',
											'o' => [
												'attributes' => [
													'itemCode' => [
														'type' => 'unsignedInt',
														'required' => true,
													],
													'palletNo' => [
														'type' => 'unsignedInt',
														'required' => false,
													],
													'totalPackages' => [
														'type' => 'unsignedByte',
														'required' => false,
													],
													'pkgUom' => [
														'type' => 'string',
														'required' => false,
													],
													'totalUnits' => [
														'type' => 'unsignedByte',
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
												],
											],
											'required' => true,
										],
									],
								],
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
									 'CONTRACT',
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
					'required' => false,
				],
			],
		],
		'required' => false,
	],
];
