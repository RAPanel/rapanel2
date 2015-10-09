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
            [['type'], 'string', 'max' => 32]
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
        $result = ['Тип: ' . $this->type];
        foreach (unserialize($this->data) as $key => $row) {
            $result[] = $this->getAttributeLabel($key) . ": " . $row;
        }
        return implode(PHP_EOL, $result);
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email = null)
    {
        preg_match('#(\w+?)(?:Form)?$#iu', $this::className(), $matches);
        $this->type = lcfirst($matches[1]);
        if ($this->save()) {
            $mail = Yii::$app->mailer->compose();
            $mail->setTo($email ?: Yii::$app->params['adminEmail']);
            $mail->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name]);
            $mail->setSubject('Сообщение с сайта ' . Yii::$app->name);
            $mail->setTextBody($this->body);

            if (!empty($this->email)) $mail->setReplyTo([$this->email]);

            $mail->send();

            return true;
        }
        return false;
    }
}
