<?php

/**
 * Class ROrderController
 *
 * @property ROrderModule $module
 */
class ROrderController extends CController {

	public function actionProcess($id) {
		$model = $this->getModel($id);
		$this->routeToStep($model->id, $model->status);
	}

	public function routeToStep($id, $status) {
		switch($status) {
			case ROrder::STATUS_DELIVERY:
				$this->redirect(array('order/delivery', 'id' => $id));
			case ROrder::STATUS_USER_DATA:
				$this->redirect(array('order/userData', 'id' => $id));
			case ROrder::STATUS_PAY:
				$this->redirect(array('order/payment', 'id' => $id));
			case ROrder::STATUS_DONE:
				$this->redirect(array('order/done', 'id' => $id));
			default:
				throw new CHttpException(404);
		}
	}

	public function actionDelivery($id) {
		$model = $this->getModel($id);
		if($model->status != ROrder::STATUS_DELIVERY)
			$this->routeToStep($id, $model->status);
		$model->scenario = 'delivery';
		$this->performAjaxValidation($model);
		if(isset($_POST[get_class($model)])) {
			$model->setAttributes($_POST[get_class($model)]);
			if($model->validate()) {
				$model->status = ROrder::STATUS_USER_DATA;
				$model->isNewRecord = false;
				$model->save();
				$this->refresh();
			}
		}
		$this->render('delivery', compact('model'));
	}

	public function actionUserData($id) {
		$model = $this->getModel($id);
		if($model->status != ROrder::STATUS_USER_DATA)
			$this->routeToStep($id, $model->status);
		$model->scenario = 'userData';
		$userModel = $model->getUserModel();
		$this->performAjaxValidation($model);
		if(isset($_POST[get_class($userModel)])) {
			$userModel->setAttributes($_POST[get_class($userModel)]);
			if($model->validate()) {
				$model->status = ROrder::STATUS_PAY;
				$model->isNewRecord = false;
				$model->save();
				$this->refresh();
			}
		}
		$this->render('userData', compact('model', 'userModel'));
	}

	public function actionPayment($id) {
		$model = $this->getModel($id);
		if($model->status != ROrder::STATUS_PAY)
			$this->routeToStep($id, $model->status);
		$model->scenario = 'payment';
		$this->performAjaxValidation($model);
		if(isset($_POST[get_class($model)])) {
			$model->setAttributes($_POST[get_class($model)]);

			if($model->validate()) {
				$model->isNewRecord = false;
				$model->save();
				$this->redirect($this->module->getPaymentUrl($model));

			}
			var_dump($model->errors);
		}
		$this->render('payment', compact('model'));
	}

	public function getModel($id) {
		$model = ROrder::model()->findByAttributes(array('id' => $id, 'user_id' => Yii::app()->user->id));
		if(!$model)
			throw new CHttpException(404);
		$model->setModule($this->module);
		return $model;
	}

	public function performAjaxValidation($models, $formId = null)
	{
		if (Yii::app()->request->isAjaxRequest) {
			if ($formId !== null && (!isset($_POST['ajax']) || $_POST['ajax'] != $formId)) Yii::app()->end();
			if (!is_array($models))
				$models = array($models);
			foreach ($models as $model) if (isset($_POST[get_class($model)]))
				$model->attributes = $_POST[get_class($model)];
			echo CActiveForm::validate($models);
			Yii::app()->end();
		}
	}
} 