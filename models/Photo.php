<?php

namespace ra\admin\models;

use Exception;
use ra\admin\helpers\Image;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%photo}}".
 *
 * @property string $id
 * @property string $sort_id
 * @property string $owner_id
 * @property string $model
 * @property string $type
 * @property string $name
 * @property string $width
 * @property string $height
 * @property string $about
 * @property string $cropParams
 * @property string $hash
 * @property string $updated_at
 * @property string $created_at
 */
class Photo extends \yii\db\ActiveRecord
{
    public static $tmpPath = 'image/tmp';
    public static $path = 'image';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%photo}}';
    }

    public static function add($file, $about, $owner_id, $options)
    {
        preg_match('#.\w{1,4}$#', basename($file), $ext);
        $filename = preg_replace('#.\w{1,4}$#', '', basename($file));
        $newFile = Yii::getAlias('@app/../' . self::$tmpPath . '/') . strtolower(Inflector::slug($filename) . $ext[0]);

        if (file_exists($newFile)) {
            $existFile = $newFile;
            $newFile = str_replace(basename($existFile), uniqid() . '-' . basename($file), $existFile);
        } else
            FileHelper::createDirectory(dirname($newFile));

        if (Image::thumbnail($file, 1920)->save($newFile, [
            'quality' => 100,
            'png_compression_level' => 9,
        ])
        ) {
            if (isset($existFile) && md5_file($existFile) == md5_file($newFile)) {
                $file = $existFile;
                unlink($existFile);
                copy($newFile, $existFile);
                unlink($newFile);
                $model = self::findOne(['name' => basename($file), 'owner_id' => $owner_id]);
            } else
                $file = $newFile;
        }

        list($width, $height) = getimagesize($file);
        $hash = md5_file($file);
        $name = basename($file);

        if (empty($model)) $model = new self;
        if (is_string($options)) $options = ['model' => $options];
        $model->setAttributes(compact('owner_id', 'name', 'width', 'height', 'about', 'hash') + $options);

        if ($model->save())
            return $model;

        throw new \yii\base\Exception(print_r($model->errors, 1));
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_id', 'owner_id', 'model', 'type', 'name', 'width', 'height', 'cropParams', 'hash'], 'required'],
            [['sort_id', 'owner_id', 'width', 'height'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['model', 'hash'], 'string', 'max' => 32],
            [['type'], 'string', 'max' => 8],
            [['name', 'about', 'cropParams'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'sort_id' => Yii::t('ra', 'Sort ID'),
            'owner_id' => Yii::t('ra', 'Owner ID'),
            'model' => Yii::t('ra', 'Model'),
            'type' => Yii::t('ra', 'Type'),
            'name' => Yii::t('ra', 'Name'),
            'width' => Yii::t('ra', 'Width'),
            'height' => Yii::t('ra', 'Height'),
            'about' => Yii::t('ra', 'About'),
            'cropParams' => Yii::t('ra', 'Crop Params'),
            'hash' => Yii::t('ra', 'Hash'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
        ];
    }

    public function init()
    {
        $this->on(self::EVENT_BEFORE_VALIDATE, function ($event) {
            if (is_null($event->sender->type))
                $event->sender->type = 'main';
            if (is_null($event->sender->sort_id))
                $event->sender->sort_id = 0;
            if (is_null($event->sender->cropParams))
                $event->sender->cropParams = serialize([]);
            if (!$event->sender->about)
                $event->sender->about = preg_replace('#.\w+$#iu', '', $event->sender->name);
        });
        parent::init();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'owner_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert && ($file = $this->getFile(true))) {
            if (file_exists($file)) {
                list($this->width, $this->height) = getimagesize($file);
                $this->hash = md5_file($file);
            } else throw new Exception('File not found in tmp dir ' . $file);
        }

        return parent::beforeSave($insert);
    }

    public function getFile($global = false)
    {
        return Yii::getAlias(($global ? '@app/..' : '@web') . '/' . self::$tmpPath . '/' . $this->name);
    }

    public function getImg($size, $options = [])
    {
        if (empty($options['alt'])) $options['alt'] = $this->about;
        $options = array_merge($options, $this->getSizes($size));
        return Html::img($this->getHref($size), $options);
    }

    public function getSizes($size)
    {
        if (strpos($size, 'x') !== false)
            list($width, $height) = explode('x', $size);
        else $width = $height = (int)$size;
        $k = $this->width / $this->height;
        if ($k > 1) $height = $width / $k;
        else $width = $height * $k;

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    public function getHref($size, $scheme = false)
    {
        return Url::to(['/image/index', 'type' => $size, 'name' => $this->name], $scheme);
    }

    public function beforeDelete()
    {
        if ($fileName = $this->name)
            FileHelper::findFiles(Yii::getAlias('@webroot/image'), ['filter' => function ($file) use ($fileName) {
                if (basename($file) == $fileName) unlink($file);
                return is_dir($file);
            }, 'recursive' => true]);

        return parent::beforeDelete();
    }
}
