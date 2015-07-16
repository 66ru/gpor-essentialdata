<?php

class SiteController extends Controller
{
    public $layout='column1';

    public function actionService()
    {
        $service = Yii::app()->request->getQuery('service');
        if (!$service)
            throw new CHttpException(404, 'Страница не найдена');

        $serviceClass = Yii::app()->essentialData->getServiceClass($service);
        if (!$serviceClass)
            throw new CHttpException(404, 'Страница не найдена');

        $drivers = $serviceClass->getDrivers();
        $res = array('feeds' => array() );
        foreach ($drivers as $k => $driver) {
            $res['feeds'][] = array (
                'name' => $k,
                'title' => $driver->title,
                'url' => 'http://' . Yii::app()->params['domain'] .  CHtml::normalizeUrl(array('/site/feed/', 'service' => $service, 'driver' => $k)),
            );
        }
        header("Content-Type: application/json; charset: UTF-8");
        echo CJSON::encode($res);
        Yii::app()->end();
    }

    public function actionFeed()
    {
        $service = Yii::app()->request->getQuery('service');
        $driver  = Yii::app()->request->getQuery('driver');
        if (!$service || !$driver)
            throw new CHttpException(404, 'Страница не найдена');

        $serviceClass = Yii::app()->essentialData->getServiceClass($service);
        if (!$serviceClass)
            throw new CHttpException(404, 'Страница не найдена');

        $res = $serviceClass->readDriverData($driver);
        if ($res === null)
            throw new CHttpException(404, 'Страница не найдена');

        header("Content-Type: application/json; charset: UTF-8");
        echo CJSON::encode($res);
        Yii::app()->end();
    }


    public function actionIndex()
    {
        $services = Yii::app()->essentialData->getServices();
        foreach ($services as $k => $service) {
            $res[] = array (
                'name' => $k,
                'title' => $service->title,
                'url' => 'http://' . Yii::app()->params['domain'] .  CHtml::normalizeUrl(array('/site/service/', 'service' => $k)),
            );
        }
        header("Content-Type: application/json; charset: UTF-8");
        echo CJSON::encode($res);
        Yii::app()->end();
    }

}
