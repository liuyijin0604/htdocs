<?php
return [
	'orderCode' => [
		'type' => 'string',
		'required' => true,
	],
	'actionType' => [
		'type' => 'string',
		'restriction' => [
			'enum' => [
			 'BOOKING',
			],
		],
		'required' => true,
	],
	'exception' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\Exception',
		'o' => [
			'attributes' => [
				'type' => [
					'type' => 'string',
					'restriction' => [
						'enum' => [
						 'EX_ETA',
						 'EX_LCL',
						],
					],
					'required' => true,
				],
				'exceptionId' => [
					'type' => 'string',
					'required' => true,
				],
				'description' => [
					'type' => 'string',
					'restriction' => [
						'maxLength' => '256',
					],
					'required' => false,
				],
				'options' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Options',
					'o' => [
						'attributes' => [
							'option' => [
								'type' => 'object',
								'def' => 'CainiaoLink\\Schemas\\Option',
								'o' => [
									'attributes' => [
										'optionNo' => [
											'type' => 'string',
											'required' => true,
										],
										'feature' => [
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
					'maxOccurs' => '1',
					'required' => true,
				],
			],
		],
		'required' => true,
	],
];
