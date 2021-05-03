<?php

namespace app\helpers;

use Yii;

trait ApiHelper
{
    public function success(?array $data = null)
    {
        return [
            'status' => true,
            'data' => $data,
        ];
    }

    public function fail($errors)
    {
        Yii::$app->response->setStatusCode(400);

        return [
            'status' => false,
            'errors' => $errors,
        ];
    }
}