<?php

namespace app\controllers;

use Yii;
use app\models\Comentarios;
use app\models\ComentariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Usuarios;
use yii\filters\AccessControl;

require '../web/uploads3.php';

/**
 * Acciones de Comentarios Controller.
 */
class ComentariosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'index'],
                'rules' => [
                    [
                        'actions' => ['delete', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ]
            ],
        ];
    }

    /**
     * Lista todos los comentarios para admin.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->id != 1) {
            return $this->goBack();
        }

        $searchModel = new ComentariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRespuestas($id)
    {
        $publicacion = new Comentarios(['usuario_id' => Yii::$app->user->id]);
        $actual = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        $comentarios = Comentarios::find()->where(['respuesta' => $id])->all();

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

        return $this->render('respuestas', [
            'comentarios' => $comentarios,
            'actual' => $actual,
            'publicacion' => $publicacion,
        ]);
    }

    public function actionCitados($id)
    {
        $publicacion = new Comentarios(['usuario_id' => Yii::$app->user->id]);
        $actual = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        $comentarios = Comentarios::find()->where(['citado' => $id])->all();

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

        return $this->render('citados', [
            'comentarios' => $comentarios,
            'actual' => $actual,
            'publicacion' => $publicacion,
        ]);
    }

    /**
     * Vista de comentarios.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $publicacion = new Comentarios(['usuario_id' => Yii::$app->user->id]);
        $model = $this->findModel($id);
        $userComment = Usuarios::find()->where(['id' => $model->usuario_id])->one();

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

        return $this->render('view', [
            'model' => $model,
            'publicacion' => $publicacion,
            'userComment' => $userComment,
        ]);
    }

    /**
     * Elimina un comentario.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $comentario = $this->findModel($id);

        if ($comentario->usuario_id == Yii::$app->user->id || $comentario->usuario_id == '1') {
            $comentario->delete();
            Yii::$app->session->setFlash('success', 'Se ha eliminado tu comentario.');
            return $this->goBack();
        } else {
            Yii::$app->session->setFlash('error', 'Debe ser el propietario del comentario para eliminarlo.');
            return $this->goBack();
        }

        return $this->goBack();
    }

    /**
     * Encontrar comentarios.
     * @param integer $id
     * @return Comentarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comentarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
