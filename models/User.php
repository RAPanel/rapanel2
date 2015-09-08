<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $status
 * @property string $email
 * @property string $new_email
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $api_key
 * @property string $login_ip
 * @property string $login_time
 * @property string $create_ip
 * @property string $created_at
 * @property string $updated_at
 * @property string $ban_time
 * @property string $ban_reason
 *
 * @property Page[] $pages
 * @property PageComments[] $pageComments
 * @property UserRole $role
 * @property UserAuth[] $userAuths
 * @property UserKey[] $userKeys
 * @property UserProfile[] $userProfiles
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'status', 'api_key'], 'required'],
            [['role_id', 'status'], 'integer'],
            [['login_time', 'created_at', 'updated_at', 'ban_time'], 'safe'],
            [['email', 'new_email', 'username', 'password', 'auth_key', 'api_key', 'login_ip', 'create_ip', 'ban_reason'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['username'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rere.model', 'ID'),
            'role_id' => Yii::t('rere.model', 'Role ID'),
            'status' => Yii::t('rere.model', 'Status'),
            'email' => Yii::t('rere.model', 'Email'),
            'new_email' => Yii::t('rere.model', 'New Email'),
            'username' => Yii::t('rere.model', 'Username'),
            'password' => Yii::t('rere.model', 'Password'),
            'auth_key' => Yii::t('rere.model', 'Auth Key'),
            'api_key' => Yii::t('rere.model', 'Api Key'),
            'login_ip' => Yii::t('rere.model', 'Login Ip'),
            'login_time' => Yii::t('rere.model', 'Login Time'),
            'create_ip' => Yii::t('rere.model', 'Create Ip'),
            'created_at' => Yii::t('rere.model', 'Created At'),
            'updated_at' => Yii::t('rere.model', 'Updated At'),
            'ban_time' => Yii::t('rere.model', 'Ban Time'),
            'ban_reason' => Yii::t('rere.model', 'Ban Reason'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Page::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageComments()
    {
        return $this->hasMany(PageComments::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(UserRole::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuths()
    {
        return $this->hasMany(UserAuth::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserKeys()
    {
        return $this->hasMany(UserKey::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['user_id' => 'id']);
    }




    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        // check if we're setting $this->password directly
        // handle it by setting $this->newPassword instead
        $dirtyAttributes = $this->getDirtyAttributes();
        if (isset($dirtyAttributes["password"])) {
            $this->newPassword = $dirtyAttributes["password"];
        }

        // hash new password if set
        if ($this->newPassword) {
            $this->password = Yii::$app->security->generatePasswordHash($this->newPassword);
        }

        // convert ban_time checkbox to date
        if ($this->ban_time) {
            $this->ban_time = date("Y-m-d H:i:s");
        }

        // ensure fields are null so they won't get set as empty string
        $nullAttributes = ["email", "username", "ban_time", "ban_reason"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        return parent::beforeSave($insert);
    }
}
