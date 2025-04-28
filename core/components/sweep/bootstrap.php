<?php

/**
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */

// Load the classes
$modx->addPackage('Sweep\Model', $namespace['path'] . 'src/', null, 'Sweep\\');

$modx->services->add('Sweep', function ($c) use ($modx) {
    return new Sweep\Sweep($modx);
});
