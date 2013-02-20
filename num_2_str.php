<?php

/*
*	Convert a number into a localized string (Georgian).
*	Only numbers in [2^31; 2^31-1] range (+/- ~2 milliards, 2*(10^9)) are supported.
*
*	Numeric conversion:		alex_sh
*	Conversion with currency:	Stichoza
*/

function translate_number($number, $to_currency = false, $currency_1 = "ლარი", $currency_2 = "თეთრი") {
	if ($to_currency) {
		$money_1 = intval($number);
		$money_2 = abs($number-$money_1);
		$money_2 = (empty($money_2)) ? 0 : round($money_2, 2)*100;
		return translate_number_ge($money_1) . " " . $currency_1
		. " და " . $money_2 . " " . $currency_2;
	} else {
		return translate_number_ge($number);
	}
}

function _translate_lookup_code_ge($num_code) {
	static $number_names = array(
		"number_minus" => "მინუს",
		"number_0" => "ნული",
		"number_1" => "ერთი",
		"number_1_" => "ერთი", // not really needed
		"number_2" => "ორი",
		"number_2_" => "ორ",
		"number_3" => "სამი",
		"number_3_" => "სამ",
		"number_4" => "ოთხი",
		"number_4_" => "ოთხ",
		"number_5" => "ხუთი",
		"number_5_" => "ხუთ",
		"number_6" => "ექვსი",
		"number_6_" => "ექვს",
		"number_7" => "შვიდი",
		"number_7_" => "შვიდ",
		"number_8" => "რვა",
		"number_8_" => "რვა",
		"number_9" => "ცხრა",
		"number_9_" => "ცხრა",
		"number_10" => "ათი",
		"number_11" => "თერთმეტი",
		"number_12" => "თორმეტი",
		"number_13" => "ცამეტი",
		"number_14" => "თოთხმეტი",
		"number_15" => "თხუთმეტი",
		"number_16" => "თექვსმეტი",
		"number_17" => "ჩვიდმეტი",
		"number_18" => "თვრამეტი",
		"number_19" => "ცხრამეტი",
		"number_20" => "ოცი",
		"number_20_" => "ოცდა",
		"number_40" => "ორმოცი",
		"number_40_" => "ორმოცდა",
		"number_60" => "სამოცი",
		"number_60_" => "სამოცდა",
		"number_80" => "ოთხმოცი",
		"number_80_" => "ოთხმოცდა",
		"number_100" => "ასი",
		"number_100_" => "ას",
		"number_1000" => "ათასი",
		"number_1000_" => "ათას",
		"number_1000000" => "მილიონი",
		"number_1000000_" => "მილიონ",
		"number_1000000000" => "მილიარდი",
		"number_1000000000_" => "მილიარდ"

		// the higher numbers may be implemented in short-scale or long-scale styles.
		// we don't need them anyway.
	);
	return (isset($number_names[$num_code]) ? $number_names[$num_code] : "");
}

function translate_number_ge($num) {
	if (!is_numeric($num))
		$num = 0;

	if ($num < 0)
		return _translate_lookup_code_ge("number_minus", "ge") . " " . translate_number_ge(-$num);

	if ($num <= 20 || $num == 40 || $num == 60 || $num == 80 || $num == 100)
		return _translate_lookup_code_ge("number_{$num}", "ge");

	if ($num < 40)
		return _translate_lookup_code_ge("number_20_", "ge") . translate_number_ge($num - 20);

	if ($num < 60)
		return _translate_lookup_code_ge("number_40_", "ge") . translate_number_ge($num - 40);

	if ($num < 80)
		return _translate_lookup_code_ge("number_60_", "ge") . translate_number_ge($num - 60);

	if ($num < 100)
		return _translate_lookup_code_ge("number_80_", "ge") . translate_number_ge($num - 80);


	if ($num < 1000) {
		$digit = ($num - ($num % 100)) / 100;
		$remainder = ($num % 100);
		if ($remainder == 0) {
			return ($digit == 1 ? "" : _translate_lookup_code_ge("number_{$digit}_", "ge"))
			. _translate_lookup_code_ge("number_100", "ge");
		}

		return ($digit == 1 ? "" : _translate_lookup_code_ge("number_{$digit}_", "ge"))
		. _translate_lookup_code_ge("number_100_", "ge") . " " . translate_number_ge($remainder);
	}

	if ($num == 1000)
		return _translate_lookup_code_ge("number_1000", "ge");


	if ($num < pow(10, 6)) {
		$digit = ($num - ($num % 1000)) / 1000;
		$remainder = ($num % 1000);

		if ($remainder == 0)
			return translate_number_ge($digit) . " " . _translate_lookup_code_ge("number_1000", "ge");

		if ($digit == 1) {
			// don't do "erti atas ..."
			//	return _translate_lookup_code_ge("number_1_", "ge") . " "
			//	. _translate_lookup_code_ge("number_1000_", "ge") . " " . translate_number_ge($remainder);
		 	return _translate_lookup_code_ge("number_1000_", "ge") . " " . translate_number_ge($remainder);
		}

		return translate_number_ge($digit) . " " . _translate_lookup_code_ge("number_1000_", "ge")
		. " " . translate_number_ge($remainder);
	}

	if ($num == pow(10, 6))
		return _translate_lookup_code_ge("number_1", "ge") . " " . _translate_lookup_code_ge("number_1000000", "ge");

	if ($num < pow(10, 9)) {
		$digit = ($num - ($num % pow(10, 6))) / pow(10, 6);
		$remainder = ($num % pow(10, 6));

		if ($remainder == 0)
			return translate_number_ge($digit) . " " . _translate_lookup_code_ge("number_1000000", "ge");

		//	if ($digit == 1)
		//	return _po("number_1_") . " " . _po("number_1000000_") . " " . translate_number_ge($remainder);
		return translate_number_ge($digit) . " " . _translate_lookup_code_ge("number_1000000_", "ge")
		. " " . translate_number_ge($remainder);
	}

	if ($num == pow(10, 9))
		return _translate_lookup_code_ge("number_1", "ge") . " " . _translate_lookup_code_ge("number_1000000000", "ge");


	if ($num > pow(10, 9)) {
		$digit = ($num - ($num % pow(10, 9))) / pow(10, 9);
		$remainder = ($num % pow(10, 9));

	if ($remainder == 0)
		return translate_number_ge($digit) . " " . _translate_lookup_code_ge("number_1000000000", "ge");

		//	if ($digit == 1)
		//	return _po("number_1_") . " " . _po("number_1000000000_") . " " . translate_number_ge($remainder);
	return translate_number_ge($digit) . " " . _translate_lookup_code_ge("number_1000000000_", "ge")
	   . " " . translate_number_ge($remainder);
	}
	return $num;
}

?>
