<?php

/**
 * This is the model class for table "wms_org_quote".
 *
 * The followings are the available columns in table 'wms_org_quote':
 * @property string $id
 * @property string $org_id
 * @property integer $type,
 * @property string $quote_no
 * @property string $meta
 * @property string $vfrom
 * @property string $vto
 * @property integer $status
 */
class WmsOrgQuote extends CActiveRecord
{
	//-old-----------------------------------------------------------------------
	// storage services
	const QUOTE_PALLET_STORAGE_WEEK = 1030;
	const QUOTE_PALLET_STORAGE_OVERSIZE_WEEK = 1031;
	const QUOTE_PALLET_STORAGE_DAY = 1032;
	const QUOTE_CBM_STORAGE_WEEK = 1035;
	// const QUOTE_CBM_LF_RATE = 1036;
	// const QUOTE_CBM_PLT_RATE = 1037;
	const QUOTE_AVERAGE_WEIGHT = 1036;
	const QUOTE_WEIGHT_TO_CBM = 1037;
	const QUOTE_STORAGE_FREE_WEEK = 1039;
	const QUOTE_STORAGE_FREE_DAY = 1040;
	const QUOTE_manifest_fee = 1053;

	// stock in
	const QUOTE_PALLET_IN = 1010;
	const QUOTE_PALLET_IN_OVERSIZE = 1011;
	const QUOTE_CBM_IN = 1015;
	const QUOTE_HAND_LOAD_IN = 1016;
	const QUOTE_SORTING_COUNTING_PALLET = 2005;
	const QUOTE_SORTING_COUNTING_CARTON = 2010;
	const QUOTE_SORTING_COUNTING_UNIT = 2015;
	const QUOTE_LOAD_20FT_CONTAINER = 1041;
	const QUOTE_UNLOAD_20FT_CONTAINER = 1042;
	const QUOTE_LOAD_40FT_CONTAINER = 1045;
	const QUOTE_UNLOAD_40FT_CONTAINER = 1046;
	const QUOTE_PALLET_PUT_AWAY = 1050;
	const QUOTE_UNLOAD_CHARGE_PALLET_IN = 1051;
	const QUOTE_LOAD_CHARGE_PALLET_OUT = 1052;

	// Stock out B2B
	const QUOTE_PALLET_OUT = 1020;
	const QUOTE_PALLET_OUT_OVERSIZE = 1021;
	const QUOTE_CBM_OUT = 1025;
	const QUOTE_HAND_LOAD_OUT = 1026;

	const QUOTE_MISC_STANDARD_PALLET = 3020;
	const QUOTE_MISC_FUMIGATED_WOODEN_PALLET = 3022;
	const QUOTE_MISC_PLASTIC_PALLET = 3025;
	const QUOTE_MISC_PALLET_WRAP = 3030;
	const QUOTE_MISC_PALLET_STACKING = 3031;
	const QUOTE_MISC_PALLET_ALL_IN = 3032;

	// Stock out B2C
	const QUOTE_PICKING_MANUAL_ORDER = 2020;
	const QUOTE_PICKING_ORDER = 2021;
	const QUOTE_PICKING_UNIT = 2026;
	const QUOTE_PICKING_CARTON = 2025;
	const QUOTE_PICKING_BAG = 20251;
	const QUOTE_PICKING_PALLET = 2024;
	const QUOTE_PICKING_SKU_BASE = 2027;
	const QUOTE_PICKING_SKU_PER = 2028;
	const QUOTE_PICKING_CHARGECODE = 2029;

	const QUOTE_PACKING_ORDER = 2030;
	const QUOTE_PACKING_ORDER_STATUS = 2031;
	const QUOTE_PACKING_ORDER_EXTRA = 2032;
	const QUOTE_PACKING_UNIT = 2035;
	// const QUOTE_BOX_FEE = 2036;
	const QUOTE_PACKING_INSERT = 2037;
	const QUOTE_PACKING_CHARGECODE = 2038;
	const QUOTE_PACKING_DELIVERY_LABEL = 2039;
	const QUOTE_RETURN_UNIT = 2041;

	// Delivery
	const QUOTE_MISC_PICKUP_DELIVERY_PALLET = 3050;
	const QUOTE_MISC_PICKUP_DELIVERY_MIN_CHARGE = 3051;
	const QUOTE_MISC_DELIVERY_AIRPORT_PALLET = 3055;
	const QUOTE_MISC_DELIVERY_AIRPORT_MIN_CHARGE = 3056;
	const QUOTE_MISC_DELIVERY_CHINA = 3057;

	// for all misc
	const QUOTE_CYCLE_COUNT_PALLET = 2040;
	const QUOTE_STOCKTAKE_HOUR = 2050;
	const QUOTE_STOCKTAKE_DAY = 2052;
	const QUOTE_MISC_CHANGE_PALLET = 3010;
	const QUOTE_MISC_PALLET_HIRE_DAY = 3040;
	const QUOTE_MISC_LABOUR_HOUR = 3060;
	const QUOTE_MISC_OVERTIME_LABOUR_HOUR = 3062;
	const QUOTE_MISC_FORK_TRUCK_HOUR = 3070;
	const QUOTE_MISC_OVERTIME_FOR_TRUCK_HOUR = 3072;
	const QUOTE_MISC_LABELLING = 3080;

	//For Sydney
	const QUOTE_COURIER_CHARGECODE = 4001;
	const QUOTE_EXPRESS_CHARGECODE = 4002;
	const QUOTE_INTERNATIONAL_CHARGECODE = 4003;
	const QUOTE_SENDLE_CHARGE = 4004;
	const QUOTE_LETTER_CHARGECODE = 4005;
	const QUOTE_COURIER_CHARGECODE_AUPOSR = 4011;
	const QUOTE_COURIER_CHARGECODE_TNT = 4012;
	const QUOTE_COURIER_CHARGECODE_ETOLL = 4013;
	const QUOTE_COURIER_CHARGECODE_SF = 4014;
	const QUOTE_COURIER_CHARGECODE_EALLIED = 4015;
	const QUOTE_COURIER_CHARGECODE_UTOLL = 4016;
	const QUOTE_COURIER_CHARGECODE_TLACARGO = 4017;
	const QUOTE_COURIER_CHARGECODE_FASTWAY = 4018;
	const QUOTE_COURIER_CHARGECODE_AUPOST_EXPRESS =4019;
	const QUOTE_COURIER_CHARGECODE_UAP = 4020;
	const QUOTE_COURIER_CHARGECODE_MYTOLL = 4021;
	const QUOTE_COURIER_CHARGECODE_BORDER = 4022;

	//For Melbourne
	const QUOTE_COURIER_MEL_CHARGECODE = 6001;
	const QUOTE_EXPRESS_MEL_CHARGECODE = 6002;
	const QUOTE_INTERNATIONAL_MEL_CHARGECODE = 6003;
	const QUOTE_SENDLE_MEL_CHARGE = 6004;
	const QUOTE_LETTER_MEL_CHARGECODE = 6005;
	const QUOTE_COURIER_MEL_CHARGECODE_AUPOSR = 6011;
	const QUOTE_COURIER_MEL_CHARGECODE_TNT = 6012;
	const QUOTE_COURIER_MEL_CHARGECODE_ETOLL = 6013;
	const QUOTE_COURIER_MEL_CHARGECODE_SF = 6014;
	const QUOTE_COURIER_MEL_CHARGECODE_EALLIED = 6015;
	const QUOTE_COURIER_MEL_CHARGECODE_UTOLL = 6016;
	const QUOTE_COURIER_MEL_CHARGECODE_TLACARGO = 6017;
	const QUOTE_COURIER_MEL_CHARGECODE_FASTWAY = 6018;
	const QUOTE_COURIER_MEL_CHARGECODE_UAP = 6019;
	const QUOTE_COURIER_MEL_CHARGECODE_MYTOLL = 6020;
	const QUOTE_COURIER_MEL_CHARGECODE_BORDER = 6021;

	//For Brisbane
	const QUOTE_COURIER_BNE_CHARGECODE = 7001;
	const QUOTE_EXPRESS_BNE_CHARGECODE = 7002;
	const QUOTE_INTERNATIONAL_BNE_CHARGECODE = 7003;
	const QUOTE_SENDLE_BNE_CHARGE = 7004;
	const QUOTE_LETTER_BNE_CHARGECODE = 7005;
	const QUOTE_COURIER_BNE_CHARGECODE_AUPOSR = 7011;
	const QUOTE_COURIER_BNE_CHARGECODE_TNT = 7012;
	const QUOTE_COURIER_BNE_CHARGECODE_ETOLL = 7013;
	const QUOTE_COURIER_BNE_CHARGECODE_SF = 7014;
	const QUOTE_COURIER_BNE_CHARGECODE_EALLIED = 7015;
	const QUOTE_COURIER_BNE_CHARGECODE_UTOLL = 7016;
	const QUOTE_COURIER_BNE_CHARGECODE_TLACARGO = 7017;
	const QUOTE_COURIER_BNE_CHARGECODE_FASTWAY = 7018;
	const QUOTE_COURIER_BNE_CHARGECODE_UAP = 7019;
	const QUOTE_COURIER_BNE_CHARGECODE_MYTOLL = 7020;



	// return service
	const QUOTE_RETURN_RTS_STANDARD = 5001;
	const QUOTE_RETURN_LABEL = 5002;
	const QUOTE_RETURN_MANUAL_INPUT = 5003;
	const QUOTE_RETURN_RECEIVE_UNIT = 5011;
	const QUOTE_RETURN_RECEIVE_2KG = 5012;
	const QUOTE_RETURN_RECEIVE_5KG = 5013;
	const QUOTE_RETURN_RECEIVE_10KG = 5014;
	const QUOTE_RETURN_STORAGE_PALLET = 5021;
	const QUOTE_RETURN_STORAGE_2KG = 5022;
	const QUOTE_RETURN_STORAGE_5KG = 5023;
	const QUOTE_RETURN_STORAGE_10KG = 5024;
	const QUOTE_RETURN_PICKUP_2KG = 5031;
	const QUOTE_RETURN_PICKUP_5KG = 5032;
	const QUOTE_RETURN_PICKUP_10KG = 5033;
	const QUOTE_RETURN_CHECK_UNPACK = 5041;
	const QUOTE_RETURN_CHECK_PICTURE = 5042;
	const QUOTE_RETURN_CHECK_EXTRA_SYSTEM_INPUT = 5043;
	const QUOTE_RETURN_NEED_PRODUCT_INFO = 5051;
	const QUOTE_RETURN_RTS_IN_OUT_FEE = 5052;
	const QUOTE_RETURN_CAN_RESTOCK = 5053;
	const QUOTE_RETURN_CAN_REDELIVERY = 5054;

	public static $chgcodes = [
		1000 => 'Storage Services',
		self::QUOTE_PALLET_STORAGE_WEEK => ['Pallet Storage / Week'],
		1 => 'sep',
		self::QUOTE_PALLET_STORAGE_DAY => ['Storage / Day'],
		self::QUOTE_AVERAGE_WEIGHT => ['Average weight'],
		self::QUOTE_WEIGHT_TO_CBM => ['Weight of 1 cubic meter'],
		// self::QUOTE_CBM_LF_RATE => ['CBM LF X %'],
		// self::QUOTE_CBM_PLT_RATE => ['CBM PLT X %'],
		2 => 'sep',
		self::QUOTE_PALLET_STORAGE_OVERSIZE_WEEK => ['Storage Oversize / Week'],
		self::QUOTE_CBM_STORAGE_WEEK => ['CBM Storage / Week'],
		self::QUOTE_STORAGE_FREE_WEEK => ['Storage Free Weeks'],
		self::QUOTE_STORAGE_FREE_DAY => ['Storage Free Days'],
		self::QUOTE_manifest_fee => ['manifest_fee/task'],
		2000 => 'Stock In',
		self::QUOTE_PALLET_IN => ['Pallet In'],
		self::QUOTE_PALLET_IN_OVERSIZE => ['Pallet In Oversize'],
		self::QUOTE_CBM_IN => ['CBM In'],
		self::QUOTE_HAND_LOAD_IN => ['Hand Load In'],
		3 => 'sep',
		self::QUOTE_SORTING_COUNTING_PALLET => ['Sorting & Counting /Pallet'],
		self::QUOTE_SORTING_COUNTING_CARTON => ['Sorting & Counting / Carton'],
		self::QUOTE_SORTING_COUNTING_UNIT => ['Sorting & Counting / Unit'],
		self::QUOTE_PALLET_PUT_AWAY => ['Pallet Put away / Pallet'],
		4 => 'sep',
		self::QUOTE_UNLOAD_20FT_CONTAINER => ['Unload 20ft Container'],
		self::QUOTE_UNLOAD_40FT_CONTAINER => ['Unload 40ft Container'],
		self::QUOTE_UNLOAD_CHARGE_PALLET_IN => ['Pallet In for Unload Container task'],
		3000 => 'Stock Out B2B',
		self::QUOTE_PALLET_OUT => ['Pallet Out'],
		self::QUOTE_PALLET_OUT_OVERSIZE => ['Pallet Out Oversize'],
		self::QUOTE_CBM_OUT => ['CBM Out'],
		self::QUOTE_HAND_LOAD_OUT => ['Hand Load Out'],
		5 => 'sep',
		self::QUOTE_LOAD_20FT_CONTAINER => ['Load 20ft Container'],
		self::QUOTE_LOAD_40FT_CONTAINER => ['Load 40ft Container'],
		self::QUOTE_LOAD_CHARGE_PALLET_OUT => ['Pallet Out for Load Container task'],
		6 => 'sep',
		self::QUOTE_MISC_STANDARD_PALLET => ['Standard Pallet'],
		self::QUOTE_MISC_FUMIGATED_WOODEN_PALLET => ['Fumigated Wooden Pallet'],
		self::QUOTE_MISC_PLASTIC_PALLET => ['Plastic / Wood Pallet'],
		8 => 'sep',
		self::QUOTE_MISC_PALLET_WRAP => ['Pallet Wrap'],
		self::QUOTE_MISC_PALLET_STACKING => ['Pallet Stacking'],
		self::QUOTE_MISC_PALLET_ALL_IN => ['Pallet All In Service'],
		4000 => 'Stock Out B2C',
		self::QUOTE_PICKING_MANUAL_ORDER => ['Picking Manual / Order'],
		self::QUOTE_PICKING_ORDER => ['Picking Min / Order'],
		self::QUOTE_PICKING_UNIT => ['Picking / Unit'],
		self::QUOTE_PICKING_CARTON => ['Picking / Carton'],
		self::QUOTE_PICKING_BAG => ['Picking / Bag'],
		self::QUOTE_PICKING_PALLET => ['Picking / Pallet'],
		self::QUOTE_PICKING_SKU_BASE => ['Picking / SKU base'],
		self::QUOTE_PICKING_SKU_PER => ['Picking / SKU extra'],
		self::QUOTE_PICKING_CHARGECODE => ['Picking By Weight Charge Code'],
		7 => 'sep',
		self::QUOTE_PACKING_ORDER => ['Standard Packing / Parcel'],
		self::QUOTE_PACKING_ORDER_STATUS => ['Standard Packing Status'],
		self::QUOTE_PACKING_ORDER_EXTRA => ['Extra Packing / Parcel'],
		self::QUOTE_PACKING_UNIT => ['Packing / Unit'],
		// self::QUOTE_BOX_FEE => ['Box Fee / Unit'],
		self::QUOTE_PACKING_INSERT => ['Insert / Order'],
		self::QUOTE_PACKING_CHARGECODE => ['Packing By Weight Charge Code'],
		self::QUOTE_PACKING_DELIVERY_LABEL => ['Delivery Label'],
		9 => 'sep',
		self::QUOTE_RETURN_UNIT => ['Return / Unit'],
		5000 => 'Delivery',
		self::QUOTE_MISC_PICKUP_DELIVERY_PALLET => ['Pickup or Delivery / Pallet'],
		self::QUOTE_MISC_PICKUP_DELIVERY_MIN_CHARGE => ['Pickup or Delivery Min Charge'],
		self::QUOTE_MISC_DELIVERY_AIRPORT_PALLET => ['Delivery to Airport / Pallet'],
		self::QUOTE_MISC_DELIVERY_AIRPORT_MIN_CHARGE => ['Delivery to Airport Min Charge'],
		self::QUOTE_MISC_DELIVERY_CHINA => ['Delivery to China'],
		6000 => 'MISC.',
		// for all misc
		self::QUOTE_CYCLE_COUNT_PALLET => ['Cycle Count / Pallet'],
		self::QUOTE_STOCKTAKE_HOUR => ['Stocktake / Hour'],
		self::QUOTE_STOCKTAKE_DAY => ['Stocktake / Day'],
		self::QUOTE_MISC_CHANGE_PALLET => ['Change Plain Pallet'],
		self::QUOTE_MISC_PALLET_HIRE_DAY => ['Chep / Loscam Pallet Hire / Day'],
		self::QUOTE_MISC_LABOUR_HOUR => ['Labour / Hr'],
		self::QUOTE_MISC_OVERTIME_LABOUR_HOUR => ['Overtime Labour / Hr'],
		self::QUOTE_MISC_FORK_TRUCK_HOUR => ['Fork/Reach Truck / Hr'],
		self::QUOTE_MISC_OVERTIME_FOR_TRUCK_HOUR => ['Overtime Fork/Reach Truck / Hr'],
		self::QUOTE_MISC_LABELLING => ['Labelling'],
		7000 => 'SYD Delivery Chargecode.',
		self::QUOTE_COURIER_CHARGECODE => ['Mixed Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_AUPOSR => ['Aupost Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_TNT => ['TNT Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_SF => ['SF Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_ETOLL => ['ETOLL Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_EALLIED => ['EALLIED Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_UTOLL => ['UTOLL Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_UAP => ['UAP Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_MYTOLL => ['MYTOLL Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_BORDER => ['BORDER Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_TLACARGO => ['TLA CARGO Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_FASTWAY => ['FASTWAY Chargecode'],
		self::QUOTE_COURIER_CHARGECODE_AUPOST_EXPRESS => ['Aupost Express Chargecode'],
		self::QUOTE_EXPRESS_CHARGECODE => ['Express Chargecode'],
		self::QUOTE_INTERNATIONAL_CHARGECODE => ['International Chargecode'],
		self::QUOTE_SENDLE_CHARGE => ['Sendle Charge (if don\'t use International Chargecode)'],
		self::QUOTE_LETTER_CHARGECODE => ['Letter Chargecode'],
		9000 => 'MEL Delivery Chargecode.',
		self::QUOTE_COURIER_MEL_CHARGECODE  => ['Mixed Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_AUPOSR  => ['Aupost Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_TNT  => ['TNT Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_SF  => ['SF Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_ETOLL  => ['ETOLL Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_EALLIED  => ['EALLIED Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_UTOLL  => ['UTOLL Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_UAP => ['UAP Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_MYTOLL => ['MYTOLL Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_BORDER => ['BORDER Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_TLACARGO  => ['TLA CARGO Chargecode'],
		self::QUOTE_COURIER_MEL_CHARGECODE_FASTWAY  => ['FASTWAY Chargecode'],
		self::QUOTE_EXPRESS_MEL_CHARGECODE  => ['Express Chargecode'],
		self::QUOTE_INTERNATIONAL_MEL_CHARGECODE  => ['International Chargecode'],
		self::QUOTE_SENDLE_MEL_CHARGE  => ['Sendle Charge (if don\'t use International Chargecode)'],
		self::QUOTE_LETTER_MEL_CHARGECODE  => ['Letter Chargecode'],
		10000 => 'BNE Delivery Chargecode.',
		self::QUOTE_COURIER_BNE_CHARGECODE  => ['Mixed Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_AUPOSR  => ['Aupost Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_TNT  => ['TNT Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_SF  => ['SF Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_ETOLL  => ['ETOLL Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_EALLIED  => ['EALLIED Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_UTOLL  => ['UTOLL Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_UAP => ['UAP Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_MYTOLL => ['MYTOLL Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_TLACARGO  => ['TLA CARGO Chargecode'],
		self::QUOTE_COURIER_BNE_CHARGECODE_FASTWAY  => ['FASTWAY Chargecode'],
		self::QUOTE_EXPRESS_BNE_CHARGECODE  => ['Express Chargecode'],
		self::QUOTE_INTERNATIONAL_BNE_CHARGECODE  => ['International Chargecode'],
		self::QUOTE_SENDLE_BNE_CHARGE  => ['Sendle Charge (if don\'t use International Chargecode)'],
		self::QUOTE_LETTER_BNE_CHARGECODE  => ['Letter Chargecode'],
		8000 => 'Return Service',
		self::QUOTE_RETURN_RTS_STANDARD => ['RTS standard'],
		self::QUOTE_RETURN_LABEL => ['Return Postage'],
		// manual create return check task, no return label
		self::QUOTE_RETURN_MANUAL_INPUT => ['Manual Input'],
		10 => 'sep',
		self::QUOTE_RETURN_RECEIVE_UNIT => ['Stock Receive / Unit'],
		self::QUOTE_RETURN_RECEIVE_2KG => ['Stock Receive up to 2kg'],
		self::QUOTE_RETURN_RECEIVE_5KG => ['Stock Receive up to 5kg'],
		self::QUOTE_RETURN_RECEIVE_10KG => ['Stock Receive up to 10kg'],
		11 => 'sep',
		self::QUOTE_RETURN_STORAGE_PALLET => ['Temporary Storage / Pallet'],
		self::QUOTE_RETURN_STORAGE_2KG => ['Temporary Storage up to 2kg'],
		self::QUOTE_RETURN_STORAGE_5KG => ['Temporary Storage up to 5kg'],
		self::QUOTE_RETURN_STORAGE_10KG => ['Temporary Storage up to 10kg'],
		12 => 'sep',
		self::QUOTE_RETURN_PICKUP_2KG => ['Pick up to 2kg'],
		self::QUOTE_RETURN_PICKUP_5KG => ['Pick up to 5kg'],
		self::QUOTE_RETURN_PICKUP_10KG => ['Pick up to 10kg'],
		13 => 'sep',
		self::QUOTE_RETURN_CHECK_UNPACK => ['Unpack + Check'],
		self::QUOTE_RETURN_CHECK_PICTURE => ['Pictures'],
		self::QUOTE_RETURN_CHECK_EXTRA_SYSTEM_INPUT => ['System Log or Input'],
		14 => 'sep',
		self::QUOTE_RETURN_NEED_PRODUCT_INFO => ['Need Product Information'],
		self::QUOTE_RETURN_RTS_IN_OUT_FEE => ['RTS Stock In & Out Fee'],
		self::QUOTE_RETURN_CAN_RESTOCK => ['Return - Restock'],
		self::QUOTE_RETURN_CAN_REDELIVERY => ['Return - Redelivery'],
	];

	//---------------------------------old------------------------------------------------

	//--------------new---------------new------------new--------------new-----------------
	const THE_QUOTE_STORAGE_ONE = 11001;
	const THE_QUOTE_STORAGE_THREE = 11003;
	const THE_QUOTE_STORAGE_FIVE = 11005;
	const THE_QUOTE_STORAGE_FIVE_PLUS = 11006;
	const THE_QUOTE_STORAGE_FIVE_PALLET = 11007;

	const THE_QUOTE_IQC_ONE = 12001;
	const THE_QUOTE_IQC_THREE = 12003;
	const THE_QUOTE_IQC_FIVE = 12005;
	const THE_QUOTE_IQC_FIVE_PLUS = 12006;
	const THE_QUOTE_IQC_PALLET = 12007;

	const THE_QUOTE_PACKING_ONE = 13001;
	const THE_QUOTE_PACKINGC_THREE = 13003;
	const THE_QUOTE_PACKING_FIVE = 13005;
	const THE_QUOTE_PACKING_FIVE_PLUS = 13006;
	const THE_QUOTE_PACKING_PALLET = 13007;

	const THE_QUOTE_RETURNING_ONE = 14001;
	const THE_QUOTE_RETURNING_THREE = 14003;
	const THE_QUOTE_RETURNING_FIVE = 14005;
	const THE_QUOTE_RETURNING_FIVE_PLUS = 14006;
	const THE_QUOTE_RETURNING_PALLET = 14007;

	const THE_QUOTE_ORDER_CANCEL_ONE = 15001;
	const THE_QUOTE_ORDER_CANCEL_THREE = 15003;
	const THE_QUOTE_ORDER_CANCEL_FIVE = 15005;
	const THE_QUOTE_ORDER_CANCEL_FIVE_PLUS = 15006;

	const THE_QUOTE_CONSUMBLE_ONE = 16001;
	const THE_QUOTE_CONSUMBLE_THREE = 16003;
	const THE_QUOTE_CONSUMBLE_FIVE = 16005;
	const THE_QUOTE_CONSUMBLE_FIVE_PLUS = 16006;

	const THE_QUOTE_CHANGE_PALLET = 17001;
	const THE_QUOTE_CHANGE_LABEL = 17002;
	const THE_QUOTE_PIKING = 17003;

	public static $newChgcodes = [
		1000 => 'Storage Services',
		self::THE_QUOTE_STORAGE_ONE => ['Storage 0kg-1kg/item/day'],
		self::THE_QUOTE_STORAGE_THREE => ['Storage 1kg-3kg/item/day'],
		self::THE_QUOTE_STORAGE_FIVE => ['Storage 3kg-5kg/item/day'],
		self::THE_QUOTE_STORAGE_FIVE_PLUS => ['Storage  5+kg/item/day'],
		self::THE_QUOTE_STORAGE_FIVE_PALLET => ['Storage  5+kg/pallet/day'],
		2000 => 'IQC Services',
		self::THE_QUOTE_IQC_ONE => ['IQC 0kg-1kg/sku'],
		self::THE_QUOTE_IQC_THREE => ['IQC 1kg-3kg/sku'],
		self::THE_QUOTE_IQC_FIVE => ['IQC 3kg-5kg/sku'],
		self::THE_QUOTE_IQC_FIVE_PLUS => ['IQC 5+kg/sku'],
		self::THE_QUOTE_IQC_PALLET => ['IQC /pallet'],
		3000 => 'Packing Services',
		self::THE_QUOTE_PACKING_ONE => ['Packing 0kg-1kg/(sku/pack)'],
		self::THE_QUOTE_PACKINGC_THREE => ['Packing 1kg-3kg/(sku/pack)'],
		self::THE_QUOTE_PACKING_FIVE => ['Packing 3kg-5kg/(sku/pack)'],
		self::THE_QUOTE_PACKING_FIVE_PLUS => ['Packing 5+kg/(sku/pack)'],
		self::THE_QUOTE_PACKING_PALLET => ['Packing Pallet/(sku/pack)'],
		4000 => 'Returning Services',
		self::THE_QUOTE_RETURNING_ONE => ['Returning 0kg-1kg/(sku/item)'],
		self::THE_QUOTE_RETURNING_THREE => ['Returning 1kg-3kg/(sku/item)'],
		self::THE_QUOTE_RETURNING_FIVE => ['Returning 3kg-5kg/(sku/item)'],
		self::THE_QUOTE_RETURNING_FIVE_PLUS => ['Returning 5+kg/(sku/item)'],
		self::THE_QUOTE_RETURNING_PALLET => ['Returning Pallet/(sku/item)'],
		5000 => 'Order Cancel Services',
		self::THE_QUOTE_ORDER_CANCEL_ONE => ['Order Cancel 0kg-1kg/(sku/item)'],
		self::THE_QUOTE_ORDER_CANCEL_THREE => ['Order Cancel 1kg-3kg/(sku/item)'],
		self::THE_QUOTE_ORDER_CANCEL_FIVE => ['Order Cancel 3kg-5kg/(sku/item)'],
		self::THE_QUOTE_ORDER_CANCEL_FIVE_PLUS => ['Order Cancel 5+kg/(sku/item)'],
		6000 => 'Consumble Charge',
		self::THE_QUOTE_CONSUMBLE_ONE => ['Consumble(box) 0kg-1kg/(sku/item)'],
		self::THE_QUOTE_CONSUMBLE_THREE => ['Consumble(box) 1kg-3kg/(sku/item)'],
		self::THE_QUOTE_CONSUMBLE_FIVE => ['Consumble(box) 3kg-5kg/(sku/item)'],
		self::THE_QUOTE_CONSUMBLE_FIVE_PLUS => ['Consumble(box) 5+kg/(sku/item)'],
		7000 => 'Other Services',
		self::THE_QUOTE_CHANGE_PALLET => ['Change Pallet AND Packing Pallet'],
		self::THE_QUOTE_CHANGE_LABEL => ['change label/item'],
		self::THE_QUOTE_PIKING => ['picking /pallet(275Kg)'],
	];

	const QUOTE_MAX_LEN = 4;

	public $mdata = [];
	public $note = '';

	public static $storageChargeType = [
		self::QUOTE_PALLET_STORAGE_WEEK => 'Pallet Storage / Week',
		self::QUOTE_CBM_STORAGE_WEEK => 'CBM Storage / Week',
	];
	/**
	 * create unique quote Number with for digitals or characters
	 */
	private function genQuoteNo($len)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		$max = strlen($characters) - 1;
		for ($i = 0; $i < $len; $i++) {
			$string .= $characters[mt_rand(0, $max)];
		}
		$code = strtoupper($string);
		$existing = WmsOrgQuote::model()->find('quote_no = :no', [':no' => $code]);
		if (!empty($existing)) {
			$this->genQuoteNo($len);
		}

		return $code;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_org_quote';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id', 'required'),
			array('quote_no, meta, vfrom,type, vto', 'safe'),
			array('status', 'numerical', 'integerOnly' => true),
			array('org_id', 'length', 'max' => 11),
			array('quote_no', 'length', 'max' => 20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, quote_no, meta, vfrom, vto,type,status', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * get organization all quotes
	 * @param $orgId
	 */
	public static function getOrgQuotes($orgId)
	{
		// get valid quotes based on currently date
		$curDate = date('Y-m-d');
		$quotes = WmsOrgQuote::model()->find('vfrom <= :dest AND vto >= :dest AND org_id = :oid AND status = 1', [':dest' => $curDate, ':oid' => $orgId]);
		if (empty($quotes) || empty($quotes->mdata)) {
			return false;
		}

		$allQuotes = array();
		foreach (self::$chgcodes as $k => $v) {
			if (isset($quotes->mdata[$k])) {
				$allQuotes[$k] = $quotes->mdata[$k];
			}
		}
		return $allQuotes;
	}

	/**
	 * get organization all quotes by quote
	 * @param $orgId
	 */
	public static function getOrgQuotesByCode($code)
	{
		// get valid quotes based on currently date
		$curDate = date('Y-m-d');
		$quotes = WmsOrgQuote::model()->find('vfrom <= :dest AND vto >= :dest AND quote_no = :code AND status = 1', [':dest' => $curDate, ':code' => $code]);
		if (empty($quotes) || empty($quotes->mdata)) {
			return false;
		}

		$allQuotes = array();
		foreach (self::$chgcodes as $k => $v) {
			if (isset($quotes->mdata[$k])) {
				$allQuotes[$k] = $quotes->mdata[$k];
			}
		}
		return $allQuotes;
	}

	/**
	 * check to see if the same date span existing or not
	 * @param $vfrom
	 * @param $vto
	 */
	public static function isValidDateSpan($orgId, $vfrom, $vto, $excludingId = 0)
	{
		if ($orgId > 0) {
			if ($excludingId > 0) {
				$existing = WmsOrgQuote::model()->find('org_id = :oid AND vfrom <= :dest AND vto >= :dest AND id != :id AND status = 1', [':oid' => $orgId, ':dest' => $vfrom, ':id' => $excludingId]);
				if (!empty($existing)) {
					return false;
				}

				$existing = WmsOrgQuote::model()->find('org_id = :oid AND vfrom <= :dest AND vto >= :dest AND id != :id AND status = 1', [':oid' => $orgId, ':dest' => $vto, ':id' => $excludingId]);
				if (!empty($existing)) {
					return false;
				}

			} else {
				$existing = WmsOrgQuote::model()->find('org_id = :oid AND vfrom <= :dest AND vto >= :dest AND status = 1', [':oid' => $orgId, ':dest' => $vfrom]);
				if (!empty($existing)) {
					return false;
				}

				$existing = WmsOrgQuote::model()->find('org_id = :oid AND vfrom <= :dest AND vto >= :dest AND status = 1', [':oid' => $orgId, ':dest' => $vto]);
				if (!empty($existing)) {
					return false;
				}

			}
		} else {
			if ($excludingId > 0) {
				$existing = WmsOrgQuote::model()->find('vfrom <= :dest AND vto >= :dest AND id != :id AND status = 1', [':dest' => $vfrom, ':id' => $excludingId]);
				if (!empty($existing)) {
					return false;
				}

				$existing = WmsOrgQuote::model()->find('vfrom <= :dest AND vto >= :dest AND id != :id AND status = 1', [':dest' => $vto, ':id' => $excludingId]);
				if (!empty($existing)) {
					return false;
				}

			} else {
				$existing = WmsOrgQuote::model()->find('vfrom <= :dest AND vto >= :dest AND status = 1', [':dest' => $vfrom]);
				if (!empty($existing)) {
					return false;
				}

				$existing = WmsOrgQuote::model()->find('vfrom <= :dest AND vto >= :dest AND status = 1', [':dest' => $vto]);
				if (!empty($existing)) {
					return false;
				}

			}
		}
		return true;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}

	public function beforeSave()
	{
		if (!empty($this->note)) {
			$this->mdata['note'] = $this->note;
		}

		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);
		if (!isset($this->status)) {
			$this->status = 1;
		}

		if (empty($this->quote_no)) {
			$this->quote_no = $this->genQuoteNo(self::QUOTE_MAX_LEN);
		}

		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}

		if (isset($this->mdata['note'])) {
			$this->note = $this->mdata['note'];
		}

		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'quote_no' => 'Quote No',
			'meta' => 'Meta',
			'note' => 'Note',
			'vfrom' => 'Vfrom',
			'vto' => 'Vto',
			'status' => 'Status',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.org_id', $this->org_id);
		$criteria->compare('quote_no', $this->quote_no, true);
		$criteria->compare('vfrom', $this->vfrom, true);
		$criteria->compare('vto', $this->vto, true);
		$criteria->compare('t.status', $this->status);

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public static $states = array(
		0 => 'Disable',
		1 => 'Active',
	);

	public function getStatus()
	{
		return isset(static::$states[$this->status]) ? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsOrgQuote the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
