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
namespace this7\config\build;

class base {

    /**
     * 当前链接地址
     *
     * @return string
     */
    public static function url() {
        $root = self::domain();
        return trim($root . '/' . trim($_SERVER['REQUEST_URI'], '/\\'), '/');
    }

    /**
     * 网站域名
     *
     * @return string
     */
    public static function domain() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return defined('RUN_MODE') && RUN_MODE != 'HTTP' ? ''
        : trim($protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/\\');
    }

    /**
     * 根据伪静态配置
     * 添加带有入口文件的链接
     *
     * @return string
     */
    public static function web() {
        $root = self::domain();
        return $root;
    }

    /**
     * 获取来源页
     *
     * @return string
     */
    public static function history() {
        return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
    }

}