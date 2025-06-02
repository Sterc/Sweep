<?php

namespace Sweep\Processors\Item;

use Sweep\Model\SweepItem;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOQuery;
use xPDO\Om\xPDOObject;

class GetList extends GetListProcessor
{
    public $objectType = 'SweepItem';
    public $classKey = SweepItem::class;
    public $defaultSortField = 'size';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';

    /**
     * We do a special check of permissions
     * because our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $used = !empty($this->getProperty('used'));
        if ($used) {
            $c->where([
                'usedin:IS NOT' => null,
                'usedin:!=' => '',
            ]);
        } else {
            $c->where([
                'usedin:IS' => null,
                'OR:usedin:=' => '',
            ]);
        }

        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where([
                'name:LIKE' => "%{$query}%",
                'OR:path:LIKE' => "%{$query}%",
            ]);
        }

        return $c;
    }

    public function outputArray(array $array, $count = false)
    {
        $output = json_decode(parent::outputArray($array, $count), true);

        $q = $this->modx->newQuery($this->classKey);
        $q->select([
            'total_size'  => 'SUM(`size`)',
            'used_size'   => 'SUM(IF(`usedin` IS NOT NULL AND `usedin` != \'\', `size`, 0))',
            'unused_size' => 'SUM(IF(`usedin` IS NULL OR `usedin` = \'\', size, 0))'
        ]);

        $q->prepare();
        $q->stmt->execute();
        $result = $q->stmt->fetch(\PDO::FETCH_ASSOC);
        
        $output = array_merge($output, $result);

        return json_encode($output, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['actions'] = [];

        /*
        $array['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('sweep_item_update'),
            //'multiple' => $this->modx->lexicon('sweep_items_update'),
            'action' => 'updateItem',
            'button' => true,
            'menu' => true,
        ];

        if (!$array['active']) {
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-play-circle-o action-green',
                'title' => $this->modx->lexicon('sweep_item_enable'),
                'multiple' => $this->modx->lexicon('sweep_items_enable'),
                'action' => 'enableItem',
                'button' => true,
                'menu' => true,
            ];
        } else {
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('sweep_item_disable'),
                'multiple' => $this->modx->lexicon('sweep_items_disable'),
                'action' => 'disableItem',
                'button' => true,
                'menu' => true,
            ];

            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-list-alt action-gray',
                'title' => $this->modx->lexicon('sweep_item_archive'),
                'multiple' => $this->modx->lexicon('sweep_items_archive'),
                'action' => 'arciveItem',
                'button' => true,
                'menu' => true,
            ];
        }
        */

        $used = !empty($this->getProperty('used'));
        if (!$used) {
            // Remove
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-trash-o action-red',
                'title' => $this->modx->lexicon('sweep_item_remove'),
                'multiple' => $this->modx->lexicon('sweep_items_remove'),
                'action' => 'removeItem',
                'button' => true,
                'menu' => true,
            ];
        }

        return $array;
    }
}
