<?php
return [
	'orderCode' => [
		'type' => 'string',
		'required' => true,
	],
	'serialNum' => [
		'type' => 'string',
		'required' => true,
	],
	'chargeList' => [
		'type' => 'object',
		'def' => 'CainiaoLink\\Schemas\\ChargeList',
		'o' => [
			'attributes' => [
				'charge' => [
					'type' => 'object',
					'def' => 'CainiaoLink\\Schemas\\Charge',
					'o' => [
						'attributes' => [
							'chargeCode' => [
								'type' => 'string',
								'required' => true,
							],
							'qty' => [
								'type' => 'unsignedInt',
								'required' => false,
							],
							'uom' => [
								'type' => 'string',
								'restriction' => [
									'enum' => [
									 'PLT',
									 'CTN',
									 'CBM',
									 'PIECE',
									],
								],
								'required' => false,
							],
							'cost' => [
								'type' => 'decimal',
								'required' => false,
							],
							'currency' => [
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
		'required' => true,
	],
];
