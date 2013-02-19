# money-num-to-string

Convert a number or money into a localized string (Georgian).
რიცხვების გადაყვანა სიტყვიერად, ქართულ ენაზე. როგორც უბრალო რიცხვი, ასევე თანხა.

## Usage
### Numeric conversion:

666 will be converted to ექვსას სამოცდაექვსი.

	<?php
	echo translate_number(666);
	?>

### Currency conversion

Default currency is "ლარი" and "თეთრი".

	<?php
	echo translate_number(120.5, true);
	?>

You can also set your own currency strings.

	<?php
	echo translate_number(1.8, true, "მანეთი", "კაპიკი");
	?>
