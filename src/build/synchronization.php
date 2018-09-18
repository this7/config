<?php
header("Content-type: text/html; charset=utf-8");

/**
 * 常量区
 */
define('DS', DIRECTORY_SEPARATOR);

define('ROOT_DIR', __DIR__);

#获取配置文件
$config = file_get_contents(ROOT_DIR . DS . ".this7/config.json");
$config = str_replace("/", "\/", $config);
$config = json_decode($config, true);

#设置地址常量
define('L_URL', $config['localhost']);

define('S_URL', $config['server']);

/**
 * 函数区
 */

if (!function_exists('F')) {
    /**
     * 文件缓存
     *
     * @param $name
     * @param string $value
     * @param string $path
     *
     * @return bool
     */
    function F($name, $value = '[get]', $path = '.this7') {
        static $cache = [];

        $file = ROOT_DIR . DS . $path . '/' . $name . '.php';
        if ($value == '[del]') {
            if (is_file($file)) {
                unlink($file);
                if (isset($cache[$name])) {
                    unset($cache[$name]);
                }
            }
            return TRUE;
        }

        if ($value === '[get]') {
            if (isset($cache[$name])) {
                return $cache[$name];
            } else if (is_file($file)) {
                return $cache[$name] = include $file;
            } else {
                return FALSE;
            }
        }
        $data = "<?php if(!defined('ROOT_DIR'))exit;\nreturn " . var_export($value, TRUE) . ";\n?>";

        if (!is_dir($path)) {
            mkdir($path, 0755, TRUE);
        }

        if (!file_put_contents($file, $data)) {
            return FALSE;
        }

        $cache[$name] = $value;

        return TRUE;
    }
}

if (!function_exists('is_cli')) {
    /**
     * 判断是否是命令模式
     * @return boolean [description]
     */
    function is_cli() {
        return preg_match("/cli/i", php_sapi_name()) ? 1 : 0;
    }
}
if (!function_exists('is_json')) {
    /**
     * 判断是否是JSON
     * @param  json  $string   需要判断的数据
     * @return boolean
     */
    function is_json($str) {
        return !is_null(json_decode($str));
    }

}

if (!function_exists('to_json')) {
    /**
     * 数组转JSON
     * @param  array  $array 数组数据
     * @return json          返回JSON数据
     */
    function to_json($array = array()) {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

}

if (!function_exists('to_array')) {
    /**
     * JSON转数组
     * @param  string $json JSON数据
     * @return array        返回数组数据
     */
    function to_array($json = '') {
        return json_decode($json, true);
    }
}

if (!function_exists('P')) {
    /**
     * 打印输出数据
     * @Author   Sean       Yan
     * @DateTime 2018-09-07
     * @param    [type]     $name [description]
     * @param    integer    $type [description]
     */
    function P($name, $type = 1) {

        echo "<pre>" . print_r($name, true) . "</pre>";

    }
}

if (!function_exists('get_post')) {
    /**
     * 获取数据.
     *
     * @param string $data 定义变量
     *
     * @return [type] [description]
     */
    function get_post($data = '') {
        if ($_POST) {
            $data = $_POST;
        } else {
            $data = file_get_contents('php://input');
        }
        if (is_array($data)) {
            return $data;
        } elseif (is_json($data)) {
            return to_array($data);
        } else {
            return $data;
        }
    }
}

if (!function_exists('encrypt')) {
    /**
     * 信息加密函数
     * @param  string $data 需要加密数据
     * @param  string $key  加解密秘钥
     * @return string       返回加密数据
     */
    function encrypt($data = "", $key = "this7") {
        $char = $str = null;
        $key  = md5($key);
        $x    = 0;
        $len  = strlen($data);
        $l    = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }
}

if (!function_exists('decrypt')) {
    /**
     * 信息解密数据
     * @param  string $data 被加密字符串
     * @param  string $key  加解密秘钥
     * @return string       返回解密数据
     */
    function decrypt($data = "", $key = "this7") {
        $char = $str = null;
        $key  = md5($key);
        $x    = 0;
        $data = base64_decode($data);
        $len  = strlen($data);
        $l    = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }
}

if (!function_exists('to_mkdir')) {
    /**
     * 创建目录
     * @param    string    $path     目录名称，如果是文件并且不存在的情况下会自动创建
     * @param    string    $data     写入数据
     * @param    bool    $is_full  完整路径，默认False
     * @param    bool    $is_cover 强制覆盖，默认False
     * @return   bool    True|False
     */
    function to_mkdir($path = null, $data = null, $is_full = false, $is_cover = false) {
        #非完整路径进行组合
        if (!$is_full) {
            $path = ROOT_DIR . '/' . ltrim(ltrim($path, './'), '/');
        }
        $file = $path;
        #检测是否为文件
        $file_suffix = pathinfo($path, PATHINFO_EXTENSION);
        if ($file_suffix) {
            $path = pathinfo($path, PATHINFO_DIRNAME);
        } else {
            $path = rtrim($path, '/');
        }
        #执行目录创建
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
            chmod($path, 0777);
        }
        #文件则进行文件创建
        if ($file_suffix) {
            if (!is_file($file)) {
                if (!file_put_contents($file, $data)) {
                    return false;
                }
            } else {
                #强制覆盖
                if ($is_cover) {
                    if (!file_put_contents($file, $data)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}

if (!function_exists('scan_dir')) {
    /**
     * 获取驱动列表
     * @param  [type] $dir  需要获取的目录
     * @param  array  $ext  排除的文件后缀如：PHP HTML
     * @param  array  $list 需要排除的目录名
     * @return [type]       [description]
     */
    function scan_dir($dir, $ext = [], $list = []) {
        $path = array();
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if (!in_array($file, $path) && !in_array(pathinfo($file, PATHINFO_EXTENSION), $ext)) {
                        $list[] = $file;
                    }
                }
                closedir($handle);
            }
        }
        return $list;
    }
}

class this7 {

    public $file_list = [];

    /**
     * 获取URL请求
     * @Author   Sean       Yan
     * @DateTime 2018-09-07
     * @param    [type]     $url [description]
     * @return   [type]          [description]
     */
    public function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        #为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        #如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /**
     * 获取URL请求
     * @Author   Sean       Yan
     * @DateTime 2018-09-07
     * @param    [type]     $url [description]
     * @return   [type]          [description]
     */
    public function httpPost($url, $data) {
        $curl     = curl_init();
        $header[] = "Content-Type:application/json;charset=utf-8";
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //设置 HTTP 头字段的数组。格式： array('Content-type: text/plain', 'Content-length: 100')
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, 320));
        #为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        #如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    public function read_all_dir($dir) {
        $path   = array('.', '..', '.htaccess', '.this7', '.DS_Store', '.stignore', '.gitignore', '.git', '.stfolder');
        $ext    = array('lock');
        $not    = array("this7.php");
        $result = array();
        $handle = opendir($dir); //读资源
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if (!in_array($file, $path) && !in_array(pathinfo($file, PATHINFO_EXTENSION), $ext)) {
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($cur_path)) {
                        $this->file_list['dir'][] = $cur_path;
                        //判断是否为目录，递归读取文件
                        $result['dir'][$cur_path] = $this->read_all_dir($cur_path);
                    } else {
                        if (!in_array(basename($cur_path), $not)) {
                            $this->file_list['file'][] = $cur_path;
                            $result['file'][]          = $cur_path;
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }

    public function get_dir_list($dir = __DIR__) {
        $this->file_list = [];
        $this->read_all_dir($dir);
        return $this->checkDocument($dir);
    }

    /**
     * 检查文件
     * @return [type] [description]
     */
    public function checkDocument($path = __DIR__) {
        $file = [];
        #需要截取的本地字符串长度
        $leng = strlen($path);
        foreach ($this->file_list['file'] as $key => $value) {
            $name = substr($value, $leng);
            #获取文件唯一码
            $unique = str_replace(DS, "_", $name);
            #获取唯一建
            $key = MD5($unique);
            #获取文件信息
            $file[$key]['code'] = MD5(file_get_contents($value));
            $file[$key]['name'] = $name;
            $file[$key]['time'] = filemtime($value);
        }
        return $file;
    }

    /**
     * 检查本地
     * @param string $value [description]
     */
    public function CheckLocal($value = '') {
        #上一次的文件
        $last = to_array(F("present"));
        #本地文件列表
        $local = $this->get_dir_list();
        #本地数据存储
        F("present", to_json($local));
        #过滤文件列表
        $residue = array(
            "check"  => [],
            "delete" => []
        );
        if ($last) {
            foreach ($local as $key => $value) {
                unset($last[$key]);
            }
        }

        $residue['delete'] = empty($last) ? $residue['delete'] : array_merge($residue['delete'], $last);
        $residue['check']  = $local;
        return $residue;
    }

    /**
     * 文件比对
     * @param string $file [description]
     */
    public function CloneSpy($file, $delete) {
        #上一次的文件
        $last = to_array(F("present"));
        #本地文件列表
        $local = $this->get_dir_list();
        #过滤文件列表
        $residue = array(
            "local"  => [],
            "line"   => [],
            "delete" => []
        );
        foreach ($local as $key => $value) {
            if (isset($file[$key])) {
                #比对时间-本地旧于新的 需要下载
                if ($value['time'] < $file[$key]['time']) {
                    if ($value['code'] !== $file[$key]['code']) {
                        $residue['line'][$key] = $file[$key];
                    }
                }
                #比对时间-本地新于旧的 需要上传
                if ($value['time'] > $file[$key]['time']) {
                    if ($value['code'] !== $file[$key]['code']) {
                        $residue['local'][$key] = $value;
                    }
                }
            }
            #如果远程不存在则添加
            else {
                $residue['local'][$key] = $value;
            }
            #判断远程文件是否要删除
            if (isset($delete[$key])) {
                #比对时间-本地旧于新的 需要删除
                if ($value['time'] <= $delete[$key]['time']) {
                    unlink(ROOT_DIR . $value['name']);
                    unset($residue['local'][$key]);
                }
                #比对时间-本地新于旧的 需要上传
                if ($value['time'] > $delete[$key]['time']) {
                    $residue['local'][$key] = $value;
                }
            }
            #判断是否有删除文件
            if ($last) {
                unset($last[$key]);
            } else {
                $last = [];
            }
            unset($local[$key]);
            unset($file[$key]);
        }
        $residue['local']  = empty($local) ? $residue['local'] : array_merge($residue['local'], $local);
        $residue['line']   = empty($file) ? $residue['line'] : array_merge($residue['line'], $file);
        $residue['delete'] = empty($last) ? $residue['delete'] : array_merge($residue['delete'], $last);
        P($residue);
        //exit();
        return $residue;
    }

    /**
     * 检查删除点
     * @param string $value [description]
     */
    public function RemoveCheckpointEd($local = '') {
        #上一次的文件
        $last = to_array(F("present"));
        #过滤文件列表
        $residue = array(
            "delete" => []
        );
        foreach ($last as $key => $value) {
            unset($local[$key]);
            unset($file[$key]);
        }
    }

    /**
     * 接受校对
     * @param string $value [description]
     */
    public function AcceptCheck($file = '') {
        if (!F("present")) {
            #获取当下文件目录结构
            $present = $this->get_dir_list();
            F("present", to_json($present));
        }
        $residue = $this->CloneSpy($file['check'], $file['delete']);
        $upload  = $this->getLocalFile($residue['local']);
        $data    = array(
            "url"    => $this->domain() . "/this7.php?type=download",
            "save"   => $upload,
            "update" => $residue['line'],
            "delete" => $residue['delete'],
        );
        $url = $file['url'];
        return $this->httpPost($url, $data);
    }

    /**
     * 获取本地文件
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function getLocalFile($file = '') {
        #判断数据不为空
        if (empty($file)) {
            return [];
        }
        $data = [];
        foreach ($file as $key => $value) {
            $path = ROOT_DIR . $value['name'];
            if (is_file($path)) {
                #获取文件内容
                $value['content'] = encrypt(file_get_contents($path));
                $data[]           = $value;
            }
        }
        return $data;
    }

    /**
     * 文件回传
     * @param string $value [description]
     */
    public function FileBack($file = '') {
        #获取当下文件目录结构
        $present = $this->get_dir_list();
        $data    = [];
        #下载需要更新的文件
        if (isset($file['save']) && !empty($file['save'])) {
            foreach ($file['save'] as $key => $value) {
                $body = decrypt($value['content']);
                to_mkdir($value['name'], $body, false, true);
            }
        }
        #判断是否需要删除文件
        if (isset($file['delete']) && !empty($file['delete'])) {
            foreach ($file['delete'] as $key => $value) {
                if (isset($present[$key])) {
                    if ($value['time'] >= $present[$key]['time']) {
                        #此处可以匹配冲突
                        unlink(ROOT_DIR . $value['name']);
                    }
                }
            }
        }
        #判断是否需要提交更新文件
        if (isset($file['update']) && !empty($file['update'])) {
            $data['save'] = $this->getLocalFile($file['update']);
            $this->httpPost($file['url'], $data);
        }
        #更新当下文件目录结构
        $present = $this->get_dir_list();
        #存储当下文件目录结构
        F("present", to_json($present));
    }

    /**
     * 网站域名
     *
     * @return string
     */
    public static function domain() {
        if (is_cli()) {
            return L_URL;
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return defined('RUN_MODE') && RUN_MODE != 'HTTP' ? ''
        : trim($protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/\\');
    }
}

$this7 = new this7();
if (isset($_GET['type'])) {
    switch ($_GET['type']) {
    #下载数据文件
    case 'download':
        $data = get_post();
        return $this7->FileBack($data);
        break;
    #检查匹配文件
    case 'check':
        $data = get_post();
        echo $this7->AcceptCheck($data);
        break;
    }
}
if (is_cli()) {
    if ($this7->domain() == S_URL) {
        exit();
    }
    #设置提交数据
    $data        = $this7->CheckLocal();
    $data['url'] = $this7->domain() . "/this7.php?type=download";
    $url         = S_URL . "/this7.php?type=check";
    $ret         = $this7->httpPost($url, $data);
    P($ret);
}

//P($this7->get_dir_list());