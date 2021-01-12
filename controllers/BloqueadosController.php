<?php

namespace app\controllers;

use Yii;
use app\models\Bloqueados;
use app\models\Usuarios;
use app\models\BloqueadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Seguidores;

require '../web/uploads3.php';

/**
 * BloqueadosController implements the CRUD actions for Bloqueados model.
 */
class BloqueadosController extends Controller
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
     * Lists all Bloqueados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BloqueadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bloqueados model.
     * @param integer $usuario
     * @param integer $bloqueado
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = Usuarios::find()->where(['id' => $id])->one();

        $bloquear1 = Bloqueados::find()->where([
            'bloqueado' => $id,
            'usuario' => Yii::$app->user->id
        ])->one();

        $all = Usuarios::find()->all();
        $sugeridos = [];
        for ($i = 0; $i < 3; $i++) {
            $random = rand(0, count($all)-1);
            if ($all[$random]->id == Yii::$app->user->id) {
                return;
            }
            for ($j = 0; $j < count($sugeridos); $j++) {
                if ($all[$random]->id == $sugeridos[$j]->id) {
                    return;
                }
            }
            array_push($sugeridos, $all[$random]);
        }

        return $this->render('view', [
            'model' => $model,
            'bloquear1' => $bloquear1,
            'sugeridos' => $sugeridos,
        ]);
    }

    public function actionBloquear($id)
    {
        $seguido = Seguidores::find()->where([
            'seguidor_id' => Yii::$app->user->id,
            'seguido_id' => $id
        ])->one();

        $seguidor = Seguidores::find()->where([
            'seguidor_id' => $id,
            'seguido_id' => Yii::$app->user->id
        ])->one();

        if ($seguido) {
            $seguido->delete();
        }

        if ($seguidor) {
            $seguidor->delete();
        }

        $bloquear = Bloqueados::find()->where([
            'bloqueado' => $id,
            'usuario' => Yii::$app->user->id
        ])->one();

        if ($bloquear) {
            $bloquear->delete();
        } else {
            $bloqueado = new Bloqueados([
                'bloqueado' => $id,
                'usuario' => Yii::$app->user->id
            ]);
            $bloqueado->save();
        }

        return $this->redirect(['usuarios/view', 'id' => $id]);
    }

    public static function bloqueado($id)
    {
        $bloquear = Bloqueados::find()->where([
            'bloqueado' => $id,
            'usuario' => Yii::$app->user->id
        ])->one();

        return isset($bloquear);
    }

    /**
     * Deletes an existing Bloqueados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $usuario
     * @param integer $bloqueado
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($usuario, $bloqueado)
    {
        $this->findModel($usuario, $bloqueado)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bloqueados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $usuario
     * @param integer $bloqueado
     * @return Bloqueados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($usuario, $bloqueado)
    {
        if (($model = Bloqueados::findOne(['usuario' => $usuario, 'bloqueado' => $bloqueado])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
