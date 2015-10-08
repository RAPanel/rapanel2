<?php

namespace app\admin\models;

use app\admin\traits\SerializeAttribute;
use Yii;

/**
 * This is the model class for table "{{%form}}".
 *
 * @property string $id
 * @property string $type
 * @property resource $data
 * @property string $updated_at
 * @property string $created_at
 */
class Form extends \yii\db\ActiveRecord
{
    use SerializeAttribute;
    public $serializeAttributes = ['name', 'phone', 'email', 'text'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['data'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['type'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra/model', 'ID'),
            'type' => Yii::t('ra/model', 'Type'),
            'data' => Yii::t('ra/model', 'Data'),
            'updated_at' => Yii::t('ra/model', 'Updated At'),
            'created_at' => Yii::t('ra/model', 'Created At'),
        ];
    }

    public function getBody()
    {

    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email = null)
    {
        if ($this->save()) {
            Yii::$app->mailer->compose()
                ->setTo($email ?: Yii::$app->params['adminEmail'])
                ->setFrom([Yii::$app->params['fromEmail']=> Yii::$app->name])
                ->setReplyTo([$this->email])
                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }
}
