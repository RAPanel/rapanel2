<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 23.09.2015
 * Time: 14:30
 */

namespace app\admin\components;

use app\admin\helpers\RA;
use app\models\Shop;
use Yii;
use yii\helpers\Url;

/**
 * Yml генератор каталога.
 */
class YmlCatalog extends YmlGenerator
{

    protected function shopInfo()
    {
        return array(
            'name' => Yii::$app->name,
            'company' => Yii::$app->name,
            'url' => Url::to(['site/index'], true),
            'platform' => 'RAPanel on Yii2',
            'version' => '2.0',
            'agency' => 'ReRe',
            'email' => 'mail@rere-design.ru'
        );
    }

    protected function currencies()
    {
        foreach ([
                     'RUR' => '1',
                 ] as $key => $val)
            $this->addCurrency($key, $val);
    }

    protected function categories()
    {
        $categories = Shop::findActive(['tyres', 'disks'], ['is_category' => 1])->select(['id', 'name', 'parent_id'])->orderBy('lft')->asArray()->all();
        foreach ($categories as $row)
            $this->addCategory($row['name'], $row['id'], $row['parent_id']);
    }

    protected function offers()
    {
        $offers = Shop::findActive(['tyres', 'disks'], ['is_category' => 0]);
        /** @var Shop $row */
        foreach ($offers->each() as $row)
            $this->addOffer($row->id, array(
                'url' => $row->getHref(true, true),
                'price' => $row->getPrice(),
                'currencyId' => 'RUR',
                'categoryId' => $row->parent_id,
                'picture' => $row->parent->photo ? $row->parent->getPhotoHref('800') : false,
                'store' => false,
                'pickup' => false,
                'delivery' => true,
                'local_delivery_cost' => 0,
                'typePrefix'=>RA::module($row->module_id, 'name'),
                'vendor' => $row->getCharacters('brand'),
//                'vendorCode',
                'model' => $row->name,
                'description' => $row->about ?: $row->parent->about,
                'sales_notes' => '',
            ), array(), false, 'vendor.model', '10', '10');
    }
}