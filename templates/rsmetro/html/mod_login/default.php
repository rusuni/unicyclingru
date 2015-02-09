<?php

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
?>
<div class="mains">
    <div class="rs_panel">
        <a href="#login_form" id="login_pop"><?php echo JText::_('TPL_RSMETRO_JLOGIN') ?></a>
        <a href="#join_form" id="join_pop"><?php echo JText::_('TPL_RSMETRO_LOGIN_REGISTER'); ?></a>		
    </div>
</div>
	
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
    <a href="#x" class="overlay" id="login_form"></a>
    <div class="popup">
	    <?php if ($params->get('pretext')): ?>
		<div class="pretext">
		    <p><?php echo $params->get('pretext'); ?></p>
		</div>
	    <?php endif; ?>
	    <div class="userdata">
		    <div id="form-login-username" class="mod_control-group">
				<label for="modlgn-username" class="element-invisible"><?php echo JText::_('TPL_RSMETRO_USERNAME'); ?></label>
				<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="1" size="18" placeholder="<?php echo JText::_('TPL_RSMETRO_USERNAME') ?>" />
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="btn hasTooltip" title="<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?>"></a>
		    </div>
		<div id="form-login-password" class="mod_control-group">
				<label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('TPL_RSMETRO_PASSWORD'); ?></label>
				<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="2" size="18" placeholder="<?php echo JText::_('TPL_RSMETRO_PASSWORD') ?>" />
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="btn hasTooltip" title="<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?>"></a>
		</div>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember"> 
			<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
			<label for="modlgn-remember" class="checkbox"><?php echo JText::_('TPL_RSMETRO_REMEMBER_ME') ?></label>
		</div>
		<?php endif; ?>
		<div class="com_users_view">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('TPL_RSMETRO_LOGIN_RESET'); ?></a>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('TPL_RSMETRO_LOGIN_REMIND'); ?></a>
		</div>
		<div id="form-login-submit" class="control-group">
			<button type="submit" tabindex="3" name="Submit" class="btn btn-primary btn"><?php echo JText::_('TPL_RSMETRO_JLOGIN') ?></button>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
	<?php if ($params->get('posttext')): ?>
		<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?><a class="close" href="#close"></a>
    </div>
</form>
<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal">
    <a href="#x" class="overlay" id="join_form"></a>
    <div class="popup">
		<div class="control-group">
        	<div class="control-label">
		        <label for="jform_name" id="jform_name-lbl"><?php echo JText::_('TPL_RSMETRO_NAME_LABEL') ?><span class="star">&nbsp;*</span></label>
            </div>		
            <div class="controls">
		        <input type="text" maxlength="50" class="inputbox required" value="" size="40" id="jform_name" name="jform[name]"/>
            </div>
        </div>				
        <div class="control-group">
            <div class="control-label">
		        <label for="jform_username" id="jform_username-lbl"><?php echo JText::_('TPL_RSMETRO_USERNAME_LABEL') ?><span class="star">&nbsp;*</span></label>
            </div>
            <div class="controls">
	            <input type="text" maxlength="25" class="inputbox required validate-username" value="" size="40" name="jform[username]" id="jform_username"/>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
		        <label for="jform_password1" id="jform_password1-lbl"><?php echo JText::_('TPL_RSMETRO_PASSWORD1_LABEL') ?><span class="star">&nbsp;*</span></label>
            </div>
            <div class="controls">
		        <input type="password" value="" size="40" name="jform[password1]" id="jform_password1" class="inputbox required validate-password"/>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
		        <label for="jform_password2" id="jform_password2-lbl"><?php echo JText::_('TPL_RSMETRO_PASSWORD2_LABEL') ?><span class="star">&nbsp;*</span></label>
            </div>
            <div class="controls">
  		        <input type="password" value="" size="40" name="jform[password2]" id="jform_password2" class="inputbox required validate-password"/>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
		        <label for="jform_email1" id="jform_email1-lbl"><?php echo JText::_('TPL_RSMETRO_EMAIL1_LABEL') ?><span class="star">&nbsp;*</span></label>
            </div>
            <div class="controls">
	            <input type="text" maxlength="100" class="inputbox required validate-email" value="" size="40" name="jform[email1]" id="jform_email1"/>
            </div>
        </div>
        <div class="control-group">			
            <div class="control-label">
		        <label for="jform_email2" id="jform_email2-lbl"><?php echo JText::_('TPL_RSMETRO_EMAIL2_LABEL') ?><span class="star">&nbsp;*</span></label>
            </div>
            <div class="controls">
		        <input type="text" maxlength="100" class="inputbox required validate-email" value="" size="40" name="jform[email2]" id="jform_email2"/>
            </div>
        </div>
        <div><?php echo JText::_('TPL_RSMETRO_REGISTER_REQUIRED') ?></div>
	    <button type="submit" class="button validate"><?php echo JText::_('MOD_LOGIN_REGISTER'); ?></button>
	    <input type="hidden" name="option" value="com_users" />
	    <input type="hidden" name="task" value="registration.register" />
	    <?php echo JHtml::_('form.token');?>
	    <a class="close" href="#close"></a>
    </div>
</form>
