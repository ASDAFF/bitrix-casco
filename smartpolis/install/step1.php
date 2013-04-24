<?php
/**
 * Модуль Умный Полис
 *
 * @file install/step1.php
 */

IncludeModuleLangFile(__FILE__);

?>
<p><?=GetMessage('SPM_INSTALL_TEXT')?></p>
<form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="smartpolis">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">
	<input type="submit" name="inst" value="<?=GetMessage('MOD_INSTALL')?>">
</form>