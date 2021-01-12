<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Usuarios;
use yii\helpers\Url;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email', 'exist',
                'targetClass' => 'app\models\Usuarios',
                'message' => 'There is no Usuarios with this email address.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $usuario Usuarios */
        $usuario = Usuarios::findOne([
            'email' => $this->email,
        ]);

        if (!$usuario) {
            return false;
        }

        if (!Usuarios::isPasswordResetTokenValid($usuario->token)) {
            $usuario->generatePasswordResetToken();
            if (!$usuario->save()) {
                return false;
            }
        }
        $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $usuario->token]);
        $body = <<<EOT
        <h2>Pulsa el siguiente enlace para cambiar la contraseña.<h2>
        <a href="$resetLink">Cambiar contraseña</a>
        EOT;

        return Yii::$app
            ->mailer
            ->compose()
            ->setFrom(Yii::$app->params['smtpUsername'])
            ->setTo($this->email)
            ->setSubject('Cambiar contraseña')
            ->setHtmlBody($body)
            ->send();
    }
}
