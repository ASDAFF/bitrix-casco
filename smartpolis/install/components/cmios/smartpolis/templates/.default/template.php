<?php
/**
 * Компонент Умный Полис
 *
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

IncludeModuleLangFile(__FILE__);

if ($arResult['COMPLETE'] === 'Y') {
	?>
	<h3>Спасибо за заказ!</h3>
	<p>На указанный e-mail было отправлено письмо</p>
	<?
} else {
?>
	<div class="smartpolis_before_info">
		<?=$arResult['TEXT_BEFORE_BUTTON']?>
	</div>
	<div class="blok">
		<form id="smartpolis_car_form">
			<input type="hidden" name="smartpolis_show_type" value="<?=$arResult['SHOW_TYPE']?>" />
			<input type="hidden" name="type" value="getRequarList" />
			<input type="hidden" name="ajax" value="Y" />
			<input type="hidden" name="ajax_url" id="ajax_url" value="<?=$APPLICATION->GetCurPage()?>" />
			<table class="table1" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><div class="bl"><label class="smartpolis_car_form_label"><?=GetMessage('SPM_FORM_TITLE_CAR_BRAND')?></label><select class="" name="smartpolis_car_marks" id="smartpolis_car_marks"><option></option>
						<?foreach ($arResult['CAR_BRANDS'] as $k => $v) {?>
							<option value="<?=$k?>"><?=$v?></option>
						<?}?>
					</select></div></td>
					<td><div class="bl"><label class="smartpolis_car_form_label"><?=GetMessage('SPM_FORM_TITLE_CAR_MODEL')?></label><select class="" name="smartpolis_car_models" disabled="disabled" id="smartpolis_car_models"><option></option></select></div></td>
					<td><div class="bl"><label class="smartpolis_car_form_label"><?=GetMessage('SPM_FORM_TITLE_CAR_MODIFICATION')?></label><select class="" name="smartpolis_car_modifications" disabled="disabled" id="smartpolis_car_modifications"><option></option></select></div></td>
				</tr>
				<tr>
					<td><div class="bl"><label class="smartpolis_car_form_label"><?=GetMessage('SPM_FORM_TITLE_CAR_PRICE')?></label><input type="text" class="pole"  id="smartpolis_car_cost" name="smartpolis_car_cost" value="0" /></div></td>
					<td><div class="bl"><label class="smartpolis_car_form_label"><?=GetMessage('SPM_FORM_TITLE_CAR_YEAR')?></label><select class=""  name="smartpolis_car_manufacturing_year" id="smartpolis_car_manufacturing_year">
						<?foreach ($arResult['YEARS'] as $k => $v) {?>
							<option value="<?=$k?>"><?=$v?> г.в.</option>
						<?}?>
					</select></div></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><div class="bl w100"><label class="smartpolis_car_form_label"><?=GetMessage('SPM_FORM_TITLE_DRIVER_QTY')?></label><select class="w100" name="smartpolis_drivers_count" id="smartpolis_drivers_count">
						<?foreach ($arResult['DRIVERS'] as $k => $v) {?>
							<option value="<?=$k?>"><?=$v?></option>
						<?}?>
					</select></div></td>
					<td colspan="2" id="smartpolis_drivers_set">
						<div class="row">
							<div class="bl">
								<label class="smartpolis_car_form_label">Возраст</label>
								<select class="smartpolis_car_form_age" name="car_driver_age[]">
									<?for ($i = 18; $i < 60; ++$i) {?>
										<option value="<?=$i?>"><?=$i?> лет</option>
									<?}?>
								</select>
							</div>
							<div class="bl">
								<label class="smartpolis_car_form_label">Стаж</label>
								<select class="smartpolis_car_form_experience" name="car_driver_prof[]">
									<option value="0">нет</option>
									<option value="1">1 год</option>
									<option value="2">2 года</option>
									<option value="3">3 года</option>
									<option value="4">4 года</option>
									<option value="5">5 лет</option>
									<option value="5">более 5 лет</option>
								</select>
							</div>
							<div class="bl">
								<label class="smartpolis_car_form_label">Пол</label>
								<select class="smartpolis_car_form_gender" name="car_driver_gender[]">
									<option value="M">Мужской</option>
									<option value="F">Женский</option>
								</select>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<?if (in_array($arResult['SHOW_TYPE'], array('show_after_form', 'send_by_letter'))) {?>
				<div class="b-gray" id="smartpolis_contact_form">
					<div class="left">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td colspan="2"><label><?=GetMessage('SPM_FORM_TITLE_YOUR_NAME')?></label><input name="" type="text" class="pole" id="smartpolis_client_name" /></td>
							</tr>
							<tr>
								<td><label><?=GetMessage('SPM_FORM_TITLE_YOUR_EMAIL')?></label><input name="" type="text" class="pole" id="smartpolis_client_email"/></td>
								<td><label><?=GetMessage('SPM_FORM_TITLE_YOUR_PHONE')?></label><input name="" type="text" class="pole" id="smartpolis_client_phone" /></td>
							</tr>
						</table>
					</div>
					<div class="right">
						<?=$arResult['HEADER_BEFORE_FORM']?>
					</div>
				</div>
			<?}?>
			<div class="b-rasch">
				<input class="but" name="" type="submit" value=" " />
			</div>
			<br />
		</form>
		<div id="smartpolis_message_before_form">
			<?=$arResult['MESSAGE_BEFORE_FORM']?>
			<br/>
			<span id="smartpolis_wait_count_result"></span>
		</div>
		<div class="table-tarif" id="smartpolis_result">
		</div>
	</div>
	<div id="smartpolis_order_form">
		<form method="post" action="<?=$APPLICATION->GetCurPage()?>">
			<input type="hidden" name="CAR_BRAND" value=""/>
			<input type="hidden" name="CAR_MODEL" value=""/>
			<input type="hidden" name="CAR_MODIFICATION" value=""/>
			<input type="hidden" name="CAR_PRICE" value=""/>
			<input type="hidden" name="CAR_YEAR" value=""/>
			<input type="hidden" name="DRIVER_QTY" value=""/>
			<input type="hidden" name="YOUR_NAME" value=""/>
			<input type="hidden" name="YOUR_EMAIL" value=""/>
			<input type="hidden" name="CONTACT_PHONE" value=""/>
			<input type="hidden" name="YEARS" value=""/>
			<input type="hidden" name="EXPERIENCE" value=""/>
			<input type="hidden" name="GENDER" value=""/>

			<h3 class="smartpolis_order_form_title"><?=GetMessage('SPM_FORM_TITLE_ORDER')?></h3>
			<table>
				<?if (!in_array($arResult['SHOW_TYPE'], array('show_after_form', 'send_by_letter'))) {?>
					<tr>
						<td><?=GetMessage('SPM_FORM_TITLE_YOUR_NAME')?><br/><input type="text" id="smartpolis_client_name" /></td>
					</tr>
					<tr>
						<td><?=GetMessage('SPM_FORM_TITLE_YOUR_EMAIL')?><br/><input type="text" id="smartpolis_client_email" /></td>
					</tr>
					<tr>
						<td><?=GetMessage('SPM_FORM_TITLE_YOUR_PHONE')?><br/><input type="text" id="smartpolis_client_phone" /></td>
					</tr>
				<?}?>
				<tr>
					<td><?=GetMessage('SPM_FORM_TITLE_DATE')?><br/><input type="text" name="DATE" /></td>
				</tr>
				<tr>
					<td><?=GetMessage('SPM_FORM_TITLE_CONVENIENCE')?><br/>
						<input type="radio" name="DELIVERY" value="<?=GetMessage('SPM_FORM_TITLE_OFFICE')?>" /><?=GetMessage('SPM_FORM_TITLE_OFFICE')?><br/>
						<input type="radio" name="DELIVERY" value="<?=GetMessage('SPM_FORM_TITLE_ADDRESS')?>" /><?=GetMessage('SPM_FORM_TITLE_ADDRESS')?><br/>
						<textarea name="ADDRESS"></textarea><br/>
						<span><?=GetMessage('SPM_FORM_TITLE_DELIVERY_FREE')?></span>
					</td>
				</tr>
				<tr>
					<td><?=GetMessage('SPM_FORM_TITLE_COMMENTS')?><br/>
					<textarea name="COMMENTS"></textarea></td>
				</tr>
				<tr>
					<td>
						<button id="smartpolis_order_form_close"><?=GetMessage('SPM_FORM_TITLE_CLOSE')?></button>
						<button id="smartpolis_order_form_submit" type="submit"><?=GetMessage('SPM_FORM_TITLE_SEND')?></button>
					</td>
				</tr>
			</table>
		</form>
	</div>
<?}?>