<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Usuarios;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\models\Comentarios;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $idActual = Yii::$app->user->id;
        $publicar = new Comentarios(['usuario_id' => $idActual]);
        $model = Usuarios::findOne(['id' => $idActual]);

        if ($publicar->load(Yii::$app->request->post()) && $publicar->save()) {
            Yii::$app->session->setFlash('success', 'Se ha publicado tu comentario.');
            return $this->redirect('index');
        }

        
        return $this->render('index', [
            'publicar' => $publicar,
            'model' => $model,
        ]);
    }

    public function actionRecuperarpass()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $usuario = new Usuarios;
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $loginForm = new LoginForm();
        $newUser = new Usuarios(['scenario' => Usuarios::SCENARIO_CREAR]);

        if (isset($_POST['LoginForm'])) {
            if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
                return $this->goBack();
            }
        } elseif (isset($_POST['Usuarios'])) {
            if ($newUser->load(Yii::$app->request->post()) && $newUser->save()) {
                $url = Url::to([
                    'site/activar',
                    'id' => $newUser->id,
                    'token' => $newUser->token,
                ], true);
    
                $body = <<<EOT
                    <h2>Pulsa el siguiente enlace para confirmar la cuenta de correo.<h2>
                    <a href="$url">Confirmar cuenta</a>
                EOT;
                $this->enviarMail($body, $newUser->email);
                Yii::$app->session->setFlash('success', 'Se ha creado el usuario correctamente.');
                return $this->redirect(['site/login']);
            }
        }
            return $this->render('login', [
                'login' => $loginForm,
                'usuario' => $newUser
            ]);
    }

    public function enviarMail($cuerpo, $dest)
    {
        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['smtpUsername'])
            ->setTo($dest)
            ->setSubject('Confirmar Cuenta')
            ->setHtmlBody($cuerpo)
            ->send();
    }

    public function actionActivar($id, $token)
    {
        $usuario = $this->findModel($id);
        if ($usuario->token === $token) {
            $usuario->token = null;
            $usuario->save();
            Yii::$app->session->setFlash('success', 'Usuario validado. Inicie sesión.');
            return $this->redirect(['site/login']);
        }
        Yii::$app->session->setFlash('error', 'La validación no es correcta.');
        return $this->redirect(['site/index']);
    }

    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página no existe.');
        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
