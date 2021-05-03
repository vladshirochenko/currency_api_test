<?php


namespace app\controllers;


use app\helpers\ApiHelper;
use app\models\CurrencyRate;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class CurrencyRateController extends Controller
{
    use ApiHelper;

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionSave()
    {
        $rate = new CurrencyRate();

        $rate->setAttributes(Yii::$app->request->post());

        if ($rate->save()) {
            return $this->success();
        } else {
            return $this->fail($rate->getErrorSummary(true));
        }
    }

    public function actionGet()
    {
        $date = Yii::$app->request->getBodyParam('at_date');
        $officeId = Yii::$app->request->getBodyParam('office_id');

        if (!$date || !$officeId) {
            return $this->fail('Wrong params');
        }

        $date = Yii::$app->formatter->asDate($date, 'php:Y-m-d H:i:s');

        $lastRates = CurrencyRate::find()
            ->select(['currency', 'MAX(begins_at) as max_begins_at', 'office_id'])
            ->andWhere(['>=', 'begins_at', $date])
            ->andWhere(['OR',
                ['office_id' => null],
                ['office_id' => $officeId]
            ])
            ->groupBy('currency');

        $rates = (new Query())
            ->select(['currency_rate.currency as currency', 'buy', 'sell'])
            ->from(['last_rates' => $lastRates])
            ->innerJoin('currency_rate', 'currency_rate.currency = last_rates.currency AND currency_rate.begins_at = last_rates.max_begins_at AND currency_rate.office_id = last_rates.office_id')
            ->all();

        return $this->success($rates);
    }
}