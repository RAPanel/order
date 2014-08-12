<?php

/**
 * Class ROrder
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property string $paymentType
 * @property int $deliveryType
 * @property string $userData
 * @property string $orderData
 * @property string $created
 * @property string $lastmod
 * @property ROrderModule $module
 * @property float $total
 * @property float $discount
 */
class ROrder extends CActiveRecord
{
	private $_module;
	private $_userDataArray;
	private $_userModel;

	const STATUS_DELIVERY = 0;
	const STATUS_USER_DATA = 1;
	const STATUS_PAY = 2;
	const STATUS_DONE = 3;

	const TABLE_NAME = 'order';

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function __construct($module, $scenario = 'insert') {
		$this->_module = $module;
		parent::__construct($scenario);
	}

	public function tableName() {
		return self::TABLE_NAME;
	}

	public function getModule() {
		return $this->_module;
	}

	public function setModule($value) {
		$this->_module = $value;
	}

	public function rules() {
		return array(
			array('userData', 'validateUserData', 'on' => 'userData'),
			array('paymentType', 'in', 'range' => array_keys($this->module->paymentTypes), 'on' => 'payment'),
			array('deliveryType', 'in', 'range' => array_keys($this->module->deliveryTypes), 'on' => 'delivery'),
		);
	}

	public function validateUserData() {
		return $this->getUserModel()->validate();
	}

	/**
	 * @return CModel
	 */
	public function getUserModel() {
		if(!isset($this->_userDataArray)) {
			$this->_userDataArray = @unserialize($this->userData);
			if(!is_array($this->_userDataArray))
				$this->_userDataArray = array();
		}
		if(!isset($this->_userModel)) {
			$className = $this->module->userModelClass;
			$this->_userModel = new $className;
			$this->_userModel->setAttributes($this->_userDataArray, false);
		}
		return $this->_userModel;
	}

	public function getTotalPrice() {
		$total = $this->total - $this->discount;
		if($total < 0)
			$total = 0;
		return $total;
	}

	public function beforeSave() {
		$this->_userDataArray = $this->getUserModel()->getAttributes();
		$this->userData = serialize($this->_userDataArray);
		return parent::beforeSave();
	}

} 