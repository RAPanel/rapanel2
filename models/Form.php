<?php

namespace ra\admin\models;

use ra\admin\traits\SerializeAttribute;
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
    public $serializeAttributes = ['name', 'phone', 'email', 'text', 'fromUrl'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form}}';
    }

    public function init()
    {
        parent::init();

        if (empty($this->fromUrl)) {
            $this->fromUrl = Yii::$app->request->hostInfo . Yii::$app->request->url;
            if (Yii::$app->request->referrer && Yii::$app->request->url == \yii\helpers\Url::to(['/site/contact']))
                $this->fromUrl = Yii::$app->request->referrer;
        }
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
            'id' => Yii::t('ra', 'ID'),
            'type' => Yii::t('ra', 'Type'),
            'data' => Yii::t('ra', 'Data'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
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
    public function contact($email = null, $cc = null)
    {
        preg_match('#(\w+?)(?:Form)?$#iu', $this::className(), $matches);
        $this->type = lcfirst($matches[1]);

        if (Yii::$app->request->isPost && $this->load(Yii::$app->request->post()) && $this->validate()) {
            $mail = Yii::$app->mailer->compose();
            $mail->setTo(explode(',', $email ?: Yii::$app->params['adminEmail']));
            $mail->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name]);
            $mail->setSubject((Yii::$app->request->post($this->spamAttribute) ? '[SPAM] ' : '') . 'Сообщение с сайта ' . Yii::$app->name);
            $mail->setTextBody($this->body);

            if (!empty($this->email))
                $mail->setReplyTo($this->name ? [$this->email => $this->name] : $this->email);

            if ($this->file) foreach ($this->file as $file)
                $mail->attach($file->tempName, [
                    'fileName' => $file->name,
                    'contentType' => $file->type,
                ]);

            if ($cc && !Yii::$app->request->post($this->spamAttribute))
                $mail->setCc(explode(',', $cc));

            if ($mail->send() || YII_ENV_DEV) {
                Yii::$app->session->setFlash('success', 'Сообщение успешно отправлено');
                return true;
            }
        }
        return false;
    }
}
