<?php
/**
 * @version     1.0.0
 * @package     com_seopult
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Dmitry <mitrich.home@gmail.com> - http://redsoft.ru
 */


// No direct access
defined('_JEXEC') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

class SeopultController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        require_once JPATH_COMPONENT.'/helpers/seopult.php';

        // Load the submenu.
        SeopultHelper::addSubmenu(JRequest::getCmd('view', ''));

        $view		= JRequest::getCmd('view', '');
        JRequest::setVar('view', $view);

        parent::display();

        return $this;
    }

    public function readMsg()
    {
        $jinput = JFactory::getApplication()->input;
        $pult_id = $jinput->get('msg_id');
        $db = JFactory::getDbo();
        $query = "UPDATE #__seopult_messages SET `isRead` = '1' WHERE `pult_id` = $pult_id";
        $db->setQuery($query);
        $db->query();
    }

    function truncate()
    {
        $db = JFactory::getDbo();
        $query = "TRUNCATE TABLE `#__seopult_datas` ";
        $db->setQuery($query);
        $db->query();
        return;
    }

    function drop()
    {
        $db = JFactory::getDbo();
        $query = "DROP TABLE IF EXISTS`#__seopult_datas`";
        $db->setQuery($query);
        $db->query();
        return;
    }

    function seopult()
    {
        $jinput = JFactory::getApplication()->input;

        $log_date = date('Y:m:d h:i:s');

        $proceed_reg = false;
        $first_start = false;

        require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_seopult' . DS . 'libs' . DS . 'SimpleCrypt.php');

        $db = JFactory::getDbo();
        $user = JFactory::getUser();

        $config = new JConfig();

        $query = "SHOW TABLES FROM $config->db LIKE '".$config->dbprefix.'seopult_datas'."'";
        $db->setQuery($query);
        $table_exists = $db->loadResult();

        if (!$table_exists)
        {
            $query = "CREATE TABLE IF NOT EXISTS #__seopult_datas (" .
                $query = "\n  `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ," .
                    $query = "\n  `email` VARCHAR( 255 ) NOT NULL ," .
                        $query = "\n  `cryptKey` MEDIUMTEXT NOT NULL ,".
                            $query = "\n  `createdOn` DATETIME);";
            $db->setQuery($query);
            $db->query();

            $query = "CREATE TABLE IF NOT EXISTS #__seopult_messages (" .
                $query = "\n  `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ," .
                    $query = "\n  `pult_id` INT( 11 ) NOT NULL ," .
                        $query = "\n  `type` VARCHAR( 255 ) NOT NULL ," .
                            $query = "\n  `color` VARCHAR( 255 ) NOT NULL ," .
                                $query = "\n  `bgColor` VARCHAR( 255 ) NOT NULL ," .
                                    $query = "\n  `borderColor` VARCHAR( 255 ) NOT NULL ," .
                                        $query = "\n  `state` TINYINT(1) NULL ,".
                                            $query = "\n  `isRead` TINYINT(1) NULL ,".
                                                $query = "\n  `text` MEDIUMTEXT NOT NULL ,".
                                                    $query = "\n  `createdOn` DATETIME);";
            $db->setQuery($query);
            $db->query();

            $first_start = true;
            $proceed_reg = true;
        } else {
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__seopult_datas');
            $query->where('id = 1');
            $db->setQuery($query);
            $reg_datas = $db->loadObject();


            if (!$reg_datas)
            {
                $proceed_reg = true;
                $first_start = true;
            } else {
                if ($user->email != $reg_datas->email)
                {
                    //if other user exists
                    $rr_out = '<div id="errOut">11' . JText::_('ANOTHER_USER_FROM_THIS_DOMAIN_HAS_REGISTERED_IN_SEOPULT'). ' <a href="mailto:' . $reg_datas->email . '">' . $reg_datas->email . '</a> ' . JText::_('FOR_COMMUNICATE_WITH_HIM') . '</div>';
                    echo $rr_out;
                    exit;
                }
            }
        }

        $domain = str_replace('/administrator/','',JUri::base());
        $domain = str_replace('http://','',$domain);

        $nowDate =  date('Y-m-d h:i:s');

        if ($proceed_reg)
        {
            $data = array(
                'login' => $domain,
                'hash' => md5($domain),
                'createdOn' => $nowDate,
            );

            if ((!$first_start) || ($jinput->get('proceed',0,'INT') == 1))
            {
                $partner = 'cf8679773bcfc32c76722fc6f277c0ea';
                $version = new JVersion;
                $jversion = 'Joomla'.$version->RELEASE;
                $login_url = 'http://i.seopult.pro/iframe/getCryptKeyWithUserReg?login='.$domain.'&email='.$user->email.'&hash='.md5($domain).'&partner='.$partner.'&partnerVersion='.$jversion.'&url='.urlencode($domain);


                $res_json = file_get_contents($login_url);

                $res = json_decode($res_json);

                //user success created from SEOPULT side
                if ($res->status->code == 0)
                {
                    $cryptKey = $res->data->cryptKey;

                    $query = $db->getQuery(true);
                    $query->insert('#__seopult_datas');
                    $query->columns('`email`,`cryptKey`,`createdOn`');
                    $query->values($db->quote($user->email) . ', ' . $db->quote($cryptKey) . ', ' . $db->quote($nowDate));
                    $db->setQuery($query);
                    $db->query();

                    $this->redirectToSeopult($data,$cryptKey,$domain);
                } else {
                    //error detected
                    $err = '<div id="errOut">'.$res->status->message.'</div>';
                    echo $err;
                    exit;
                }
            }  else {
                $this->drawWelcome($domain);
                return;
            }
        } else {
            $data = array(
                'login' => $domain,
                'hash' => md5($domain),
                'createdOn' => $reg_datas->createdOn,
            );
            $cryptKey = $reg_datas->cryptKey;

            $this->redirectToSeopult($data,$cryptKey,$domain);
        }
    }

    private function drawWelcome($domain)
    {
        $jinput = JFactory::getApplication()->input;
        $stop = $jinput->get('stop') ? '&stop=1' : '';
        $f = file_get_contents(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_seopult' . DS . 'index.1.html');
        $f = str_replace('{localurl}','http://'.$domain.'/administrator/index.php?option=com_seopult&tmpl=component&proceed=1'.$stop,$f);
        echo $f;
        return;
    }

    private function redirectToSeopult($data,$cryptKey,$domain)
    {
        $jinput = JFactory::getApplication()->input;
        $log_date = date('Y:m:d h:i:s');

        $k = json_encode($data);
        $k = SimpleCrypt::encrypt($k,$cryptKey);
        $k = urlencode($k);


        $url = 'http://i.seopult.pro/iframe/cryptLogin?k=zaa'.$data['hash'].$k;

        if ($jinput->get('stop'))
        {
            echo '<pre>';
            var_dump($url);
            echo '</pre>';
            return;
        }

        if (!@file_get_contents($url))
        {
            $this->drawWelcome($domain);
        }   else {
            $this->setRedirect($url);
        }
    }

}
