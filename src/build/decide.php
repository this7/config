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

class decide {

    /**
     * 是否为异步提交
     *
     * @return bool
     */
    public static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * https请求
     *
     * @return bool
     */
    public static function isHttps() {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }

        return false;
    }

    /**
     * 微信客户端检测
     *
     * @return bool
     */
    public static function isWeChat() {
        return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    /**
     * 判断请求来源是否为本网站域名
     *
     * @return bool
     */
    public static function isDomain() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = parse_url($_SERVER['HTTP_REFERER']);
            return $referer['host'] == $_SERVER['HTTP_HOST'];
        }
        return false;
    }

    /**
     * 手机客户端判断
     *
     * @return bool
     */
    public static function isMobile() {
        //微信客户端检测
        if (static::isWeChat()) {
            return true;
        }
        if (!empty($_GET['_mobile'])) {
            return true;
        }
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser      = '0';
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }
        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }
        $mobile_ua     = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = ['w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-',
        ];
        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
            $mobile_browser++;
        }
        # 如果用户在Windows上，预先检查重置所有内容
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
            $mobile_browser = 0;
        }
        # 如果用户在Windows上，则是预final检查，但WP7也是Windows，其特点略有不同。
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
            $mobile_browser++;
        }
        if ($mobile_browser > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断请求类型
     *
     * @param $action
     *
     * @return bool
     */
    public static function isMethod($action) {
        switch (strtoupper($action)) {
        case 'GET':
            return $_SERVER['REQUEST_METHOD'] == 'GET';
        case 'POST':
            return $_SERVER['REQUEST_METHOD'] == 'POST';
        case 'DELETE':
            return $_SERVER['REQUEST_METHOD'] == 'DELETE'
            ?: (isset($_POST['_method']) && $_POST['_method'] == 'DELETE');
        case 'PUT':
            return $_SERVER['REQUEST_METHOD'] == 'PUT'
            ?: (isset($_POST['_method']) && $_POST['_method'] == 'PUT');
        case 'AJAX':
            return static::isAjax();
        case 'wechat':
            return static::isWeChat();
        case 'mobile':
            return static::isMobile();
        }
    }
}