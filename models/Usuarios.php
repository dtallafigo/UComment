<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $log_us
 * @property string $email
 * @property string $password
 * @property string|null $rol
 * @property string|null $auth_key
 * @property string|null $url_img
 * @property string|null $bio
 * @property string|null $ubi
 *
 * @property Comentarios[] $comentarios
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREAR = 'crear';

    public $password_repeat;
    public $verification_code;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['log_us', 'email', 'password'], 'required'],
            [['log_us'], 'string', 'max' => 60],
            [['email', 'password', 'rol', 'auth_key'], 'string', 'max' => 255],
            [['password_repeat'], 'required', 'on' => self::SCENARIO_CREAR],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
            [['url_img'], 'string', 'max' => 2048],
            [['bio'], 'string', 'max' => 280],
            [['ubi'], 'string', 'max' => 50],
            [['email'], 'unique'],
            [['log_us'], 'unique'],
            [['password'], 'compare', 'on' => self::SCENARIO_CREAR],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'log_us' => 'Usuario',
            'email' => 'Email',
            'password' => 'Contraseña',
            'password_repeat' => 'Repetir contraseña',
            'rol' => 'Rol',
            'auth_key' => 'Auth Key',
            'url_img' => 'Url Img',
            'bio' => 'Bio',
            'ubi' => 'Ubi',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public static function findByUsername($log_us)
    {
        return static::findOne(['log_us' => $log_us]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            if ($this->scenario === self::SCENARIO_CREAR) {
                $security = Yii::$app->security;
                $this->auth_key = $security->generateRandomString();
                $this->token = $security->generateRandomString(32);
                $this->password = $security->generatePasswordHash($this->password);
            }
        }

        return true;
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['usuario_id' => 'id']);
    }
}
