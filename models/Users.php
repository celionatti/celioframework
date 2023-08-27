<?php

namespace App\models;

use App\Core\Config;
use App\Core\Cookie;
use App\Core\Application;
use App\models\UserSessions;
use App\Core\Database\DbModel;
use App\Core\Support\Helpers\Token;
use App\Core\Support\Helpers\Bcrypt;
use App\Core\Support\Helpers\UserInfo;
use App\Core\Validations\MinValidation;
use App\Core\Validations\EmailValidation;
use App\Core\Validations\UniqueValidation;
use App\Core\Validations\MatchesValidation;
use App\Core\Validations\RequiredValidation;


class Users extends DbModel
{
    const BLOCKED = 0;
    const UNBLOCKED = 1;
    const USER_ACCESS = 'user';
    const ADMIN_ACCESS = 'admin';
    const MANAGER_ACCESS = 'manager';
    const AUTHOR_ACCESS = 'author';

    public string $uid = "";
    public string $surname = "";
    public string|null $username = null;
    public string $name = "";
    public string $email = "";
    public string|null $avatar = null;
    public string|null $phone = null;
    public string $acl = self::USER_ACCESS;
    public string $password = "";
    public string $confirm_password = "";
    public string $old_password = "";
    public string|null $token = null;
    public string|null $bio = "";
    public string|null $social = "";
    public int $blocked = self::BLOCKED;
    public string $remember = '';
    public string $created_at = "";
    public string $updated_at = "";

    protected static $_current_user = false;

    public static function tableName(): string
    {
        return 'users';
    }

    public function beforeSave(): void
    {
        $this->timeStamps();

        $this->runValidation(new RequiredValidation($this, ['field' => 'surname', 'msg' => "Surname is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'name', 'msg' => "First Name is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'username', 'msg' => "Username is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'email', 'msg' => "Email is a required field."]));
        $this->runValidation(new EmailValidation($this, ['field' => 'email', 'msg' => 'You must provide a valid email.']));
        $this->runValidation(new UniqueValidation($this, ['field' => ['email', 'surname', 'username'], 'msg' => 'A user with that email address already exists.']));

        $this->runValidation(new RequiredValidation($this, ['field' => 'acl', 'msg' => "Access Level is a required field."]));

        $this->runValidation(new RequiredValidation($this, ['field' => 'phone', 'msg' => "Phone Number is a required field."]));
        // $this->runValidation(new RequiredValidation($this, ['field' => 'social', 'msg' => "Social Link is a required field."]));

        if ($this->isNew()) {
            $this->runValidation(new RequiredValidation($this, ['field' => 'password', 'msg' => "Password is a required field."]));
            $this->runValidation(new RequiredValidation($this, ['field' => 'confirm_password', 'msg' => "Confirm Password is a required field."]));
            $this->runValidation(new MatchesValidation($this, ['field' => 'confirm_password', 'rule' => $this->password, 'msg' => "Your passwords do not match."]));
            $this->runValidation(new MinValidation($this, ['field' => 'password', 'rule' => 8, 'msg' => "Password must be at least 8 characters."]));

            $this->uid = Token::generateOTP(60);
            $this->password = Bcrypt::hashPassword($this->password);
        }
    }

    public function validateLogin()
    {
        $this->runValidation(new RequiredValidation($this, ['field' => 'email', 'msg' => "Email is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'password', 'msg' => "Password is a required field."]));
    }

    public function validateChangePassword()
    {
        $this->runValidation(new RequiredValidation($this, ['field' => 'password', 'msg' => "Password is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'confirm_password', 'msg' => "Confirm Password is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'old_password', 'msg' => "Old Password is a required field."]));
        $this->runValidation(new MatchesValidation($this, ['field' => 'confirm_password', 'rule' => $this->password, 'msg' => "Your passwords do not match."]));
        $this->runValidation(new MinValidation($this, ['field' => 'password', 'rule' => 8, 'msg' => "Password must be at least 8 characters."]));

        $this->password = Bcrypt::hashPassword($this->password);
    }

    public function validateChangePasswordAuth()
    {
        $this->runValidation(new RequiredValidation($this, ['field' => 'password', 'msg' => "Password is a required field."]));
        $this->runValidation(new RequiredValidation($this, ['field' => 'confirm_password', 'msg' => "Confirm Password is a required field."]));
        $this->runValidation(new MatchesValidation($this, ['field' => 'confirm_password', 'rule' => $this->password, 'msg' => "Your passwords do not match."]));
        $this->runValidation(new MinValidation($this, ['field' => 'password', 'rule' => 8, 'msg' => "Password must be at least 8 characters."]));

        $this->password = Bcrypt::hashPassword($this->password);
    }

    public function login($remember = false)
    {
        session_regenerate_id();
        Application::$app->session->set(Config::get('session_login'), $this->uid);
        self::$_current_user = $this;
        if ($remember) {
            $now = time();
            $newHash = md5("{$this->id}_{$now}");
            $session = UserSessions::findByUserId($this->uid);
            if (!$session) {
                $session = new UserSessions();
            }
            $session->user_id = $this->uid;
            $session->hash = $newHash;
            $session->ip = UserInfo::get_ip();
            $session->os = UserInfo::get_os();
            $session->save();
            Cookie::set(Config::get('login_token'), $newHash, 60 * 60 * 24 * 30);
        }
    }

    public static function loginFromCookie()
    {
        $cookieName = Config::get('login_token');
        if (!Cookie::exists($cookieName))
            return false;
        $hash = Cookie::get($cookieName);
        $session = UserSessions::findByHash($hash);
        if (!$session)
            return false;
        $user = self::findFirst([
            'conditions' => "uid = :uid",
            'bind' => ['uid' => $session->user_id]
        ]);
        if ($user) {
            $user->login(true);
        }
    }

    public function logout()
    {
        Application::$app->session->remove(Config::get('session_login'));
        self::$_current_user = false;
        $session = UserSessions::findByUserId($this->uid);
        if ($session) {
            $session->delete();
        }
        Cookie::delete(Config::get('login_token'));
    }

    public static function getCurrentUser()
    {
        if (!self::$_current_user && Application::$app->session->exists(Config::get('session_login'))) {
            $user_id = Application::$app->session->get(Config::get('session_login'));
            self::$_current_user = self::findFirst([
                'conditions' => "uid = :uid",
                'bind' => ['uid' => $user_id]
            ]);
        }
        if (!self::$_current_user)
            self::loginFromCookie();
        if (self::$_current_user && self::$_current_user->blocked) {
            self::$_current_user->logout();
            Application::$app->session->setFlash("success", "You are currently blocked. Please talk to an admin to resolve this.");
        }
        return self::$_current_user;
    }

    public function hasPermission($acl)
    {
        if (is_array($acl)) {
            return in_array($this->acl, $acl);
        }
        return $this->acl == $acl;
    }

    public function displayName(): string
    {
        return trim($this->username);
    }

    public static function users_count()
    {
        return Users::findTotal();
    }

}