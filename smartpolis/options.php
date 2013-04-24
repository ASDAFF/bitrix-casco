<?php
/**
 * Модуль Умный Полис
 *
 * @file options.php
 */

if (!$USER->IsAdmin())
    return;

$acl = $APPLICATION->GetGroupRight($mid);

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule($mid);
CModule::IncludeModule('fileman');

$aTabs = array(
    array(
        'DIV'   => 'calculations',
        'TAB'   => GetMessage('SPM_TAB_CALCULATIONS_NAME'),
        'ICON'  => 'ib_settings',
        'TITLE' => GetMessage('SPM_TAB_CALCULATIONS_TITLE')
    ),
    array(
        'DIV'   => 'mode',
        'TAB'   => GetMessage('SPM_TAB_MODE_NAME'),
        'ICON'  => 'ib_settings',
        'TITLE' => GetMessage('SPM_TAB_MODE_TITLE')
    ),
    array(
        'DIV'   => 'messages',
        'TAB'   => GetMessage('SPM_TAB_MESSAGES_NAME'),
        'ICON'  => 'ib_settings',
        'TITLE' => GetMessage('SPM_TAB_MESSAGES_TITLE')
    ),
    array(
        'DIV'   => 'companies',
        'TAB'   => GetMessage('SPM_TAB_COMPANIES_NAME'),
        'ICON'  => 'ib_settings',
        'TITLE' => GetMessage('SPM_TAB_COMPANIES_TITLE')
    ),
);

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$settings = new SmartpolisSettings();

if ($_POST['Update'] === 'Y') {
    try {
        $settings->updateCompanies(new SmartpolisCascoApi());
    } catch (Exception $e) {
        echo CAdminMessage::ShowMessage(
            array(
                'TYPE'    => 'ERROR',
                'MESSAGE' => GetMessage('SPM_MESSAGE_UPDATE_ERR'),
                'DETAILS' => $e->getMessage(),
                'HTML'    => true,
            )
        );
    }
}

if ($_POST['Save'] === 'Y') {
    try {
        $settings->set($_POST);
    } catch (Exception $e) {
        echo CAdminMessage::ShowMessage(
            array(
                'TYPE'    => 'ERROR',
                'MESSAGE' => GetMessage('SPM_MESSAGE_SAVE_ERR'),
                'DETAILS' => $e->getMessage(),
                'HTML'    => true,
            )
        );
    }
}

echo '<form method="post" action="' . $APPLICATION->GetCurPage() . '?mid=' . htmlspecialcharsbx($mid) . '&lang=' . LANG . '">';
echo bitrix_sessid_post();

$tabControl->Begin();

$tabControl->BeginNextTab();
?>
	<tr>
		<td><label for="smartpolis_auth_type_token"><?=GetMessage('SPM_OPTION_AT_TOKEN')?></label></td>
		<td><input type="radio" id="smartpolis_auth_type_token" name="smartpolis_auth_type" value="by_token"<?=$settings->get('smartpolis_auth_type') == 'by_token' ? ' checked="checked"' : ''?>></td>
	</tr>
	<tr>
		<td><label for="smartpolis_auth_type_ip"><?=GetMessage('SPM_OPTION_AT_IP')?></label></td>
		<td><input type="radio" id="smartpolis_auth_type_ip" name="smartpolis_auth_type" value="by_ip"<?=$settings->get('smartpolis_auth_type') == 'by_ip' ? ' checked="checked"' : ''?>></td>
	</tr>
	<tr>
		<td><label for="smartpolis_auth_token"><?=GetMessage('SPM_OPTION_TOKEN_VAL')?></label></td>
		<td><input type="text" id="smartpolis_auth_token" name="smartpolis_auth_token" value="<?=htmlspecialcharsbx($settings->get('smartpolis_auth_token'))?>"></td>
	</tr>
<?

$tabControl->BeginNextTab();
?>
	<tr>
		<td width="30%"><label for="smartpolis_show_type_form_after_show"><?=GetMessage('SPM_OPTION_ST_FAS')?></label></td>
		<td width="30%"><input type="radio" id="smartpolis_show_type_form_after_show" name="smartpolis_show_type" value="form_after_show"<?=$settings->get('smartpolis_show_type') == 'form_after_show' ? ' checked="checked"' : ''?>></td>
	</tr>
	<tr>
		<td width="30%"><label for="smartpolis_show_type_show_after_form"><?=GetMessage('SPM_OPTION_ST_SAF')?></label></td>
		<td width="30%"><input type="radio" id="smartpolis_show_type_show_after_form" name="smartpolis_show_type" value="show_after_form"<?=$settings->get('smartpolis_show_type') == 'show_after_form' ? ' checked="checked"' : ''?>></td>
	</tr>
	<tr>
		<td width="30%"><label for="smartpolis_show_type_send_by_letter"><?=GetMessage('SPM_OPTION_ST_SBL')?></label></td>
		<td width="30%"><input type="radio" id="smartpolis_show_type_send_by_letter" name="smartpolis_show_type" value="send_by_letter"<?=$settings->get('smartpolis_show_type') == 'send_by_letter' ? ' checked="checked"' : ''?>></td>
	</tr>
<?

$tabControl->BeginNextTab();
?>
	<tr>
		<td width="30%"><label for="smartpolis_message_before_button"><?=GetMessage('SPM_OPTION_M_BB')?></label></td>
		<td><?CFileMan::AddHTMLEditorFrame('smartpolis_message_before_button', $settings->get('smartpolis_message_before_button'), 'smartpolis_message_before_button_type', 'html')?></td>
	</tr>
	<tr>
		<td width="30%"><label for="smartpolis_header_before_form"><?=GetMessage('SPM_OPTION_H_BF')?></label></td>
		<td><?CFileMan::AddHTMLEditorFrame('smartpolis_header_before_form', $settings->get('smartpolis_header_before_form'), 'smartpolis_header_before_form_type', 'html')?></td>
	</tr>
	<tr>
		<td width="30%"><label for="smartpolis_message_before_form"><?=GetMessage('SPM_OPTION_M_BF')?></label></td>
		<td><?CFileMan::AddHTMLEditorFrame('smartpolis_message_before_form', $settings->get('smartpolis_message_before_form'), 'smartpolis_message_before_form_type', 'html')?></td>
	</tr>
<?

$companies = $settings->get('smartpolis_companies');

$tabControl->BeginNextTab();
?>
	<tr>
		<td colspan="2">
			<?if (count($companies)) {?>
				<table class="internal">
					<tbody>
						<tr class="heading">
							<td><?=GetMessage('SPM_TABLE_ACTIVE')?></td>
							<td><?=GetMessage('SPM_TABLE_LOGO')?></td>
							<td><?=GetMessage('SPM_TABLE_COMPANY')?></td>
							<td><?=GetMessage('SPM_TABLE_DISCOUNT')?></td>
						</tr>
						<?foreach ($companies as $id => $company) {?>
							<tr>
								<td style="text-align:center">
									<input type="checkbox" name="smartpolis_companies[<?=$id?>][active]" value="Y"<?=$company['active'] ? ' checked="checked"' : ''?>>
								</td>
								<td style="text-align:center"><img src="http://casco.cmios.ru/<?=$company['logo']?>"></td>
								<td style="text-align:center"><?=$company['title']?></td>
								<td style="text-align:center">
									<input type="text" name="smartpolis_companies[<?=$id?>][discount]" value="<?=number_format($company['discount'], 2, '.', '')?>">
								</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			<?} else {?>
				<div class="adm-info-message-wrap">
					<div class="adm-info-message">
						<?=GetMessage('SPM_MESSAGE_UPDATE_WARN')?>
					</div>
				</div>
			<?}?>
		</td>
	</tr>
<?

$tabControl->Buttons();
?>
	<input type="submit" name="Save" value="<?=GetMessage('MAIN_SAVE')?>" onclick="document.getElementById('smartpolisSaveHidden').value = 'Y'">
	<input type="hidden" id="smartpolisSaveHidden" name="Save" value="N">
	<input type="reset" name="reset" value="<?=GetMessage('MAIN_RESET')?>">
	<input type="submit" name="Update" value="<?=GetMessage('SPM_BTN_UPDATE_DICT')?>" onclick="document.getElementById('smartpolisUpdateHidden').value = 'Y'">
	<input type="hidden" id="smartpolisUpdateHidden" name="Update" value="N">
<?

$tabControl->End();

echo '</form>';

?>