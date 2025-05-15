<?php
namespace Sweep\Model\mysql;

use xPDO\xPDO;

class SweepDirectory extends \Sweep\Model\SweepDirectory
{

    public static $metaMap = array (
        'package' => 'Sweep\\Model',
        'version' => '3.0',
        'table' => 'sweep_directories',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
        'engine' => 'InnoDB CHARSET=utf8 COLLATE=utf8_general_ci',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB CHARSET=utf8 COLLATE=utf8_general_ci',
        ),
        'fields' => 
        array (
            'path' => '',
            'active' => 1,
        ),
        'fieldMeta' => 
        array (
            'path' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '1024',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'active' => 
            array (
                'dbtype' => 'tinyint',
                'precision' => '1',
                'phptype' => 'boolean',
                'null' => true,
                'default' => 1,
            ),
        ),
        'indexes' => 
        array (
            'path' => 
            array (
                'alias' => 'path',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'path' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'active' => 
            array (
                'alias' => 'active',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'active' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
    );

}
