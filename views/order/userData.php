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
/** @var CModel $userModel */
$form = $this->beginWidget('CActiveForm', array(
	'id' => 'order-form',
));

foreach($userModel->safeAttributeNames as $name) {
	echo CHtml::openTag('div');
	echo $form->label($userModel, $name);
	echo $form->textField($userModel, $name);
	echo $form->error($userModel, $name);
	echo CHtml::closeTag('div');
}

echo CHtml::submitButton('Process to payment');

$this->endWidget();