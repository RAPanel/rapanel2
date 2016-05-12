<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.09.2015
 * Time: 11:47
 */

namespace ra\admin\components;

use ra\admin\models\Cart;
use Yii;
use yii\base\Component;

class ShoppingCart extends Component
{
    public $attributes;
    private $_items = false;

    public function put($model, $quantity = 1)
    {
        $this->update($model, $quantity, true);
    }

    /**
     * @param $model \ra\admin\traits\CartItem
     */
    public function update($model, $quantity, $add = false)
    {
        foreach ($this->getItems() as $row)
            if ($row->item_id == $model->getId()) {
                $item = $row;
                break;
            }
        if (isset($item) && !$quantity) {
            $item->delete();
            return;
        }
        if (empty($item)) {
            $item = new Cart;
            $item->setAttributes(['session_id' => $this->getSessionId(), 'item_id' => $model->getId(), 'status' => 0, 'order_id' => 0], false);
        }
        $item->data = $model;
        $item->price = $model->getPrice(0);
        $item->quantity = $add ? ($item->quantity + $quantity) : $quantity;
        $item->save(false);
    }

    /**
     * @return Cart[]
     */
    public function getItems()
    {
        if ($this->_items === false)
            $this->_items = Cart::findAll(['session_id' => $this->getSessionId(), 'status' => 0, 'order_id' => 0]);
        return $this->_items ?: [];
    }

    public function getSessionId()
    {
        if (isset($_COOKIE['PHPSESSID'])) return $_COOKIE['PHPSESSID'];
        return Yii::$app->user->getId() ?: Yii::$app->session->hasSessionId || !Yii::$app->session->open() ? Yii::$app->session->getId() : '';
    }

    public function delete($id)
    {
        foreach ($this->getItems() as $row)
            if ($row->item_id == $id) {
                $item = $row;
                break;
            }
        if (!empty($item)) return $item->delete();
        return false;
    }

    public function getQuantity()
    {
        $quantity = 0;
        foreach ($this->getItems() as $row)
            $quantity += $row['quantity'];
        return $quantity;
    }

    public function getCount()
    {
        return count($this->getItems());
    }

    public function getCost()
    {
        $total = 0;
        foreach ($this->getItems() as $row)
            $total += $row['price'] * $row['quantity'];
        return $total;
    }

    public function clear()
    {
        foreach ($this->getItems() as $row)
            $row->delete();
        $this->_items = false;
    }

    public function toOrder($order_id)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($this->getItems() as $row) {
            $row->status = 1;
            $row->order_id = $order_id;
            $row->update(false, ['status', 'order_id']);
        }
        $this->_items = false;
        $transaction->commit();
    }
}