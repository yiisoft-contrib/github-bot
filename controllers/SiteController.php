<?php
/**
 * 
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\controllers;


use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class SiteController extends Controller
{
	public function actionError()
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;

		if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
			// action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
			$exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
		}

		if ($exception instanceof HttpException) {
			$code = $exception->statusCode;
			Yii::$app->response->statusCode = $code;
		} else {
			$code = $exception->getCode();
			Yii::$app->response->statusCode = 500;
		}
		if ($exception instanceof Exception) {
			$name = $exception->getName();
		} else {
			$name = $this->defaultName ?: Yii::t('yii', 'Error');
		}
		if ($code) {
			$name .= " (#$code)";
		}

		if ($exception instanceof UserException) {
			$message = $exception->getMessage();
		} else {
			$message = Yii::t('yii', 'An internal server error occurred.');
		}

		return [
			'error' => $name,
			'message' => $message,
		];
	}
}
