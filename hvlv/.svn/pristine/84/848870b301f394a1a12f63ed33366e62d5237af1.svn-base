<?php
return [
	'orderCode' => [
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
	'cargo' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Cargo',
		'o' => [
			'attributes' => [
				'totalPackages' => [
					'type' => 'unsignedInt',
					'required' => false,
				],
				'totalPallets' => [
					'type' => 'unsignedInt',
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
							'containerNo' => [
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
								'required' => true,
							],
							'size' => [
								'type' => 'unsignedByte',
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
													'containerNo' => [
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
					'required' => false,
				],
			],
		],
		'required' => false,
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
];
