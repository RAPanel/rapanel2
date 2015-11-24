<?php

namespace ra\admin\models;

use ra\admin\traits\SerializeAttribute;
use Yii;
use yii\web\UploadedFile;

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

    public function getSpamAttribute()
    {
        return 'controlUserNameExist';
    }

    /** for delete in futere */
    public function contact($email = null)
    {
        return $this->send($email);
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string $email the target email address
     * @return boolean whether the model passes validation
     */
    public function send($email = null, $cc = null)
    {
        preg_match('#(\w+?)(?:Form)?$#iu', $this::className(), $matches);
        $this->type = lcfirst($matches[1]);

        if (Yii::$app->request->isPost && $this->load(Yii::$app->request->post()) && $this->save()) {
            if (empty(Yii::$app->params['adminEmail']))
                Yii::$app->params['adminEmail'] = 'info@' . preg_replace('#.*//|.*www.#', '', Yii::$app->request->hostInfo);
            if (empty(Yii::$app->params['fromEmail']))
                Yii::$app->params['fromEmail'] = 'noreply@' . preg_replace('#.*//|.*www.#', '', Yii::$app->request->hostInfo);

            $mail = Yii::$app->mailer->compose();
            $mail->setTo(explode(',', $email ?: Yii::$app->params['adminEmail']));
            $mail->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name]);
            $mail->setSubject(($this->getIsSpam() ? '[SPAM] ' : '') . 'Сообщение с сайта ' . Yii::$app->name);
            $mail->setTextBody($this->body);

            if (!empty($this->email))
                $mail->setReplyTo($this->name ? [$this->email => $this->name] : $this->email);

            if ($cc && !$this->getIsSpam())
                $mail->setCc(explode(',', $cc));

            $this->beforeSend($mail);

            if ($mail->send() || YII_ENV_DEV) {
                $this->afterSend($mail);

                return true;
            }
        }
        return false;
    }

    public function getIsSpam()
    {
        return Yii::$app->request->post($this->spamAttribute);
    }

    public function beforeSend($mail)
    {

    }

    public function afterSend($mail)
    {
        Yii::$app->session->setFlash('success', 'Сообщение успешно отправлено');
    }

    public function uploadFiles($fileName, $dir)
    {
        $this->{$fileName} = UploadedFile::getInstances($this, $fileName);
        /** @var UploadedFile $file */
        foreach ($this->{$fileName} as $file) {
            $dir = Yii::getAlias($dir);
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            $path = $dir . $file->baseName . '.' . $file->extension;
            if (file_exists($path) && filesize($path) && $file->size && md5_file($path) != md5_file($file->tempName))
                $path = str_replace('/' . $file->baseName, '/' . uniqid($file->baseName . '-'), $path);
            if ($file->size) {
                $file->saveAs($path);
                $file->tempName = $path;
            } else {
                $file->tempName = false;
            }
        }
    }
}
