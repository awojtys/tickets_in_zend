<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Polcode
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';
/**
 * @see Zend_Locale
 */
require_once 'Zend/Locale.php';

class Zend_Filter_UcWords implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if(is_string($value))
        {
            $value = ucwords($value);
        }
        
        return $value;
    }
}