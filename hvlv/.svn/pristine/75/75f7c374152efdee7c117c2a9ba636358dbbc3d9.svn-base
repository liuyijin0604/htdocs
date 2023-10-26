<?php
return [
	'order' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Order',
		'o' => [
			'attributes' => [
				'flOrderCode' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => true,
				],
				'gmtModified' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => true,
				],
				'status' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'enum' => [
						 'NEW',
						 'UPDATE',
						],
					],
					'required' => true,
				],
				'originWarehouse' => [
					'type' => 'string',
					'required' => false,
				],
				'destinationWarehouse' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => false,
				],
				'transportMode' => [
					'type' => 'string',
					'maxOccurs' => '1',
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
					'maxOccurs' => '1',
					'restriction' => [
						'enum' => [
						 'FCL',
						 'LCL',
						],
					],
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
				'incoterm' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'enum' => [
						 'FOB',
						 'EXW',
						 'CIF',
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
					'required' => false,
				],
				'cargoReadyDate' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => true,
				],
				'cargoReadyTimeZone' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => true,
				],
				'bizType' => [
					'type' => 'string',
					'restriction' => [
						'enum' => [
						 'TMALL_DIRECT',
						 'TMALL_HK',
						],
					],
					'required' => false,
				],
				'targetEta' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => true,
				],
				'targetEtaTimezone' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => true,
				],
				'refCode' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'required' => false,
				],
				'feature' => [
					'type' => 'string',
					'required' => false,
				],
				'remark' => [
					'type' => 'string',
					'required' => false,
				],
			],
		],
		'required' => false,
	],
	'serviceList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\ServiceList',
		'o' => [
			'attributes' => [
				'service' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Service',
					'o' => [
						'attributes' => [
							'serviceId' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'ORIGIN_PICKUP',
									 'CFS_RECEIVING',
									 'ORIGIN_DECLARATION',
									 'TALLY_BY_PIECE',
									 'TALLY_BY_CARTON',
									 'TALLY_BY_PALLET',
									 'LABELING',
									 'SHIPPING_MARK',
									 'QC',
									 'HSCODE_FILING',
									 'SHELFLIFE_CHECK',
									 'DOCUMENTATION',
									],
								],
								'required' => true,
							],
							'serviceName' => [
								'type' => 'string',
								'required' => false,
							],
						],
					],
					'required' => true,
				],
			],
		],
		'maxOccurs' => '1',
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
								'maxOccurs' => '1',
								'restriction' => [
									'enum' => [
									 'CI',
									 'PL',
									 'MSDS',
									 'FUMIGATION_CERT',
									 'OTHER',
									],
								],
								'required' => true,
							],
							'url' => [
								'type' => 'string',
								'required' => false,
							],
							'remark' => [
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
	'partyList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\PartyList',
		'o' => [
			'attributes' => [
				'party' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Party',
					'o' => [
						'attributes' => [
							'type' => [
								'type' => 'string',
								'maxOccurs' => '1',
								'restriction' => [
									'enum' => [
									 'SHIPPER',
									 'CONSIGNEE',
									 'PICKUP',
									 'FIRST_NOTIFIER',
									 'SECOND_NOTIFIER',
									],
								],
								'required' => true,
							],
							'companyName' => [
								'type' => 'string',
								'required' => false,
							],
							'contactName' => [
								'type' => 'string',
								'required' => false,
							],
							'phone' => [
								'type' => 'string',
								'required' => true,
							],
							'email' => [
								'type' => 'string',
								'required' => true,
							],
							'address' => [
								'type' => 'string',
								'required' => true,
							],
							'city' => [
								'type' => 'string',
								'required' => true,
							],
							'state' => [
								'type' => 'string',
								'required' => true,
							],
							'country' => [
								'type' => 'string',
								'required' => true,
							],
							'zipcode' => [
								'type' => 'string',
								'required' => true,
							],
							'uscc' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '128',
								],
								'required' => false,
							],
						],
					],
					'required' => true,
				],
			],
		],
		'maxOccurs' => '1',
		'required' => true,
	],
	'cargo' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Cargo',
		'o' => [
			'attributes' => [
				'totalPackages' => [
					'type' => 'unsignedByte',
					'required' => true,
				],
				'packageUom' => [
					'type' => 'string',
					'required' => true,
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
												 '20',
												 '40',
												 '45',
												],
											],
											'required' => false,
										],
										'qty' => [
											'type' => 'unsignedInt',
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
											'type' => 'unsignedInt',
											'required' => true,
										],
										'length' => [
											'type' => 'unsignedInt',
											'required' => true,
										],
										'width' => [
											'type' => 'unsignedInt',
											'required' => true,
										],
										'num' => [
											'type' => 'unsignedInt',
											'required' => true,
										],
										'packageUom' => [
											'type' => 'string',
											'required' => true,
										],
										'feature' => [
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
													'itemLineId' => [
														'type' => 'long',
														'required' => true,
													],
													'itemCode' => [
														'type' => 'string',
														'required' => true,
													],
													'itemName' => [
														'type' => 'string',
														'required' => false,
													],
													'barcode' => [
														'type' => 'string',
														'required' => false,
													],
													'desc' => [
														'type' => 'string',
														'required' => false,
													],
													'originHscode' => [
														'type' => 'string',
														'required' => false,
													],
													'destinationHscode' => [
														'type' => 'string',
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
			],
		],
		'required' => false,
	],
];
