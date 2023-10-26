<?php
return [
	'order' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Order',
		'o' => [
			'attributes' => [
				'orderCode' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '64',
					],
					'required' => true,
				],
				'gmtModified' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '20',
					],
					'required' => true,
				],
				'status' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'enum' => [
						 'NEW',
						],
					],
					'required' => true,
				],
				'originWarehouse' => [
					'type' => 'string',
					'restriction' => [
						'maxLength' => '32',
					],
					'required' => false,
				],
				'destinationWarehouse' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '32',
					],
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
					'restriction' => [
						'maxLength' => '16',
					],
					'required' => false,
				],
				'pod' => [
					'type' => 'string',
					'restriction' => [
						'maxLength' => '16',
					],
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
					'restriction' => [
						'maxLength' => '20',
					],
					'required' => true,
				],
				'cargoReadyTimeZone' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '16',
					],
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
					'restriction' => [
						'maxLength' => '20',
					],
					'required' => true,
				],
				'targetEtaTimezone' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '16',
					],
					'required' => true,
				],
				'refCode' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '64',
					],
					'required' => false,
				],
				'feature' => [
					'type' => 'string',
					'restriction' => [
						'maxLength' => '512',
					],
					'required' => false,
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
								'restriction' => [
									'maxLength' => '64',
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
								'restriction' => [
									'maxLength' => '512',
								],
								'required' => false,
							],
							'remark' => [
								'type' => 'string',
								'maxOccurs' => '512',
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
								'restriction' => [
									'maxLength' => '128',
								],
								'required' => false,
							],
							'contactName' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '64',
								],
								'required' => false,
							],
							'phone' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '32',
								],
								'required' => true,
							],
							'email' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '64',
								],
								'required' => true,
							],
							'address' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '512',
								],
								'required' => true,
							],
							'city' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '64',
								],
								'required' => true,
							],
							'state' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '64',
								],
								'required' => true,
							],
							'country' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '64',
								],
								'required' => true,
							],
							'zipcode' => [
								'type' => 'string',
								'restriction' => [
									'maxLength' => '32',
								],
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
					'required' => false,
				],
				'packageUom' => [
					'type' => 'string',
					'maxOccurs' => '1',
					'restriction' => [
						'maxLength' => '16',
					],
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
											'restriction' => [
												'maxLength' => '16',
											],
											'required' => true,
										],
										'feature' => [
											'type' => 'string',
											'restriction' => [
												'maxLength' => '512',
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
								'restriction' => [
									'maxLength' => '64',
								],
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
													'itemLineId' => [
														'type' => 'long',
														'required' => true,
													],
													'itemCode' => [
														'type' => 'string',
														'restriction' => [
															'maxLength' => '64',
														],
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
														'type' => 'string',
														'restriction' => [
															'maxLength' => '64',
														],
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
														'type' => 'string',
														'restriction' => [
															'maxLength' => '16',
														],
														'required' => false,
													],
													'destinationHscode' => [
														'type' => 'string',
														'restriction' => [
															'maxLength' => '16',
														],
														'required' => false,
													],
													'totalPackages' => [
														'type' => 'unsignedInt',
														'required' => false,
													],
													'packageUom' => [
														'type' => 'string',
														'restriction' => [
															'maxLength' => '16',
														],
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
