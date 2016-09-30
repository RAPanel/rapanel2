<?php

namespace ra\admin\models;

use ra\admin\traits\SerializeAttribute;
use Yii;
use yii\mail\MessageInterface;
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

    public $sendToUser = true;

    public $serializeAttributes = ['name', 'phone', 'email', 'text'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form}}';
    }

    public function init()
    {
        $this->serializeAttributes[] = 'ip';
        $this->serializeAttributes[] = 'fromUrl';
        array_unique($this->serializeAttributes);
        parent::init();
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
            [$this->serializeAttributes, 'safe'],
            [['type'], 'string', 'max' => 32],
            ['fromUrl', 'default', 'value' => $this->defaultFromUrl()],
            ['ip', 'default', 'value' => Yii::$app->request->userIP],
        ];
    }

    public function defaultFromUrl()
    {
        $fromUrl = Yii::$app->request->hostInfo . Yii::$app->request->url;
        if (Yii::$app->request->referrer &&
            strpos(Yii::$app->request->referrer, Yii::$app->request->hostInfo) !== false &&
            Yii::$app->request->isPost
        ) $fromUrl = Yii::$app->request->referrer;
        return $fromUrl;
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

    /** for delete in future */
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
                if ($this->sendToUser && !$this->getIsSpam() && !empty($this->email)) Yii::$app->mailer->compose()
                    ->setTo($this->email)
                    ->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name])
                    ->setSubject('Ваше сообщение получено')
                    ->setTextBody("Спасибо за обращение.\n\nМы ответим Вам как можно скорее.")
                    ->send();

                if ($this->getIsSpam() && !empty($this->email))
                    Yii::$app->mailer->compose()
                        ->setTo($this->email)
                        ->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name])
                        ->setSubject('Ваше сообщение отмечено как SPAM')
                        ->setTextBody("Внимание, нам не удалось получить Ваше сообщение.\n\nСвяжитесь с нами любым другим способом.")
                        ->send();

                return true;
            }
        }
        return false;
    }

    public function getIsSpam()
    {
        return (bool)Yii::$app->request->post($this->getSpamAttribute());
    }

    public function getSpamAttribute()
    {
        return 'controlUserNameExist';
    }

    /**
     * @param $mail MessageInterface
     */
    public function beforeSend($mail)
    {

    }

    /**
     * @param $mail MessageInterface
     */
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

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
            ],
        ];
    }
}
