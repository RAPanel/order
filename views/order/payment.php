<table class="order-info">
	<tr>
		<td>Order total: </td>
		<td><?=$model->total ?></td>
	</tr>
	<tr>
		<td>Order discount: </td>
		<td><?=$model->discount ?></td>
	</tr>
	<tr>
		<td>Total: </td>
		<td><?=$model->getTotalPrice(); ?></td>
	</tr>
</table>

<?php
/** @var CActiveForm $form */
/** @var ROrder $model */
$form = $this->beginWidget('CActiveForm', array(
	'id' => 'order-form',
));

echo $form->dropDownList($model, 'paymentType', $this->module->getPaymentTypesList());

echo CHtml::submitButton('Pay');

$this->endWidget();