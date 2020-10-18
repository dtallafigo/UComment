<?php

namespace app\models;

use Yii;

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
class Usuarios extends \yii\db\ActiveRecord
{
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
            [['url_img'], 'string', 'max' => 2048],
            [['bio'], 'string', 'max' => 280],
            [['ubi'], 'string', 'max' => 50],
            [['email'], 'unique'],
            [['log_us'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'log_us' => 'Log Us',
            'email' => 'Email',
            'password' => 'Password',
            'rol' => 'Rol',
            'auth_key' => 'Auth Key',
            'url_img' => 'Url Img',
            'bio' => 'Bio',
            'ubi' => 'Ubi',
        ];
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
