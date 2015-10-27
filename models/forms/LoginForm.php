<?php

namespace ra\admin\models\forms;

use ra\admin\helpers\RA;
use Yii;
use yii\base\Model;
use yii\web\User;

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
     * @var \ra\admin\models\User
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
            $this->addError("username", Yii::t("user", $this->getLoginLabel() . " not found"));
            Yii::$app->session->set('authorizeErrors', Yii::$app->session->get('authorizeErrors', 0) + 1);
        }
    }

    /**
     * Get user based on email and/or username
     *
     * @return \ra\admin\models\User|null
     */
    public function getUser()
    {
        // check if we need to get user
        if ($this->_user === false) {

            /** @var \ra\admin\models\User $userModel */
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

    public function getLoginLabel()
    {
        if (RA::config('user')['loginEmail'] && RA::config('user')['loginUsername']) {
            return "Email / Username";
        } else {
            return RA::config('user')['loginEmail'] ? "Email" : "Username";
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

            //@todo Доделать генератор токена для входа
            /** @var \ra\admin\models\UserKey $userKey */
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

        /** @var \ra\admin\models\User $user */

        // check if password is correct
        $user = $this->getUser();
        if (!$user->validatePassword($this->password)) {
            $this->addError("password", Yii::t("user", "Incorrect password"));
        }
    }

    // calculate attribute label for "username"

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            "username" => Yii::t("user", $this->getLoginLabel()),
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
            Yii::$app->user->on(User::EVENT_AFTER_LOGIN, function ($event) {
                $event->identity->login_ip = Yii::$app->getRequest()->getUserIP();
                $event->identity->login_time = date("Y-m-d H:i:s");
                $event->identity->update(false, ["login_ip", "login_time"]);
            });
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $loginDuration : 0);
        }

        return false;
    }
}