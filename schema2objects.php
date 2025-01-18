<?php

require_once __DIR__ . '/vendor/autoload.php';

$schemaPath = __DIR__ . '/schema/';
$schemaData = json_decode(file_get_contents($schemaPath . 'schema.json'));

$s2c_config = '
targetPHPVersion: "8.4"
files:
';

foreach ($schemaData->definitions as $name => $definition) {
    file_put_contents($schemaPath.'generated/'. $name.'.json', json_encode($definition, JSON_PRETTY_PRINT));
    
    if (!isset($definition->type) || $definition->type !== 'object') {
        echo "Can't support $name\n";
        continue;
    }
    
    $s2c_config .= '  - input: schema/generated/'. $name.'.json
    className: ' . $name . '
    targetDirectory: src/Types
    targetNamespace: "ModelContextProtocol\\\Types"
';
}

file_put_contents(__DIR__ . '/.s2c.yaml', $s2c_config);
