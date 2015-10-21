<?php

namespace app\admin\models\forms;

use app\admin\helpers\RA;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    /**
     * @var string Username and/or email
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var bool If true, users will be logged in for $loginDuration
     */
    public $rememberMe = true;

    /**
     * @var \app\admin\models\User
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [["username", "password"], "required"],
            ["username", "validateUser"],
            ["username", "validateUserStatus"],
            ["password", "validatePassword"],
            ["rememberMe", "boolean"],
        ];
    }

    /**
     * Validate user
     */
    public function validateUser()
    {
        // check for valid user or if user registered using social auth
        $user = $this->getUser();
        if (!$user || !$user->password) {
            if (Yii::$app->getModule("user")->loginEmail && Yii::$app->getModule("user")->loginUsername) {
                $attribute = "Email / Username";
            } else {
                $attribute = Yii::$app->getModule("user")->loginEmail ? "Email" : "Username";
            }
            $this->addError("username", Yii::t("user", "$attribute not found"));

            // do we need to check $user->userAuths ???
        }
    }

    /**
     * Validate user status
     */
    public function validateUserStatus()
    {
        // check for ban status
        $user = $this->getUser();
        if ($user->ban_time) {
            $this->addError("username", Yii::t("user", "User is banned - {banReason}", [
                "banReason" => $user->ban_reason,
            ]));
        }

        // check status and resend email if inactive
        if ($user->status == $user::STATUS_INACTIVE) {

            /** @var \app\admin\models\UserKey $userKey */
            $userKey = Yii::$app->getModule("user")->model("UserKey");
            $userKey = $userKey::generate($user->id, $userKey::TYPE_EMAIL_ACTIVATE);
            $user->sendEmailConfirmation($userKey);
            $this->addError("username", Yii::t("user", "Confirmation email resent"));
        }
    }

    /**
     * Validate password
     */
    public function validatePassword()
    {
        // skip if there are already errors
        if ($this->hasErrors()) {
            return;
        }

        /** @var \app\admin\models\User $user */

        // check if password is correct
        $user = $this->getUser();
        if (!$user->validatePassword($this->password)) {
            $this->addError("password", Yii::t("user", "Incorrect password"));
        }
    }

    /**
     * Get user based on email and/or username
     *
     * @return \app\admin\models\User|null
     */
    public function getUser()
    {
        // check if we need to get user
        if ($this->_user === false) {

            /** @var \app\admin\models\User $userModel */
            // build query based on email and/or username login properties
            $userModel = RA::config('user')['models']['user'];
            $user = $userModel::find();
            if (RA::config('user')['loginEmail']) {
                $user->orWhere(["email" => $this->username]);
            }
            if (RA::config('user')['loginUsername']) {
                $user->orWhere(["username" => $this->username]);
            }

            // get and store user
            $this->_user = $user->one();
        }

        // return stored user
        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        // calculate attribute label for "username"
        if (RA::config('user')['loginEmail'] && RA::config('user')['loginUsername']) {
            $attribute = "Email / Username";
        } else {
            $attribute = RA::config('user')['loginEmail'] ? "Email" : "Username";
        }

        return [
            "username" => Yii::t("user", $attribute),
            "password" => Yii::t("user", "Password"),
            "rememberMe" => Yii::t("user", "Remember Me"),
        ];
    }

    /**
     * Validate and log user in
     *
     * @param int $loginDuration
     * @return bool
     */
    public function login($loginDuration)
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $loginDuration : 0);
        }

        return false;
    }
}