<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comentarios".
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $text
 * @property string|null $created_at
 * @property int $respuesta
 *
 * @property Comentarios $respuesta0
 * @property Comentarios[] $comentarios
 * @property Usuarios $usuario
 */
class Comentarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comentarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'text'], 'required'],
            [['usuario_id'], 'default', 'value' => null],
            [['usuario_id'], 'integer'],
            [['respuesta'], 'integer'],
            [['citado'], 'integer'],
            [['created_at'], 'safe'],
            [['text'], 'string', 'max' => 280],
            [['respuesta'], 'exist', 'skipOnError' => true, 'targetClass' => Comentarios::className(), 'targetAttribute' => ['respuesta' => 'id']],
            [['citado'], 'exist', 'skipOnError' => true, 'targetClass' => Comentarios::className(), 'targetAttribute' => ['citado' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'text' => 'Text',
            'created_at' => 'Created At',
            'respuesta' => 'Respuesta',
            'citado' => 'Citado',
        ];
    }

    /**
     * Gets query for [[Respuesta0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRespuesta0()
    {
        return $this->hasOne(Comentarios::className(), ['id' => 'respuesta']);
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['respuesta' => 'id']);
    }

    public function getCitados()
    {
        return $this->hasMany(Comentarios::className(), ['citado' => 'id']);
    }

    public function like($comentario_id)
    {
        $like = Likes::find()->where([
            'comentario_id' => $comentario_id,
            'usuario_id' => Yii::$app->user->id
        ])->one();

        return isset($like);
    }

    public function fecha($fecha)
    {
        $actual = new  \DateTime('now');
        $cf = new \DateTime($fecha);

        $interval = $actual->diff($cf);

        if ($interval->format('%i') < 1) {
            return $interval->format('%ss');
        } elseif ($interval->format('%H') < 1) {
            return $interval->format('%im');
        } elseif ($interval->format('%d') < 1) {
            return $interval->format('%Hh');
        } else {
            return $cf->format('d-m-y');
        }
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id']);
    }
}
