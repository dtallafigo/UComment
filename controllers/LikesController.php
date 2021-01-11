<?php

namespace app\controllers;

use Yii;
use app\models\Likes;
use app\models\LikesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\Usuarios;
use app\models\Comentarios;

require '../web/uploads3.php';

/**
 * LikesController implements the CRUD actions for Likes model.
 */
class LikesController extends Controller
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
     * Lists all Likes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LikesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLike($comentario_id)
    {
        $model = Likes::find()->andWhere([
            'comentario_id' => $comentario_id,
            'usuario_id' => Yii::$app->user->id,
        ])->one();

        if ($model) {
            $model->delete();
        } else {
            $like = new Likes([
                'comentario_id' => $comentario_id,
                'usuario_id' => Yii::$app->user->id,
            ]);
            $like->save();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return array_merge([$this->like($comentario_id)], [Likes::find()->where(['comentario_id' => $comentario_id])->count()]);
    }

    public function like($comentario_id)
    {
        $like = Likes::find()->where([
            'comentario_id' => $comentario_id,
            'usuario_id' => Yii::$app->user->id
        ])->one();

        return isset($like);
    }

    /**
     * Displays a single Likes model.
     * @param integer $usuario_id
     * @param integer $comentario_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($comentario_id)
    {
        $likes = Likes::findAll(['comentario_id' => $comentario_id]);
        $comentario = Comentarios::findOne(['id' => $comentario_id]);
        $ucl = Usuarios::findOne(['id' => $comentario->usuario_id]);

        return $this->render('likes', [
            'likes' => $likes,
            'ucl' => $ucl,
        ]);
    }

    /**
     * Finds the Likes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $usuario_id
     * @param integer $comentario_id
     * @return Likes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($usuario_id, $comentario_id)
    {
        if (($model = Likes::findOne(['usuario_id' => $usuario_id, 'comentario_id' => $comentario_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
