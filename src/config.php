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
namespace this7\config;
use this7\config\build\base;
use this7\config\build\decide;

/**
 * 框架基础配置文件
 */
class config {
    /**
     * 设置存储
     * @var array
     */
    protected static $items = array(
        'GET'     => '',
        'POST'    => '',
        'REQUEST' => '',
        'SERVER'  => '',
        'GLOBALS' => '',
        'SESSION' => '',
        'COOKIE'  => '',
    );

    /**
     * 定义请求常量
     */
    public static function defineConst() {
        self::$items['POST']    = self::getPost();
        self::$items['GET']     = $_GET;
        self::$items['REQUEST'] = $_REQUEST;
        self::$items['SERVER']  = $_SERVER;
        self::$items['GLOBALS'] = $GLOBALS;
        self::$items['SESSION'] = $_SESSION;
        self::$items['COOKIE']  = $_COOKIE;
        defined('IS_DEFEND') or define('IS_DEFEND', false);
        defined('IS_GET') or define('IS_GET', decide::isMethod('get'));
        defined('IS_POST') or define('IS_POST', decide::isMethod('post'));
        defined('IS_DELETE') or define('IS_DELETE', decide::isMethod('delete'));
        defined('IS_PUT') or define('IS_PUT', decide::isMethod('put'));
        defined('IS_AJAX') or define('IS_AJAX', decide::isAjax());
        defined('IS_WECHAT') or define('IS_WECHAT', decide::isWeChat());
        defined('IS_DOMAIN') or define('IS_DOMAIN', decide::isDomain());
        defined('IS_HTTPS') or define('IS_HTTPS', decide::isHttps());
        defined('ROOT') or define('ROOT', base::web());
        defined('URL') or define('URL', base::url());
        define("HISTORY", isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '');
    }

    /**
     * @Author   Sean       Yan
     * @DateTime 2018-06-21
     * @param    array      $data 路由数据
     * @return   array            返回数组
     */
    public static function getPost($data = []) {
        if ($_POST) {
            $data = $_POST;
        } else {
            $data = file_get_contents('php://input');
        }
        if (is_json($data)) {
            return $_POST = to_array($data);
        } elseif (is_array($data)) {
            return $_POST = $data;
        } elseif (is_string($data)) {
            parse_str($data, $query_arr);
            return $_POST = $query_arr;
        } else {
            return $_POST = $data;
        }
    }

    /**
     * 执行配置文件.
     *
     * @param string $class 类名称
     *
     * @return array
     */
    public static function dispose($path, $class) {
        $Main = static::getInterior($path, $class);
        $New  = static::getExterior($class);
        if ($New) {
            return $New;
        } else {
            return $Main;
        }
    }

    /**
     * 获取内部配置
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public static function getInterior($path, $class) {
        $Main_Config = $path . DS . 'src' . DS . 'config.json';
        if (is_file($Main_Config)) {
            C($class, get_json($Main_Config, true, true));
            return C($class);
        } else {
            return false;
        }
    }

    /**
     * 获取外部配置
     * @param  string $class [description]
     * @return [type]        [description]
     */
    public static function getExterior($class = '') {
        $path = ROOT_DIR . DS . 'server' . DS . "config" . DS . $class . '.json';
        if (is_file($path)) {
            C($class, get_json($path, true, true));
            return C($class);
        } else {
            return false;
        }
    }

    /**
     * 数据错误接口
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public static function error($args) {
        if (is_string($args) || is_numeric($args)) {
            return [0, '成功', $args, ''];
        }
        if (is_array($args) && isset($args['__def'])) {
            unset($args['__def']);
            return [0, '成功', $args, ''];
        }
        #根据不同的参数个数进行判断
        switch (count($args)) {
        case 0:
            return [0, '成功', [], ''];
            break;
        case 1:
            $arg1 = reset($args);
            #如果是数字
            if (is_numeric($arg1)) {
                if ($arg1 === 0) {
                    return [0, '成功', [], ''];
                } else {
                    return [$arg1, '错误', [], ''];
                }
            }
            #如果是字符串
            if (is_string($arg1)) {
                return [0, $arg1, [], ''];
            }
            #如果是数组
            if (is_array($arg1)) {
                if (empty($arg1)) {
                    return [-2, '数据为空', [], ''];
                } else {
                    return [0, '成功', $arg1, ''];
                }
            }
            break;
        case 2:
            $arg1 = reset($args);
            $arg2 = next($args);
            if (is_numeric($arg1) && is_string($arg2)) {
                return [$arg1, $arg2, [], ''];
            }
            if (is_string($arg1) && is_array($arg2)) {
                if (empty($arg2)) {
                    return [-2, $arg1, [], ''];
                } else {
                    return [0, '成功', $arg2, ''];
                }
            }
            if (is_array($arg1) && is_array($arg2)) {
                #如果数组为空
                if (empty($arg2) && empty($arg1)) {
                    return [-2, '失败', [], ''];
                }
                #如果第二个参数为空
                elseif (empty($arg2)) {
                    return [-2, reset($arg1), [], ''];
                } else {
                    $arg1 = next($arg1);
                    $arg1 = empty($arg1) ? '成功' : $arg1;
                    return [0, $arg1, $arg2, ''];
                }
            }
            break;
        case 3:
            $arg1 = reset($args);
            $arg2 = next($args);
            $arg3 = next($args);
            if (is_object($arg1) && is_string($arg2) && is_array($arg3)) {
                return [$arg1, $arg2, $arg3, ''];
            }
            if (is_numeric($arg1) && is_string($arg2) && is_array($arg3)) {
                return [$arg1, $arg2, $arg3, ''];
            }
            if (is_numeric($arg1) && is_string($arg2) && is_numeric($arg3)) {
                return [$arg1, $arg2, $arg3, ''];
            }
            if (is_numeric($arg1) && is_string($arg2) && is_string($arg3)) {
                $arg3 = static::params($arg3) ? static::params($arg3) : $arg3;
                return [$arg1, $arg2, [], $arg3];
            }
            if (is_array($arg1) && is_array($arg2) && is_string($arg3)) {
                #如果数组为空
                if (empty($arg2) && empty($arg1)) {
                    return [-2, '失败', [], ''];
                }
                #如果第二个参数为空
                elseif (empty($arg2)) {
                    return [-2, reset($arg1), [], ''];
                } else {
                    $arg1 = next($arg1);
                    $arg1 = empty($arg1) ? '成功' : $arg1;
                    $arg3 = static::params($arg3) ? static::params($arg3) : $arg3;
                    return [0, $arg1, $arg2, $arg3];
                }
            }
            break;
        case 4:
        default:
            $arg1 = reset($args);
            $arg2 = next($args);
            $arg3 = next($args);
            $arg4 = next($args);
            if (is_numeric($arg1) && is_string($arg2) && is_array($arg3) && is_string($arg4)) {
                $arg4 = static::params($arg4) ? static::params($arg4) : $arg4;
                return [$arg1, $arg2, $arg3, $arg4];
            }
            break;
        }
        return [0, '其他', $args, ''];
    }

    /**
     *  参数设置
     * @Author   Sean       Yan
     * @DateTime 2018-07-03
     * @param    [type]     $url [description]
     * @return   [type]          [description]
     */
    public static function params($url) {
        $path  = explode('?', $url)[0];
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            return site_url($path, $query);
        } else {
            return false;
        }
    }

    /**
     * 获取请求的类型
     * GET/POST/DELETE/PUT
     *
     * @return mixed
     */
    public function getRequestType() {
        $type = ['PUT', 'DELETE', 'POST', 'GET'];
        foreach ($type as $t) {
            if ($this->isMethod($t)) {
                return $t;
            }
        }
    }

}