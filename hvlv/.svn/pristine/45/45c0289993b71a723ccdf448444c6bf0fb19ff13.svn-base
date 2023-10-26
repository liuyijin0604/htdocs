<?php
return [
	'orderCode' => [
		'type' => 'string',
		'maxOccurs' => '1',
		'required' => true,
	],
	'operateTime' => [
		'type' => 'string',
		'maxOccurs' => '1',
		'required' => true,
	],
	'operateTimezone' => [
		'type' => 'string',
		'maxOccurs' => '1',
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
				'totalUnits' => [
					'type' => 'unsignedInt',
					'required' => false,
				],
				'totalVolume' => [
					'type' => 'decimal',
					'maxOccurs' => '1',
					'required' => true,
				],
				'totalGrossWeight' => [
					'type' => 'decimal',
					'maxOccurs' => '1',
					'required' => true,
				],
				'chargeWeight' => [
					'type' => 'decimal',
					'required' => false,
				],
				'chargeVolume' => [
					'type' => 'decimal',
					'required' => false,
				],
			],
		],
		'maxOccurs' => '1',
		'required' => true,
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
							'packageNo' => [
								'type' => 'string',
								'required' => false,
							],
							'height' => [
								'type' => 'unsignedInt',
								'required' => false,
							],
							'width' => [
								'type' => 'unsignedInt',
								'required' => false,
							],
							'length' => [
								'type' => 'unsignedInt',
								'required' => false,
							],
							'qty' => [
								'type' => 'int',
								'required' => true,
							],
							'uom' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'PLT',
									 'CNT',
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
													'itemLineId' => [
														'type' => 'long',
														'required' => true,
													],
													'itemCode' => [
														'type' => 'string',
														'required' => true,
													],
													'palletNo' => [
														'type' => 'string',
														'required' => false,
													],
													'totalPackages' => [
														'type' => 'unsignedInt',
														'required' => false,
													],
													'pkgUom' => [
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
													'itemLineDetailList' => [
														'type' => 'object',
														'def' => 'CainiaoLink\\Schemas\\ItemLineDetailList',
														'o' => [
															'attributes' => [
																'itemLineDetail' => [
																	'type' => 'object',
																	'def' => 'CainiaoLink\\Schemas\\ItemLineDetail',
																	'o' => [
																		'attributes' => [
																			'type' => [
																				'type' => 'string',
																				'restriction' => [
																					'enum' => [
																					 'GOOD',
																					 'DAMAGED',
																					],
																				],
																				'required' => false,
																			],
																			'qty' => [
																				'type' => 'integer',
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
								'maxOccurs' => '1',
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
									 'CONTRACT',
									 'CONTAINER_MANIFEST',
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
								'required' => false,
							],
							'fileData' => [
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
						],
					],
					'required' => true,
				],
			],
		],
		'required' => false,
	],
];
