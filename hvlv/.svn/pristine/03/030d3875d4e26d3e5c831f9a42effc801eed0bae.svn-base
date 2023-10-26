<?php
return [
	'order' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Order',
		'o' => [
			'attributes' => [
				'orderCode' => [
					'type' => 'string',
					'required' => true,
				],
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
				'containerLoad' => [
					'type' => 'string',
					'restriction' => [
						'enum' => [
						 'FCL',
						 'LCL',
						],
					],
					'required' => true,
				],
				'incoterm' => [
					'type' => 'string',
					'restriction' => [
						'enum' => [
						 'CIF',
						 'FOB',
						 'EXW',
						 'FCA',
						],
					],
					'required' => true,
				],
				'cargoType' => [
					'type' => 'string',
					'restriction' => [
						'enum' => [
						 'General',
						 'Dangerous',
						 'Reefer',
						],
					],
					'required' => true,
				],
				'refCode' => [
					'type' => 'string',
					'required' => true,
				],
				'feature' => [
					'type' => 'string',
					'required' => false,
				],
				'remark' => [
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
							'url' => [
								'type' => 'string',
								'required' => true,
							],
							'remark' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '1024',
								],
								'required' => false,
							],
						],
					],
					'required' => true,
				],
			],
		],
		'required' => true,
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
				'packageUom' => [
					'type' => 'string',
					'required' => false,
				],
				'totalUnits' => [
					'type' => 'unsignedByte',
					'required' => false,
				],
				'totalVolume' => [
					'type' => 'decimal',
					'required' => false,
				],
				'totalGrossWeight' => [
					'type' => 'decimal',
					'required' => false,
				],
				'height' => [
					'type' => 'unsignedByte',
					'required' => false,
				],
				'length' => [
					'type' => 'unsignedShort',
					'required' => false,
				],
				'width' => [
					'type' => 'unsignedShort',
					'required' => false,
				],
				'packageList' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\PackageList',
					'o' => [
						'attributes' => [
							'packageItem' => [
								'type' => 'object',
								'def' => 'CainiaoLink\\Schemas\\PackageItem',
								'o' => [
									'attributes' => [
										'height' => [
											'type' => 'unsignedShort',
											'required' => false,
										],
										'length' => [
											'type' => 'unsignedShort',
											'required' => false,
										],
										'width' => [
											'type' => 'unsignedShort',
											'required' => false,
										],
										'num' => [
											'type' => 'unsignedByte',
											'required' => false,
										],
										'packageUom' => [
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
			],
		],
		'required' => true,
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
							'totalPackages' => [
								'type' => 'unsignedInt',
								'required' => false,
							],
							'packageUom' => [
								'type' => 'string',
								'required' => false,
							],
							'totalUnits' => [
								'type' => 'unsignedInt',
								'required' => false,
							],
							'totalVolume' => [
								'type' => 'decimal',
								'required' => false,
							],
							'totalGrossWeight' => [
								'type' => 'decimal',
								'required' => false,
							],
							'cargoDesc' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '1024',
								],
								'required' => false,
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
														'type' => 'unsignedLong',
														'required' => true,
													],
													'itemName' => [
														'type' => 'string',
														'restriction' => [
															'maxLength' => '1024',
														],
														'required' => false,
													],
													'barcode' => [
														'type' => 'unsignedLong',
														'required' => false,
													],
													'desc' => [
														'type' => 'string',
														'restriction' => [
															'maxLength' => '1024',
														],
														'required' => false,
													],
													'originHscode' => [
														'type' => 'unsignedLong',
														'required' => false,
													],
													'destinationHscode' => [
														'type' => 'unsignedLong',
														'required' => false,
													],
													'totalPackages' => [
														'type' => 'unsignedInt',
														'required' => false,
													],
													'packageUom' => [
														'type' => 'string',
														'required' => false,
													],
													'totalUnits' => [
														'type' => 'unsignedInt',
														'required' => false,
													],
													'totalVolume' => [
														'type' => 'decimal',
														'required' => false,
													],
													'totalGrossWeight' => [
														'type' => 'decimal',
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
						],
					],
					'required' => true,
				],
			],
		],
		'required' => false,
	],
];
