<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency_rate".
 *
 * @property int $id
 * @property string $currency
 * @property float $buy
 * @property float $sell
 * @property string|null $office_id
 * @property string $begins_at
 * @property string $created_at
 */
class CurrencyRate extends \yii\db\ActiveRecord
{
    const CURRENCIES = ['EUR', 'USD'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency', 'buy', 'sell', 'begins_at'], 'required'],
            ['currency', 'in', 'range' => self::CURRENCIES],
            [['buy', 'sell'], 'number', 'min' => 0],
            [['begins_at'], 'datetime', 'format' => 'php:d.m.Y H:i:s'],
            [['created_at'], 'safe'],
            [['currency'], 'string', 'max' => 8],
            [['office_id'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currency' => 'Currency',
            'buy' => 'Buy',
            'sell' => 'Sell',
            'office_id' => 'Office ID',
            'begins_at' => 'Begins At',
            'created_at' => 'Created At',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->begins_at) {
            $this->begins_at = \Yii::$app->formatter->asDate($this->begins_at, 'php:Y-m-d H:i:s');
        }

        $existedRateQuery = self::find()->andWhere(['currency' => $this->currency, 'begins_at' => $this->begins_at]);

        if ($this->office_id) {
            $existedRateQuery->andWhere(['OR',
                ['office_id' => null],
                ['office_id' => $this->office_id]
            ]);
        }

        $existedRate = $existedRateQuery->one();

        if ($existedRate) {
            $this->addError('currency', 'currency rate for this date, currency and office already exists');
            return false;
        }

        return true;
    }
}
