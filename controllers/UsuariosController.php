<?php

namespace app\controllers;

use app\models\Comentarios;
use Yii;
use app\models\Usuarios;
use app\models\Seguidores;
use app\models\UsuariosSearch;
use app\models\Bloqueados;
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
                'only' => ['view', 'delete', 'index', 'update'],
                'rules' => [
                    [
                        'actions' => ['view', 'delete', 'index', 'update'],
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
     * Lista de todos los usuarios para admin.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->id != 1) {
            return $this->goBack();
        }

        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Vista de usuarios.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $bloquear1 = Bloqueados::find()->where([
            'bloqueado' => $id,
            'usuario' => Yii::$app->user->id
        ])->one();

        $bloquear2 = Bloqueados::find()->where([
            'bloqueado' => Yii::$app->user->id,
            'usuario' => $id,
        ])->one();

        if ($bloquear1 || $bloquear2) {
            return $this->redirect(['bloqueados/view', 'id' => $id]);
        }

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

        $queryF = Comentarios::find()->andWhere(['usuario_id' => $id])->andWhere(['<>', 'url_img', ''])->orderBy(['created_at' => SORT_DESC]);
        $countF = $queryF->count();
        $paginationF = new Pagination([
            'totalCount' => $countF,
            'pageSize' => 10
        ]);
        $cfotos = $queryF->offset($paginationF->offset)
        ->limit($paginationF->limit)
        ->all();

        $all = Usuarios::find()->all();
        $sugeridos = [];
        for ($i = 0; $i < 3; $i++) {
            $random = rand(0, count($all)-1);
            if ($all[$random]->id == Yii::$app->user->id) {
                return;
            }
            if ($all[$random]->id == $id) {
                return;
            }
            for ($j = 0; $j < count($sugeridos); $j++) {
                if ($all[$random]->id == $sugeridos[$j]->id) {
                    return;
                }
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
            'usuario' => $usuario,
            'seguido' => $id,
            'comentarios' => $comentarios,
            'publicacion' => $publicacion,
            'actual' => $actual,
            'ml' => $misLikes,
            'cfotos' => $cfotos,
            'sugeridos' => $sugeridos,
            'getRelacionados' => $getRelacionados,
            'paginationC' => $paginationC,
            'paginationL' => $paginationL,
            'paginationF' => $paginationF,
        ]);
    }

     /**
     * Vista de usuarios relacionados.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
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
     * Editar a un usuario que este requistrado y logueado.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if ($id != Yii::$app->user->id) {
            return $this->goBack();
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($_FILES['Usuarios']['name']['url_img'] == null) {
                $model->url_img = $model->getOldAttribute('url_img');
                $model->save();
                Yii::$app->session->setFlash('success', 'Se ha modificado tu perfil.');
                return $this->redirect(['usuarios/view', 'id' => $model['id']]);
            } else {
                uploadImagen($model);
                $model->url_img = $_FILES['Usuarios']['name']['url_img'];
                $model->save();
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
