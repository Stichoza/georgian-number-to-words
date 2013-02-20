# money-num-to-string

Convert a number or money into a localized string (Georgian).
რიცხვების გადაყვანა სიტყვიერად, ქართულ ენაზე. როგორც უბრალო რიცხვი, ასევე თანხა.

Supported range is **`(-1,000,000,000,000; 1,000,000,000,000)`**, but higher numbers may be also implemented.

### Currently available
- PHP
- JavaScript

## Usage
### Numeric conversion:

321 will be converted to სამას ოცდაერთი.

	<?php
	echo translate_number(321);
	?>

### Currency conversion

Default currency is "ლარი" and "თეთრი".

	<?php
	echo translate_number(120.5, true);	// ას ოცდახუთი ლარი და 50 თეთრი
	?>

You can also set your own currency strings.

	<?php
	echo translate_number(1.8, true, "მანეთი", "კაპიკი"); // ერთი მანეთი და 80 კაპიკი
	?>
