<?php

namespace ra\admin\models;

use ra\admin\helpers\RA;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

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
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @var int Inactive status
     */
    const STATUS_INACTIVE = 0;
    /**
     * @var int Active status
     */
    const STATUS_ACTIVE = 1;
    /**
     * @var int Unconfirmed email status
     */
    const STATUS_UNCONFIRMED_EMAIL = 2;
    /**
     * @var string Current password - for account page updates
     */
    public $currentPassword;
    /**
     * @var string New password - for registration and changing password
     */
    public $newPassword;
    /**
     * @var string New password confirmation - for reset
     */
    public $newPasswordConfirm;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(["api_key" => $token]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // set initial rules
        $rules = [
            // general email and username rules
            [['email', 'username'], 'string', 'max' => 255],
            [['email', 'username'], 'unique'],
            [['email', 'username'], 'filter', 'filter' => 'trim'],
            [['email'], 'email'],
            [['username'], 'match', 'pattern' => RA::config('user')['loginUsername'] ? '/^[A-Za-z0-9_]+$/u' : '/^[\w\d\s]+$/ui', 'message' => Yii::t('user', '{attribute} can contain only letters, numbers, and "_"')],

            // password rules
            [['newPassword'], 'string', 'min' => 3],
            [['newPassword'], 'filter', 'filter' => 'trim'],
            [['newPassword'], 'required', 'on' => ['register', 'reset']],
            [['newPasswordConfirm'], 'required', 'on' => ['reset']],
            [['newPasswordConfirm'], 'compare', 'compareAttribute' => 'newPassword', 'message' => Yii::t('user', 'Passwords do not match')],

            // account page
            [['currentPassword'], 'required', 'on' => ['account']],
            [['currentPassword'], 'validatePassword', 'on' => ['account']],

            // admin crud rules
            [['role_id', 'status'], 'required', 'on' => ['admin']],
            [['role_id', 'status'], 'integer', 'on' => ['admin']],
            [['ban_time'], 'integer', 'on' => ['admin']],
            [['ban_reason'], 'string', 'max' => 255, 'on' => 'admin'],
        ];

        // add required rules for email/username depending on module properties
        foreach (["requireEmail", "requireUsername"] as $requireField) {
            if (RA::config('user')[$requireField]) {
                $attribute = strtolower(substr($requireField, 7)); // "email" or "username"
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'role_id' => Yii::t('ra', 'Role ID'),
            'status' => Yii::t('ra', 'Status'),
            'email' => Yii::t('ra', 'Email'),
            'new_email' => Yii::t('ra', 'New Email'),
            'username' => Yii::t('ra', 'Username'),
            'password' => Yii::t('ra', 'Password'),
            'auth_key' => Yii::t('ra', 'Auth Key'),
            'api_key' => Yii::t('ra', 'Api Key'),
            'login_ip' => Yii::t('ra', 'Login Ip'),
            'login_time' => Yii::t('ra', 'Login Time'),
            'create_ip' => Yii::t('ra', 'Create Ip'),
            'created_at' => Yii::t('ra', 'Created At'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'ban_time' => Yii::t('ra', 'Ban Time'),
            'ban_reason' => Yii::t('ra', 'Ban Reason'),
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

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Verify password
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
            ],
        ];
    }
}
