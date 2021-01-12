<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "conversaciones".
 *
 * @property int $id
 * @property int $id_user1
 * @property int $id_user2
 *
 * @property Usuarios $user1
 * @property Usuarios $user2
 * @property Mensajes[] $mensajes
 */
class Conversaciones extends \yii\db\ActiveRecord
{
    public $username;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conversaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['id_user1', 'id_user2'], 'default', 'value' => null],
            [['id_user1', 'id_user2'], 'integer'],
            [['id_user1'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['id_user1' => 'id']],
            [['id_user2'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['id_user2' => 'id']],
            [
                ['username'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Usuarios::className(),
                'targetAttribute' => ['username' => 'log_us'],
                'message' =>  'El nombre de usuario no existe, asegurate de que lo has escrito bien',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user1' => 'Id User1',
            'id_user2' => 'Id User2',
            'username' => 'Nombre de Usuario',
        ];
    }

    /**
     * Se ejecuta antes del save().
     * @param  bool $insert Indica si se va a hacer un insert o un update
     * @return bool         True si es insert, false si es update
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->id_user1 = Yii::$app->user->id;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Borra las conversaciones del usuario sin mensajes.
     * @return void
     */
    public static function vacias()
    {
        $ids = Mensajes::find()->select('id_conversacion')->column();
        $conversaciones = self::find()->where(['not in', 'id', $ids])->all();
        foreach ($conversaciones as $conversacion) {
            $conversacion->delete();
        }
    }

    /**
     * Obtiene el último mensaje de la conversación.
     * @return ActiveRecord Último mensaje de la conversación.
     */
    public function getLast()
    {
        return Mensajes::find()->where(['id_conversacion' => $this->id])->orderBy('created_at DESC')->one();
    }

    /**
     * Busca quien es la persona con la que conversa el usuario actual.
     * @return ActiveRecord Usuario participante en la conversación
     */
    public function getReceiver()
    {
        return Usuarios::findOne(['id' => $this->id_user1 == Yii::$app->user->id ? $this->id_user2 : $this->id_user1]);
    }

    /**
     * Gets query for [[User1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser1()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'id_user1'])->inverseOf('conversaciones');
    }

    /**
     * Gets query for [[User2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser2()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'id_user2'])->inverseOf('conversaciones0');
    }

    /**
     * Gets query for [[Mensajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMensajes()
    {
        return $this->hasMany(Mensajes::className(), ['id_conversacion' => 'id'])->inverseOf('conversacion');
    }
}
