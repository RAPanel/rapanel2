<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 10.04.2015
 * Time: 10:14
 */

namespace app\admin\widgets;


class Menu extends \yii\widgets\Menu
{
    public $data = [];
    public $levels = false;

    public function run()
    {
        /** @var \app\admin\models\Page $row */
        if (count($this->data)) {
            foreach (array_reverse($this->data) as $row)
                $this->items[$row->hasAttribute('parent_id') ? $row->parent_id : 0][] = [
                    'label' => $row->getLabel(),
                    'url' => $row->getHref(false),
                    'active' => $row->getActive(),
                    'items' => $this->levels && $row->is_category && isset($this->items[$row->id]) ? array_reverse($this->items[$row->id]) : null,
                ];

            if ($this->items[min(array_keys($this->items))])
                $this->items = $this->items[min(array_keys($this->items))];
            $this->items = array_reverse($this->items);
        }

        parent::run();
    }

}