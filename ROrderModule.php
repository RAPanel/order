<?php

YiiBase::setPathOfAlias('order', dirname(__FILE__));

YiiBase::import('order.controllers.ROrderController');
YiiBase::import('order.models.ROrder');

class ROrderModule extends CWebModule {

	public $controllerMap = array(
		'order' => array(
			'class' => 'ROrderController',
		),
	);

	public $defaultController = 'order';

	public $userModelClass = 'OrderUser';

	public $deliveryTypes = array(
		'Самовывоз',
		'Почта России',
		'Почта EMS',
		'FeedEx',
		'DHL',
	);

	public $paymentTypes = array(
		'robokassa' => 'Робокасса',
	);

	/**
	 * @param float $totalPrice Цена без скидки
	 * @param float $discount Скидка
	 * @param string $orderData Скидка
	 */
	public function beginProcessing($totalPrice, $discount, $orderData) {
		$order = new ROrder($this);
		$order->total = $totalPrice;
		$order->discount = $discount;
		$order->user_id = Yii::app()->user->id;
		$order->status = ROrder::STATUS_DELIVERY;
		$order->orderData = $orderData;
		if($order->save(false)) {
			Yii::app()->controller->redirect(array('/' . $this->id . '/order/process', 'id' => $order->id));
		}
	}

	public function getPaymentTypesList() {
		$types = array();
		foreach($this->paymentTypes as $name => $type)
			if(is_array($type))
				$types[$name] = $type['name'];
			else
				$types[$name] = $type;
		return $types;
	}

	public function getPaymentUrl($model) {
		return array('/pay/pay/index', 'id' => $model->id, 'type' => $model->paymentType);
	}
}