<?php

// Prepare the data that we want represented as JSON
$d = [
   'version' => 'rhea-1.0-109013',
   'url' => 'https://www.fuzzwork.co.uk/dump/rhea-1.0-109013/',
   'format' => '.sql.bz2',
   'tables' => [
      'dgmTypeAttributes',
      'invCategories',
      'invContrabandTypes',
      'invControlTowerResourcePurposes',
      'invControlTowerResources',
      'invFlags',
      'invGroups',
      'invItems',
      'invMarketGroups',
      'invMetaGroups',
      'invMetaTypes',
      'invNames',
      'invPositions',
      'invTypeMaterials',
      'invTypeReactions',
      'invTypes',
      'invUniqueNames',
      'mapDenormalize',
      'staStations'
   ]
];

// Print the resultant JSON
print_r(json_encode($d) . PHP_EOL);

