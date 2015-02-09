<?php
/**
* @version		1.0
* @author		JoomForest http://www.joomforest.com
* @copyright	Copyright (C) 2011-2014 JoomForest.com
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class jf_ku_themeInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent){}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent){}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent){}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent){}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		
		/*
			$installer			= JInstaller::getInstance();
			$path				= $installer->getPath('manifest');
			$communityVersion	= $installer->getManifest()->version;
			if ( version_compare(JVERSION,'2.5.6','<') && $communityVersion >= '2.8.0'){
				JError::raiseNotice(1, 'JomSocial 2.8.x require minimum Joomla! CMS 2.5.6');
				return false;
			}
		*/

		$com_kunena 		= JPATH_ROOT.'/administrator/components/com_kunena/';
		//m $buffer      	= "installing";
		jimport('joomla.filesystem.file');
		
		if(!JFolder::exists($com_kunena)){
		// if(!JFile::write($destins)){
			ob_start();
			?>
				<style>
					.jf_btn_alert {
						display: block;
						padding: 20px;
						font-size: 22px;
						color: #FFF;
						background: #C00;
						text-align: center;
						line-height: 38px;
					}
					.jf_btn_alert p {
						font-size: 14px;
						margin: 0 0 -10px 0;
					}
					.jf_btn_alert a {
						border-bottom: 1px dashed #FFF;
						color: #fff;
					}
					.jf_btn_alert a:hover {
						border-bottom: 1px solid #FFF;
						text-decoration: none;
					}
					body table.adminform,
					body table.adminform tbody,
					body table.adminform tbody tr,
					body table.adminform tbody tr td {
						display:block;
					}
					body #system-message-container{display:none!important}
				</style>
				<div class="jf_btn_alert">
					You haven't Installed Kunena Component. For downloading please visit: <a href="http://www.kunena.org" target="_blank">www.kunena.org</a>
					<p>When you install it, please again try to install our Kunena Template</p>
				</div>
			<!-- 
				<table width="100%" border="0">
					<tr>
						<td>
							There was an error while trying to create an installation file.
							Please ensure that the path <strong><?php echo $com_kunena; ?></strong> has correct permissions and try again.
						</td>
					</tr>
				</table> 
			-->
			<?php
			$html = ob_get_contents();
			@ob_end_clean();
		} else {
			
			$jf_ku_theme_name			= 'jf_business_ku_3';
			$link = rtrim(JURI::root(), '/').'/administrator/index.php?option=com_kunena&view=templates';

			ob_start();
			?>
				<style type="text/css">
					.jf_success {
						text-align: center;
						padding: 0 10px;
						line-height: 22px;
					}
					.jf_success h3 {
						font-size: 18px;
						line-height: 20px;
					}
					.jf_btn {
						margin: 10px 0px 20px;
						padding: 10px 20px;
						font-size: 16px;
						color: #FFF;
						background: #009EB4;
						border: 0;
						cursor: pointer;
					}
					.jf_btn:hover {
						background: #008FA3;
					}
					.jf_btn:focus {
						background: #007C8D !important;
					}
					body table.adminform,
					body table.adminform tbody,
					body table.adminform tbody tr,
					body table.adminform tbody tr td {
						display:block;
					}
					body table.adminform {
						margin: 0 0 20px 0;
					}
				</style>
				
				<div class="jf_success" width="100%" border="0">
					<h3>JoomForest Kunena Template installation was <u>Successful</u> !</h3>
					<p>Thank you very much for downloading our Template, please click below to check our KU Template features and options. <br> We hope you enjoy it <b>:)</b></p>
					<input type="button" class="jf_btn" onclick="window.location = '<?php echo $link; ?>'" value="<?php echo JText::_('Go to Kunena Template Manager');?>"/>
				</div>
			<?php
			$html = ob_get_contents();
			@ob_end_clean();
		}

		echo $html;
	}
}