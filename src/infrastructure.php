<?php
/**
 * this7 PHP Framework
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2016-2018 Yan TianZeng<qinuoyun@qq.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ub-7.com
 */
header("Content-Type:text/html;charset=utf-8"); //设置系统的输出字符为utf-8

session_start(); //开始服务器session

ob_start(); //开启冲出缓存

echo "<script>console.log('欢迎使用This7框架')</script>";

date_default_timezone_set("PRC"); //设置时区（中国)

defined('DEBUG') or define('DEBUG', true); //DEBUG调试器

defined('XDEBUG') or define('XDEBUG', false); //XDEBUG调试器

defined('LOGIN') or define('LOGIN', false); //登录控制器

defined('SYNSERVER') or define('SYNSERVER', ""); //同步服务器

/**
 * 判断PHP版本是否符合
 */
if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    die('This7 需要PHP版本不低于php5.6.0,当前版本' . PHP_VERSION);
}

/**
 * 设置名称及版本号
 */
defined('VERSION') or define('VERSION', "V3.0.8");

defined('FRAMEWORK') or define('FRAMEWORK', "this7");

defined('FRAMEKEY') or define('FRAMEKEY', "www.this7.com");

/**
 * 开始运行时间和内存使用
 */
defined('START_NOW') or define('START_NOW', $_SERVER['REQUEST_TIME']);

defined('START_TIME') or define('START_TIME', microtime(true));

defined('START_MEM') or define('START_MEM', memory_get_usage());

/**
 * 设置物理路径
 */

define('DS', DIRECTORY_SEPARATOR);

define('FRAME_DIR', dirname(dirname(dirname(__FILE__))));

define('ROOT_DIR', dirname(dirname(FRAME_DIR)));

define('VENDOR_DIR', ROOT_DIR . DS . 'vendor');