<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.09.2015
 * Time: 11:50
 */

namespace ra\admin\traits;


trait CartItem
{
    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return 0;
    }

    public function getQuantity()
    {
        return 1;
    }

}