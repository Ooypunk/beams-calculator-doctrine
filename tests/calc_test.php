<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ooypunk\BeamsCalculatorLib\Calculator\Calculator;
use Ooypunk\BeamsCalculatorLib\Register\Register;
use Ooypunk\BeamsCalculatorLib\Parts\PartsListFactory;
use Ooypunk\BeamsCalculatorLib\Materials\StoreFactory;

$register = Register::getInstance();
$register->saw_width = 3;

$header_map = [
	'Lengte' => 'length',
	'Breedte' => 'width',
	'Dikte' => 'height',
	'Label' => 'label',
	'Aantal' => 'qty',
];

$parts_list_factory = new PartsListFactory();
$parts_list = $parts_list_factory->fromCsvFile(__DIR__ . '/parts.csv', $header_map);
$store_factory = new StoreFactory();
$materials_store = $store_factory->fromCsvFile(__DIR__ . '/materials.csv', $header_map);

$calc = new Calculator($materials_store, $parts_list);
$calc->runCalc();

print 'Calculations count: ' . $calc->getCalculationsCount() . "\n";

$fh = fopen('var/calc_test_report.' . date('Ymd_Hi'), 'w');
foreach ($calc->getLeastWasteScenarios() as $scenario) {
	foreach ($scenario->getUsedMaterials() as $used_material) {
		fwrite($fh, $used_material->getMaterialLabel() . "\n");
		foreach ($used_material->getParts() as $part) {
			fwrite($fh, "\t" . $part->getLabelWithDims() . "\n");
		}
		fwrite($fh, "\n");
	}
	fwrite($fh, "\n");
}
fclose($fh);
die('@debug in file: ' . __FILE__ . '@' . __LINE__ . "\n");
