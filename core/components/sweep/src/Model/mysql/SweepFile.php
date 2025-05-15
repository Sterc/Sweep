<?php
namespace Sweep\Model\mysql;

use xPDO\xPDO;

class SweepFile extends \Sweep\Model\SweepFile
{

    public static $metaMap = array (
        'package' => 'Sweep\\Model',
        'version' => '3.0',
        'table' => 'sweep_files',
        'extends' => 'xPDO\\Om\\xPDOObject',
        'engine' => 'InnoDB CHARSET=utf8 COLLATE=utf8_general_ci',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB CHARSET=utf8 COLLATE=utf8_general_ci',
        ),
        'fields' => 
        array (
            'path' => '',
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
        ),
        'indexes' => 
        array (
            'PRIMARY' => 
            array (
                'alias' => 'PRIMARY',
                'primary' => true,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'path' => 
                    array (
                        'length' => '1024',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
    );

}
