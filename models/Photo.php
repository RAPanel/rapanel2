<?php

namespace app\admin\models;

use app\admin\helpers\Image;
use Exception;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\HttpException;

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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%photo}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_id', 'owner_id', 'model', 'type', 'name', 'width', 'height', 'about', 'cropParams', 'hash'], 'required'],
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
            'id' => Yii::t('ra/model', 'ID'),
            'sort_id' => Yii::t('ra/model', 'Sort ID'),
            'owner_id' => Yii::t('ra/model', 'Owner ID'),
            'model' => Yii::t('ra/model', 'Model'),
            'type' => Yii::t('ra/model', 'Type'),
            'name' => Yii::t('ra/model', 'Name'),
            'width' => Yii::t('ra/model', 'Width'),
            'height' => Yii::t('ra/model', 'Height'),
            'about' => Yii::t('ra/model', 'About'),
            'cropParams' => Yii::t('ra/model', 'Crop Params'),
            'hash' => Yii::t('ra/model', 'Hash'),
            'updated_at' => Yii::t('ra/model', 'Updated At'),
            'created_at' => Yii::t('ra/model', 'Created At'),
        ];
    }


    public static $tmpPath = 'image/tmp';
    public static $path = 'image';

    public function init()
    {
        $this->on(self::EVENT_BEFORE_VALIDATE, function ($event) {
            if (is_null($event->sender->type))
                $event->sender->type = 'main';
            if (is_null($event->sender->sort_id))
                $event->sender->sort_id = 0;
            if (is_null($event->sender->cropParams))
                $event->sender->cropParams = serialize([]);
        });
        parent::init();
    }

    public static function add($file, $about, $owner_id, $model)
    {
        $name = basename($file);
        $newFile = Yii::getAlias('@app/../' . self::$tmpPath . '/') . $name;

        FileHelper::createDirectory(dirname($newFile));
        if (Image::thumbnail($file, 1920)->save($newFile, [
            'quality' => 100,
            'png_compression_level' => 9,
        ])
        ) $file = $newFile;

        list($width, $height) = getimagesize($file);
        $hash = md5($file);

        $class = new self;
        $class->setAttributes(compact('owner_id', 'model', 'name', 'width', 'height', 'about', 'hash'));
        if ($class->save())
            return $class;

        throw new HttpException(400, implode("\n", $class->errors));
    }

    public function getFile($global = false)
    {
        return Yii::getAlias(($global ? '@app/..' : '@web') . '/' . self::$tmpPath . '/' . $this->name);
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

    public function getHref($type, $scheme = false)
    {
        return Url::to(['/image/index', 'type' => $type, 'name' => $this->name], $scheme);
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
