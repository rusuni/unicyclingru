<?php
/**
 *  @Copyright
 *  @package    Editor Button - SIGE Parameter Button - Plugin for Joomla! 3
 *  @author     Viktor Vogel {@link http://www.kubik-rubik.de}
 *  @version    3-1 - 2013-10-05
 *  @link       Project Site {@link http://joomla-extensions.kubik-rubik.de/sige-simple-image-gallery-extended}
 *
 *  @license GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgButtonsige_button extends JPlugin
{
    function plgButtonsige_button(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    function onDisplay($name)
    {
        $app = JFactory::getApplication();
        $document = JFactory::getDocument();
        JPlugin::loadLanguage('plg_editors-xtd_sige_button', JPATH_ADMINISTRATOR);
        JHTML::addIncludePath(JPATH_COMPONENT.'/helper');

        if($app->isAdmin())
        {
            $css = '.button2-left .sige_button {background: transparent url('.JURI::root(true).'/plugins/editors-xtd/sige_button/sige_button/images/j_button1_sige_button.png) no-repeat scroll 100% 0pt;}'."\n";
            $document->addStyleDeclaration($css);
        }

        $document->addStyleSheet(JURI::root(true).'/plugins/editors-xtd/sige_button/sige_button/css/sige_button.css.php');
        $getContent = $this->_subject->getContent($name);
        $method = $this->params->get('method', 0);

        if($method == 1)
        {
            $width = $this->params->get('width', 0);
            $height = $this->params->get('height', 0);
            $gap_v = $this->params->get('gap_v', 0);
            $gap_h = $this->params->get('gap_h', 0);
            $quality = $this->params->get('quality', 0);
            $quality_png = $this->params->get('quality_png', 0);
            $displaynavtip = $this->params->get('displaynavtip', 0);
            $displayarticle = $this->params->get('displayarticle', 0);
            $thumbs = $this->params->get('thumbs', 0);
            $limit = $this->params->get('limit', 0);
            $limit_quantity = $this->params->get('limit_quantity', 0);
            $noslim = $this->params->get('noslim', 0);
            $sort = $this->params->get('sort', 0);
            $root = $this->params->get('root', 0);
            $ratio = $this->params->get('ratio', 0);
            $caption = $this->params->get('caption', 0);
            $iptc = $this->params->get('iptc', 0);
            $iptcutf8 = $this->params->get('iptcutf8', 0);
            $print = $this->params->get('print', 0);
            $single = $this->params->get('single', 0);
            $scaption = $this->params->get('scaption', 0);
            $single_gallery = $this->params->get('single_gallery', 0);
            $salign = $this->params->get('salign', 0);
            $connect = $this->params->get('connect', 0);
            $download = $this->params->get('download', 0);
            $list = $this->params->get('list', 0);
            $crop = $this->params->get('crop', 0);
            $crop_factor = $this->params->get('crop_factor', 0);
            $thumbdetail = $this->params->get('thumbdetail', 0);
            $watermark = $this->params->get('watermark', 0);
            $watermarkposition = $this->params->get('watermarkposition', 0);
            $watermarkimage = $this->params->get('watermarkimage', 0);
            $encrypt = $this->params->get('encrypt', 0);
            $image_info = $this->params->get('image_info', 0);
            $image_link = $this->params->get('image_link', 0);
            $image_link_new = $this->params->get('image_link_new', 0);
            $column_quantity = $this->params->get('column_quantity', 0);
            $css_image = $this->params->get('css_image', 0);
            $css_image_half = $this->params->get('css_image_half', 0);
            $copyright = $this->params->get('copyright', 0);
            $word = $this->params->get('word', 0);
            $calcmaxthumbsize = $this->params->get('calcmaxthumbsize', 0);
            $fileinfo = $this->params->get('fileinfo', 0);
            $turbo = $this->params->get('turbo', 0);
            $resize_images = $this->params->get('resize_images', 0);
            $width_image = $this->params->get('width_image', 0);
            $height_image = $this->params->get('height_image', 0);
            $ratio_image = $this->params->get('ratio_image', 0);
            $images_new = $this->params->get('images_new', 0);

            $params = array();
            $params[] = JText::_('PLG_SIGE_BUTTON_FOLDER');

            if($width != 0)
            {
                $params[] = 'width='.$width;
            }

            if($height != 0)
            {
                $params[] = 'height='.$height;
            }

            if($gap_v != 0)
            {
                $params[] = 'gap_v='.$gap_v;
            }

            if($gap_h != 0)
            {
                $params[] = 'gap_h='.$gap_h;
            }

            if($quality != 0)
            {
                $params[] = 'quality='.$quality;
            }

            if($quality_png != 0)
            {
                $params[] = 'quality_png='.$quality_png;
            }

            if($displaynavtip != 0)
            {
                if($displaynavtip == 1)
                {
                    $params[] = 'displaynavtip=1';
                }
                elseif($displaynavtip == 2)
                {
                    $params[] = 'displaynavtip=0';
                }
            }

            if($displayarticle != 0)
            {
                if($displayarticle == 1)
                {
                    $params[] = 'displayarticle=1';
                }
                elseif($displayarticle == 2)
                {
                    $params[] = 'displayarticle=0';
                }
            }

            if($thumbs != 0)
            {
                if($thumbs == 1)
                {
                    $params[] = 'thumbs=1';
                }
                elseif($thumbs == 2)
                {
                    $params[] = 'thumbs=0';
                }
            }

            if($limit != 0)
            {
                if($limit == 1)
                {
                    $params[] = 'limit=1';
                }
                elseif($limit == 2)
                {
                    $params[] = 'limit=0';
                }
            }

            if($limit_quantity != 0)
            {
                $params[] = 'limit_quantity='.$limit_quantity;
            }

            if($noslim != 0)
            {
                if($noslim == 1)
                {
                    $params[] = 'noslim=1';
                }
                elseif($noslim == 2)
                {
                    $params[] = 'noslim=0';
                }
            }

            if($sort != 0)
            {
                $params[] = 'sort='.$sort;
            }

            if($root != 0)
            {
                if($root == 1)
                {
                    $params[] = 'root=1';
                }
                elseif($root == 2)
                {
                    $params[] = 'root=0';
                }
            }

            if($ratio != 0)
            {
                if($ratio == 1)
                {
                    $params[] = 'ratio=1';
                }
                elseif($ratio == 2)
                {
                    $params[] = 'ratio=0';
                }
            }

            if($caption != 0)
            {
                if($caption == 1)
                {
                    $params[] = 'caption=1';
                }
                elseif($caption == 2)
                {
                    $params[] = 'caption=0';
                }
            }

            if($iptc != 0)
            {
                if($iptc == 1)
                {
                    $params[] = 'iptc=1';
                }
                elseif($iptc == 2)
                {
                    $params[] = 'iptc=0';
                }
            }

            if($iptcutf8 != 0)
            {
                if($iptcutf8 == 1)
                {
                    $params[] = 'iptcutf8=1';
                }
                elseif($iptcutf8 == 2)
                {
                    $params[] = 'iptcutf8=0';
                }
            }

            if($print != 0)
            {
                if($print == 1)
                {
                    $params[] = 'print=1';
                }
                elseif($print == 2)
                {
                    $params[] = 'print=0';
                }
            }

            if($single != '')
            {
                $params[] = 'single='.$single;
            }

            if($scaption != '')
            {
                $params[] = 'scaption='.$scaption;
            }

            if($single_gallery != 0)
            {
                if($single_gallery == 1)
                {
                    $params[] = 'single_gallery=1';
                }
                elseif($single_gallery == 2)
                {
                    $params[] = 'single_gallery=0';
                }
            }

            if($salign != 0)
            {
                if($salign == 1)
                {
                    $params[] = 'salign=left';
                }
                elseif($salign == 2)
                {
                    $params[] = 'salign=right';
                }
                elseif($salign == 3)
                {
                    $params[] = 'salign=center';
                }
            }

            if($connect != '')
            {
                $params[] = 'connect='.$connect;
            }

            if($download != 0)
            {
                if($download == 1)
                {
                    $params[] = 'download=1';
                }
                elseif($download == 2)
                {
                    $params[] = 'download=0';
                }
            }

            if($list != 0)
            {
                if($list == 1)
                {
                    $params[] = 'list=1';
                }
                elseif($list == 2)
                {
                    $params[] = 'list=0';
                }
            }

            if($crop != 0)
            {
                if($crop == 1)
                {
                    $params[] = 'crop=1';
                }
                elseif($crop == 2)
                {
                    $params[] = 'crop=0';
                }
            }

            if($crop_factor != 0)
            {
                $params[] = 'crop_factor='.$crop_factor;
            }

            if($thumbdetail != 0)
            {
                $params[] = 'thumbdetail='.$thumbdetail;
            }

            if($watermark != 0)
            {
                if($watermark == 1)
                {
                    $params[] = 'watermark=1';
                }
                elseif($watermark == 2)
                {
                    $params[] = 'watermark=0';
                }
            }

            if($watermarkposition != 0)
            {
                $params[] = 'watermarkposition='.$watermarkposition;
            }

            if($watermarkimage != '')
            {
                $params[] = 'watermarkimage='.$watermarkimage;
            }

            if($encrypt != 0)
            {
                $params[] = 'encrypt='.$encrypt;
            }

            if($image_info != 0)
            {
                if($image_info == 1)
                {
                    $params[] = 'image_info=1';
                }
                elseif($image_info == 2)
                {
                    $params[] = 'image_info=0';
                }
            }

            if($image_link != '')
            {
                $params[] = 'image_link='.$image_link;
            }

            if($image_link_new != 0)
            {
                if($image_link_new == 1)
                {
                    $params[] = 'image_link_new=1';
                }
                elseif($image_link_new == 2)
                {
                    $params[] = 'image_link_new=0';
                }
            }

            if($column_quantity != 0)
            {
                $params[] = 'column_quantity='.$column_quantity;
            }

            if($css_image != 0)
            {
                if($css_image == 1)
                {
                    $params[] = 'css_image=1';
                }
                elseif($css_image == 2)
                {
                    $params[] = 'css_image=0';
                }
            }

            if($css_image_half != 0)
            {
                if($css_image_half == 1)
                {
                    $params[] = 'css_image_half=1';
                }
                elseif($css_image_half == 2)
                {
                    $params[] = 'css_image_half=0';
                }
            }

            if($copyright != 0)
            {
                if($copyright == 1)
                {
                    $params[] = 'copyright=1';
                }
                elseif($copyright == 2)
                {
                    $params[] = 'copyright=0';
                }
            }

            if($word != '')
            {
                $params[] = 'word='.$word;
            }

            if($calcmaxthumbsize != 0)
            {
                if($calcmaxthumbsize == 1)
                {
                    $params[] = 'calcmaxthumbsize=1';
                }
                elseif($calcmaxthumbsize == 2)
                {
                    $params[] = 'calcmaxthumbsize=0';
                }
            }

            if($fileinfo != 0)
            {
                if($fileinfo == 1)
                {
                    $params[] = 'fileinfo=1';
                }
                elseif($fileinfo == 2)
                {
                    $params[] = 'fileinfo=0';
                }
            }

            if($turbo != 0)
            {
                if($turbo == 1)
                {
                    $params[] = 'turbo=1';
                }
                elseif($turbo == 2)
                {
                    $params[] = 'turbo=0';
                }
                elseif($turbo == 3)
                {
                    $params[] = 'turbo=new';
                }
            }

            if($resize_images != 0)
            {
                if($resize_images == 1)
                {
                    $params[] = 'resize_images=1';
                }
                elseif($resize_images == 2)
                {
                    $params[] = 'resize_images=0';
                }
            }

            if($width_image != 0)
            {
                $params[] = 'width_image='.$width_image;
            }

            if($height_image != 0)
            {
                $params[] = 'height_image='.$height_image;
            }

            if($ratio_image != 0)
            {
                if($ratio_image == 1)
                {
                    $params[] = 'ratio_image=1';
                }
                elseif($ratio_image == 2)
                {
                    $params[] = 'ratio_image=0';
                }
            }

            if($images_new != 0)
            {
                if($images_new == 1)
                {
                    $params[] = 'images_new=1';
                }
                elseif($images_new == 2)
                {
                    $params[] = 'images_new=0';
                }
            }

            $params = '{gallery}'.implode(",", $params).'{/gallery}';
            $js = "function sige_button(editor) {var content = $getContent; jInsertEditorText('$params', editor);}";
            $document->addScriptDeclaration($js);

            $button = new JObject();
            $button->set('modal', false);
            $button->set('class', 'btn');
            $button->set('onclick', 'sige_button(\''.$name.'\');return false;');
            $button->set('text', JText::_('PLG_SIGE_BUTTON_SIGEBUTTONTEXT'));
            $button->set('name', 'camera');
            $button->set('link', '#');
        }
        elseif($method == 0)
        {
            $lang = JFactory::getLanguage();
            $folder_input = $this->params->get('folder_input');
            $read_in_folder = $this->params->get('read_in_folder');
            $token = md5($this->params->get('token'));

            if($app->isAdmin())
            {
                $link = '../plugins/editors-xtd/sige_button/sige_button.html.php?lang='.$lang->getTag().'&amp;e_name='.$name.'&amp;folder_input='.$folder_input.'&amp;read_in_folder='.$read_in_folder.'&amp;token='.$token;
            }
            else
            {
                $link = 'plugins/editors-xtd/sige_button/sige_button.html.php?lang='.$lang->getTag().'&amp;e_name='.$name.'&amp;folder_input='.$folder_input.'&amp;read_in_folder='.$read_in_folder.'&amp;token='.$token;
            }

            $button = new JObject();
            $button->set('modal', true);
            $button->set('class', 'btn');
            $button->set('link', $link);
            $button->set('text', JText::_('PLG_SIGE_BUTTON_SIGEBUTTONTEXT'));
            $button->set('name', 'camera');
            $button->set('options', "{handler: 'iframe', size: {x: 600, y: 550}}");
        }

        return $button;
    }

}
