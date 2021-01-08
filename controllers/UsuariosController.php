<?php

namespace app\controllers;

use app\models\Comentarios;
use Yii;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Likes;
use yii\filters\AccessControl;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view', 'delete'],
                'rules' => [
                    [
                        'actions' => ['view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $usuario = Usuarios::findOne(['id' => $id]);
        $actual = Usuarios::findOne(['id' => Yii::$app->user->id]);
        $publicacion = new Comentarios(['usuario_id' => Yii::$app->user->id]);
        $comentarios = Comentarios::find()->where(['usuario_id' => $id])->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        $misLikes = Likes::find()->where(['usuario_id' => $id])->orderBy(['created_at' => SORT_DESC])->limit(20)->all();
        $all = Usuarios::find()->all();
        $sugeridos = [];
        for ($i = 0; $i < 3; $i++) {
            $random = rand(0, count($all)-1);
            if ($all[$random]->id == Yii::$app->user->id) {
                return;
            }
            array_push($sugeridos, $all[$random]);
        }

        if ($publicacion->load(Yii::$app->request->post()) && $publicacion->save()) {
            Yii::$app->session->setFlash('success', 'Se ha publicado tu comentario.');
            return $this->redirect(['comentarios/view', 'id' => $publicacion->id]);
        }

        return $this->render('view', [
            'usuario' => $usuario,
            'seguido' => $id,
            'comentarios' => $comentarios,
            'publicacion' => $publicacion,
            'actual' => $actual,
            'ml' => $misLikes,
            'sugeridos' => $sugeridos,
        ]);
    }


    /**
     * Updates an existing Usuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
