<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/Sweep/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/sweep')) {
            $cache->deleteTree(
                $dev . 'assets/components/sweep/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/sweep/', $dev . 'assets/components/sweep');
        }
        if (!is_link($dev . 'core/components/sweep')) {
            $cache->deleteTree(
                $dev . 'core/components/sweep/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/sweep/', $dev . 'core/components/sweep');
        }
    }
}

return true;
