<?php

namespace app\controllers;

use Yii;
use app\models\Comentarios;
use app\models\ComentariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Usuarios;

/**
 * ComentariosController implements the CRUD actions for Comentarios model.
 */
class ComentariosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Comentarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComentariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $respuestas = Comentarios::find()->where(['respuesta' => '1'])->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'respuestas' => $respuestas
        ]);
    }

    /**
     * Displays a single Comentarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $publicacion = new Comentarios(['usuario_id' => Yii::$app->user->id]);
        $model = $this->findModel($id);
        $userComment = Usuarios::find()->where(['id' => $model->usuario_id])->one();

        if ($publicacion->load(Yii::$app->request->post()) && $publicacion->save()) {
            Yii::$app->session->setFlash('success', 'Se ha publicado tu comentario.');
            return $this->redirect(['comentarios/view', 'id' => $publicacion->id]);
        }

        return $this->render('view', [
            'model' => $model,
            'publicacion' => $publicacion,
            'userComment' => $userComment,
        ]);
    }

    /**
     * Creates a new Comentarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Comentarios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Comentarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
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
     * Finds the Comentarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
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
