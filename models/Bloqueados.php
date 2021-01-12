<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bloqueados".
 *
 * @property int $usuario
 * @property int $bloqueado
 *
 * @property Usuarios $usuario0
 * @property Usuarios $bloqueado0
 */
class Bloqueados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bloqueados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario', 'bloqueado'], 'required'],
            [['usuario', 'bloqueado'], 'default', 'value' => null],
            [['usuario', 'bloqueado'], 'integer'],
            [['usuario', 'bloqueado'], 'unique', 'targetAttribute' => ['usuario', 'bloqueado']],
            [['usuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario' => 'id']],
            [['bloqueado'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['bloqueado' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'usuario' => 'Usuario',
            'bloqueado' => 'Bloqueado',
        ];
    }

    /**
     * Gets query for [[Usuario0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario0()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario']);
    }

    /**
     * Gets query for [[Bloqueado0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloqueado0()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'bloqueado']);
    }
}
