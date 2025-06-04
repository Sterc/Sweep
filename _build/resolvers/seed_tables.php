<?php

use Sweep\Model\SweepDirectory;

/** @var xPDO\Transport\xPDOTransport $transport */
/** @var array $options */
/** @var  MODX\Revolution\modX $modx */

if ($transport->xpdo) {
    $modx = $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx->addPackage('Sweep\Model', MODX_CORE_PATH . 'components/sweep/src/', null, 'Sweep\\');
            if ($modx->getCount(SweepDirectory::class) === 0) {
                $object = $modx->newObject(SweepDirectory::class);
                $object->set('path', 'uploads/');
                $object->save();
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;
