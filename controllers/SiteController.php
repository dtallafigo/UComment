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
use yii\data\Pagination;
use app\models\Seguidores;

require '../web/uploads3.php';

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
                'only' => ['logout', 'index', 'login'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?','@'],
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
        $usuario = Usuarios::findOne(['id' => $idActual]);
        $publicacion = new Comentarios(['usuario_id' => $idActual]);
        $actual = Usuarios::findOne(['id' => Yii::$app->user->id]);
        $model = Usuarios::findOne(['id' => $idActual]);
        $ids = $model->getSeguidos()->select('seguido_id')->column();
        array_push($ids, $idActual);
        $query = Comentarios::find()->where(['IN', 'usuario_id', $ids])->orderBy(['created_at' => SORT_DESC]);
        $count = $query->count();

        $pagination = new Pagination([
            'totalCount' => $count,
            'pageSize' => 5
        ]);

        $comentarios = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        
        if ($publicacion->load(Yii::$app->request->post())) {
            if ($_FILES['Comentarios']['name']['url_img'] == null) {
                $publicacion->save();
                Yii::$app->session->setFlash('success', 'Se ha modificado tu perfil.');
                return $this->redirect(['comentarios/view', 'id' => $publicacion['id']]);
            } else {
                uploadComentario($publicacion);
                $publicacion->url_img = $_FILES['Comentarios']['name']['url_img'];
                $publicacion->save();
                Yii::$app->session->setFlash('success', 'Se ha modificado tu perfil.');
                return $this->redirect(['comentarios/view', 'id' => $publicacion['id']]);
            }
        }

        $all = Usuarios::find()->all();
        $sugeridos = [];
        for ($i = 0; $i < 3; $i++) {
            $random = rand(0, count($all)-1);
            if ($all[$random]->id == Yii::$app->user->id) {
                return;
            }
            array_push($sugeridos, $all[$random]);
        }

        $getSeguidores = $actual->getSeguidos()->select('seguido_id')->column();
        $getRelacionados = Seguidores::find()->where(['IN', 'seguidor_id', $getSeguidores])->andWhere(['seguido_id' => $usuario->id])->all();
        $getIds = [];

        for ($i = 0; $i < count($getRelacionados); $i++) {
            $idUser = $getRelacionados[$i]->seguidor_id;
            array_push($getIds, $idUser);
        }
        
        return $this->render('index', [
            'publicacion' => $publicacion,
            'model' => $model,
            'comentarios' => $comentarios,
            'actual' => $actual,
            'pagination' => $pagination,
            'sugeridos' => $sugeridos,
            'usuario' => $usuario,
        ]);
    }

    public function actionRecuperarpass()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $usuario = new Usuarios;
    }

    public function actionBusqueda()
    {
        $usuarios = Usuarios::find()->where('1=0');
        $comentarios = Comentarios::find()->where('1=0');
        $actual = Usuarios::findOne(['id' => Yii::$app->user->id]);
        $publicacion = new Comentarios(['usuario_id' => Yii::$app->user->id]);

        $countU = 0;
        $countC = 0;

        if ($publicacion->load(Yii::$app->request->post()) && $publicacion->save()) {
            Yii::$app->session->setFlash('success', 'Se ha publicado tu comentario.');
            return $this->redirect(['comentarios/view', 'id' => $publicacion->id]);
        }

        if (($cadena = Yii::$app->request->get('cadena', ''))) {
            $usuarios = Usuarios::find()->where(['ilike', 'log_us', $cadena])->all();
            $countU = Usuarios::find()->where(['ilike', 'log_us', $cadena])->count();
            $ids = [];
            for ($i = 0; $i < count($usuarios); $i++) {
                array_push($ids, $usuarios[$i]->id);
            }
            $usuariosComment = Comentarios::find()->where(['IN', 'usuario_id', $ids])->all();
            $comentarios = Comentarios::find()->where(['ilike', 'text', $cadena])->all();
            $comentarios = array_merge($comentarios, $usuariosComment);
            $this->arraySortBy($comentarios, 'created_at');
            $countC = Comentarios::find()->where(['ilike', 'text', $cadena])->count();
        }

        return $this->render('busqueda', [
            'usuarios' => $usuarios,
            'comentarios' => $comentarios,
            'publicar' => $publicacion,
            'actual' => $actual,
            'cadena' => $cadena,
            'countU' => $countU,
            'countC' => $countC,
        ]);
    }

    public function arraySortBy(&$arrIni, $col, $order = SORT_DESC)
    {
        $arrAux = [];
        foreach ($arrIni as $key => $row) {
            $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
            $arrAux[$key] = strtolower($arrAux[$key]);
        }
        array_multisort($arrAux, $order, $arrIni);
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
                    <div class='wrap'>
                        <div class='container'>
                            <div class='row com'>
                                <div class='col-12'>
                                    <h2>Bienvenido a <img src='icons/logodavo2.png'></h2>
                                    <a href="$url">Confirmar cuenta</a>
                                </div>
                            </div>
                        </div>
                    </div>
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
