<?php

namespace app\controllers;

use app\models\Comentarios;
use Yii;
use app\models\Usuarios;
use app\models\Seguidores;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Likes;
use yii\filters\AccessControl;
use yii\data\Pagination;

require '../web/uploads3.php';

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
        $queryC = Comentarios::find()->where(['usuario_id' => $id])->orderBy(['created_at' => SORT_DESC]);
        $countC = $queryC->count();
        $paginationC = new Pagination([
            'totalCount' => $countC,
            'pageSize' => 10
        ]);
        $comentarios = $queryC->offset($paginationC->offset)
            ->limit($paginationC->limit)
            ->all();
        $queryL = Likes::find()->where(['usuario_id' => $id])->orderBy(['created_at' => SORT_DESC]);
        $countL = $queryL->count();
        $paginationL = new Pagination([
            'totalCount' => $countL,
            'pageSize' => 10
        ]);
        $misLikes = $queryL->offset($paginationL->offset)
        ->limit($paginationL->limit)
        ->all();
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

        if ($publicacion->load(Yii::$app->request->post())) {
            if (file_exists($_FILES['Comentarios']['name']['url_img'])) {
                uploadComentario($publicacion);
                $publicacion->url_img = $_FILES['Comentarios']['name']['url_img'];
            }

            if ($publicacion->save()) {
                Yii::$app->session->setFlash('success', 'Se ha publicado tu comentario.');
                return $this->redirect(['comentarios/view', 'id' => $publicacion['id']]);
            }
        }

        return $this->render('view', [
            'usuario' => $usuario,
            'seguido' => $id,
            'comentarios' => $comentarios,
            'publicacion' => $publicacion,
            'actual' => $actual,
            'ml' => $misLikes,
            'sugeridos' => $sugeridos,
            'getRelacionados' => $getRelacionados,
            'paginationC' => $paginationC,
            'paginationL' => $paginationL,
        ]);
    }

    public function actionRelacionados($id)
    {
        $usuario = Usuarios::findOne(['id' => $id]);
        $actual = Usuarios::findOne(['id' => Yii::$app->user->id]);

        $getSeguidores = $actual->getSeguidos()->select('seguido_id')->column();
        $getRelacionados = Seguidores::find()->where(['IN', 'seguidor_id', $getSeguidores])->andWhere(['seguido_id' => $usuario->id])->all();
        $getIds = [];

        for ($i = 0; $i < count($getRelacionados); $i++) {
            $idUser = $getRelacionados[$i]->seguidor_id;
            array_push($getIds, $idUser);
        }
        
        $getUsuarios = Usuarios::find()->where(['IN', 'id', $getIds])->all();

        return $this->render('relacionados', [
            'getUsuarios' => $getUsuarios,
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

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($_FILES)) {
                uploadImagen($model);
                $model->url_img = $_FILES['Usuarios']['name']['url_img'];
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Se ha modificado tu perfil.');
                return $this->redirect(['usuarios/view', 'id' => $model['id']]);
            }
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
