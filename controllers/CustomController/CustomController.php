<?php
namespace app\controllers\CustomController;
use yii;

use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\Response;
use app\models\components\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\VerbFilter;

class CustomController extends ActiveController{
	public function init()
	{
		parent::init(); // TODO: Change the autogenerated stub

		Yii::$app->user->enableSession = false;
		yii::$app->request->parsers    = [
			'application/json' => 'yii\web\JsonParser',
		];
	}
}