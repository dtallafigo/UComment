<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mensajes".
 *
 * @property int $id
 * @property int $id_sender
 * @property string|null $cuerpo
 * @property bool|null $leido
 * @property string $created_at
 * @property int $id_conversacion
 *
 * @property Conversaciones $conversacion
 * @property Usuarios $sender
 */
class Mensajes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mensajes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sender', 'id_conversacion'], 'required'],
            [['id_sender', 'id_conversacion'], 'default', 'value' => null],
            [['id_sender', 'id_conversacion'], 'integer'],
            [['cuerpo'], 'string'],
            [['leido'], 'boolean'],
            [['created_at'], 'safe'],
            [['id_conversacion'], 'exist', 'skipOnError' => true, 'targetClass' => Conversaciones::className(), 'targetAttribute' => ['id_conversacion' => 'id']],
            [['id_sender'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['id_sender' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sender' => 'Id Sender',
            'cuerpo' => 'Cuerpo',
            'leido' => 'Leido',
            'created_at' => 'Created At',
            'id_conversacion' => 'Id Conversacion',
        ];
    }

    /**
     * Gets query for [[Conversacion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConversacion()
    {
        return $this->hasOne(Conversaciones::className(), ['id' => 'id_conversacion'])->inverseOf('mensajes');
    }

    /**
     * Gets query for [[Sender]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'id_sender'])->inverseOf('mensajes');
    }
}
