/**
* @version		JF_KU_DTF_078
* @author		JoomForest http://www.joomforest.com
* @copyright	Copyright (C) 2011-2014 JoomForest.com
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

jQuery(document).ready(function($){
	/* _____________________________________================= START - ADD "POWERED BY" CLASS IDENTIFICATOR ===================== _____________________________ */
		$("#Kunena").next().addClass('jf_ku_poweredby');
		$(".kfooter").after($(".jf_ku_poweredby"));
	/* _____________________________________================= END - ADD "POWERED BY" CLASS IDENTIFICATOR ===================== _______________________________ */
});