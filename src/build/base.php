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
    public function url() {
        return trim('http://' . $_SERVER['HTTP_HOST'] . '/' . trim($_SERVER['REQUEST_URI'], '/\\'), '/');
    }

    /**
     * 网站域名
     *
     * @return string
     */
    public function domain() {
        return defined('RUN_MODE') && RUN_MODE != 'HTTP' ? ''
        : trim('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/\\');
    }

    /**
     * 根据伪静态配置
     * 添加带有入口文件的链接
     *
     * @return string
     */
    public function web() {
        $root = $this->domain();

        return Config::get('http.rewrite') ? $root : $root . '/index.php';
    }

    /**
     * 获取来源页
     *
     * @return string
     */
    public function history() {
        return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
    }

}