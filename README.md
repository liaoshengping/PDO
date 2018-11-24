<div id="content_views" class="markdown_views prism-atom-one-dark">
							<!-- flowchart 箭头图标 勿删 -->
							<svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><path stroke-linecap="round" d="M5,0 0,2.5 5,5z" id="raphael-marker-block" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path></svg>
							<h2><a name="t0"></a><a id="_0" target="_blank"></a>使用方法</h2>
<p><a href="https://github.com/liaoshengping/PDO" rel="nofollow" target="_blank">项目仓库</a><br>
1.namespace 改成自己项目中的<br>
2.new  这个PDO 类，注意是你命名空间的PDO（你也可以把这个类名改掉）</p>
<p>连接数据库</p>
<pre class="prettyprint"><code class="has-numbering">$PDO = PDO::getInstance($dbHost='', $dbUser ='', $dbPasswd  ='', $dbName ='', $dbCharset='');
</code><ul class="pre-numbering" style=""><li style="color: rgb(153, 153, 153);">1</li></ul></pre>
<h2><a name="t1"></a><a id="_10" target="_blank"></a>新增</h2>
<p>eg:  users信息</p>
<pre class="prettyprint"><code class="has-numbering">$PDO-&gt;table('users')-&gt;insert(['name'=&gt;'liaosp']);
</code><ul class="pre-numbering" style=""><li style="color: rgb(153, 153, 153);">1</li></ul></pre>
<h2><a name="t2"></a><a id="_18" target="_blank"></a>获取</h2>
<p>查询一条：</p>
<pre class="prettyprint"><code class="has-numbering">$data =$PDO-&gt;table('oauth_clients')-&gt;where("client_id != 'admin'")-&gt;find();
</code><ul class="pre-numbering" style=""><li style="color: rgb(153, 153, 153);">1</li></ul></pre>
<p>获取多条</p>
<pre class="prettyprint"><code class="has-numbering">$data =$PDO-&gt;table('oauth_clients')-&gt;where("client_id != 'admin'")-&gt;get();
</code><ul class="pre-numbering" style=""><li style="color: rgb(153, 153, 153);">1</li></ul></pre>
<h2><a name="t3"></a><a id="_31" target="_blank"></a>更新</h2>
<pre class="prettyprint"><code class="has-numbering">$data =$PDO-&gt;table('oauth_clients')-&gt;where("id = 2")-&gt;update(['admin'=&gt;'liaosp']);
</code><ul class="pre-numbering" style=""><li style="color: rgb(153, 153, 153);">1</li></ul></pre>
<p><a href="https://github.com/liaoshengping/PDO" rel="nofollow" target="_blank">项目仓库</a></p>
<pre class="prettyprint"><code class="has-numbering">&lt;?php
/**
 * @author liaosp.top
 * @Time: 2018/11/24 -9:19
 * @Version 1.0
 * @Describe:
 * 1:
 * 2:
 * ...
 */

class PDOs
{
    protected static $_instance = null;
    protected $dbName = '';
    protected $dsn;
    protected $dbh;
    public $table =[];
    protected $where;
    protected $orderFiled;
    protected $orderDesc;

    /**
     * 构造
     * @return PDO
     */
    private function __construct($dbHost, $dbUser, $dbPasswd, $dbName, $dbCharset)
    {
        try {
            $this-&gt;dsn = 'mysql:host=' . $dbHost . ';dbname=' . $dbName;
            $this-&gt;dbh = new \PDO($this-&gt;dsn, $dbUser, $dbPasswd);
            $this-&gt;dbh-&gt;exec('SET character_set_connection=' . $dbCharset . ', character_set_results=' . $dbCharset . ', character_set_client=binary');
        } catch (\PDOException $e) {
            $this-&gt;outputError($e-&gt;getMessage());
        }
    }
    public function table($table){
        $this-&gt;table= $table;
        return $this;
    }
    public function where($where){
        $this-&gt;where = $where;
        return $this;
    }
    public function get(){
        $sql= $this-&gt;selectgetDataMatch();
        $recordset = $this-&gt;dbh-&gt;query($sql);
        if(!is_bool($recordset)){
            $result = $recordset-&gt;fetchAll();
        }
        return $result;
    }
    public function order($filed,$desc){
        $this-&gt;orderFiled = $filed;
        $this-&gt;orderDesc = $desc;
        return $this;

    }
    public function find(){
        $sql= $this-&gt;selectgetDataMatch();
        $recordset = $this-&gt;dbh-&gt;query($sql);
        if(!is_bool($recordset)) {
            $recordset = $recordset-&gt;fetch();
        }
        return $recordset;
    }
    public function selectgetDataMatch(){

        $sql = "select * from ".$this-&gt;table;
        if(!empty($this-&gt;where)){
            $sql.=" where ".$this-&gt;where;
        }


        if(!empty($this-&gt;orderFiled)){
            $sql.=' orderBy '.$this-&gt;orderFiled.' '.$this-&gt;orderDesc;
        }
        return $sql;
    }
    /**
     * 防止克隆
     *
     */
//    private function __clone()
//    {
//    }

    /**
     * Singleton instance
     *
     * @return Object
     */
public static function getInstance($dbHost='', $dbUser ='', $dbPasswd  ='', $dbName ='', $dbCharset='utf8')
{
    if (self::$_instance === null) {
        self::$_instance = new self($dbHost, $dbUser, $dbPasswd, $dbName, $dbCharset);
    }
    return self::$_instance;
}

    /**
     * Query 查询
     *
     * @param String $strSql SQL语句
     * @param String $queryMode 查询方式(All or Row)
     * @param Boolean $debug
     * @return Array
     */
    public function query($strSql, $queryMode = 'All', $debug = false)
    {
        if ($debug === true) $this-&gt;debug($strSql);
        $recordset = $this-&gt;dbh-&gt;query($strSql);
        $this-&gt;getPDOError();
        if ($recordset) {
            $recordset-&gt;setFetchMode(\PDO::FETCH_ASSOC);
            if ($queryMode == 'All') {
                $result = $recordset-&gt;fetchAll();
            } elseif ($queryMode == 'Row') {
                $result = $recordset-&gt;fetch();
            }
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Update 更新
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param String $where 条件
     * @param Boolean $debug
     * @return Int
     */
    public function update($arrayDataValue,$debug = false)
    {
        $table = $this-&gt;table;
        $where = $this-&gt;where;
        $this-&gt;checkFields($table, $arrayDataValue);
        if ($where) {
            $strSql = '';
            foreach ($arrayDataValue as $key =&gt; $value) {
                $strSql .= ", `$key`='$value'";
            }
            $strSql = substr($strSql, 1);
            $strSql = "UPDATE `$table` SET $strSql WHERE $where";
        } else {
            $strSql = "REPLACE INTO `$table` (`" . implode('`,`', array_keys($arrayDataValue)) . "`) VALUES ('" . implode("','", $arrayDataValue) . "')";
        }
        if ($debug === true) $this-&gt;debug($strSql);
        $result = $this-&gt;dbh-&gt;exec($strSql);
        $this-&gt;getPDOError();
        return $result;
    }

    /**
     * Insert 插入
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param Boolean $debug
     * @return Int
     */
    public function insert( $arrayDataValue, $debug = false)
    {
        $table = $this-&gt;table;
        $this-&gt;checkFields($table, $arrayDataValue);
        $strSql = "INSERT INTO `$table` (`" . implode('`,`', array_keys($arrayDataValue)) . "`) VALUES ('" . implode("','", $arrayDataValue) . "')";
        if ($debug === true) $this-&gt;debug($strSql);
        $result = $this-&gt;dbh-&gt;exec($strSql);
        if($result ==1){
            $result =$this-&gt;dbh-&gt;lastInsertId();
        }
        $this-&gt;getPDOError();
        return $result;
    }

    /**
     * Replace 覆盖方式插入
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param Boolean $debug
     * @return Int
     */
    public function replace($table, $arrayDataValue, $debug = false)
    {
        $this-&gt;checkFields($table, $arrayDataValue);
        $strSql = "REPLACE INTO `$table`(`" . implode('`,`', array_keys($arrayDataValue)) . "`) VALUES ('" . implode("','", $arrayDataValue) . "')";
        if ($debug === true) $this-&gt;debug($strSql);
        $result = $this-&gt;dbh-&gt;exec($strSql);
        $this-&gt;getPDOError();
        return $result;
    }

    /**
     * Delete 删除
     *
     * @param String $table 表名
     * @param String $where 条件
     * @param Boolean $debug
     * @return Int
     */
    public function delete($table, $debug = false)
    {
            $where = $this-&gt;where;
            $strSql = "DELETE FROM `$table` WHERE $where";
            if ($debug === true) $this-&gt;debug($strSql);
            $result = $this-&gt;dbh-&gt;exec($strSql);
            $this-&gt;getPDOError();
            return $result;

    }

    /**
     * execSql 执行SQL语句,debug=&gt;true可打印sql调试
     *
     * @param String $strSql
     * @param Boolean $debug
     * @return Int
     */
    public function execSql($strSql, $debug = false)
    {
        if ($debug === true) $this-&gt;debug($strSql);
        $result = $this-&gt;dbh-&gt;exec($strSql);
        $this-&gt;getPDOError();
        return $result;
    }

    /**
     * 获取字段最大值
     *
     * @param string $table 表名
     * @param string $field_name 字段名
     * @param string $where 条件
     */
    public function getMaxValue($table, $field_name, $where = '', $debug = false)
    {
        $strSql = "SELECT MAX(" . $field_name . ") AS MAX_VALUE FROM $table";
        if ($where != '') $strSql .= " WHERE $where";
        if ($debug === true) $this-&gt;debug($strSql);
        $arrTemp = $this-&gt;query($strSql, 'Row');
        $maxValue = $arrTemp["MAX_VALUE"];
        if ($maxValue == "" || $maxValue == null) {
            $maxValue = 0;
        }
        return $maxValue;
    }

    /**
     * 获取指定列的数量
     *
     * @param string $table
     * @param string $field_name
     * @param string $where
     * @param bool $debug
     * @return int
     */
    public function getCount($table, $field_name, $where = '', $debug = false)
    {
        $strSql = "SELECT COUNT($field_name) AS NUM FROM $table";
        if ($where != '') $strSql .= " WHERE $where";
        if ($debug === true) $this-&gt;debug($strSql);
        $arrTemp = $this-&gt;query($strSql, 'Row');
        return $arrTemp['NUM'];
    }

    /**
     * 获取表引擎
     *
     * @param String $dbName 库名
     * @param String $tableName 表名
     * @param Boolean $debug
     * @return String
     */
    public function getTableEngine($dbName, $tableName)
    {
        $strSql = "SHOW TABLE STATUS FROM $dbName WHERE Name='" . $tableName . "'";
        $arrayTableInfo = $this-&gt;query($strSql);
        $this-&gt;getPDOError();
        return $arrayTableInfo[0]['Engine'];
    }

    //预处理执行
    public function prepareSql($sql = '')
    {
        return $this-&gt;dbh-&gt;prepare($sql);
    }

    //执行预处理
    public function execute($presql)
    {
        return $this-&gt;dbh-&gt;execute($presql);
    }

    /**
     * pdo属性设置
     */
    public function setAttribute($p, $d)
    {
        $this-&gt;dbh-&gt;setAttribute($p, $d);
    }

    /**
     * beginTransaction 事务开始
     */
    public function beginTransaction()
    {
        $this-&gt;dbh-&gt;beginTransaction();
    }

    /**
     * commit 事务提交
     */
    public function commit()
    {
        $this-&gt;dbh-&gt;commit();
    }

    /**
     * rollback 事务回滚
     */
    public function rollback()
    {
        $this-&gt;dbh-&gt;rollback();
    }

    /**
     * transaction 通过事务处理多条SQL语句
     * 调用前需通过getTableEngine判断表引擎是否支持事务
     *
     * @param array $arraySql
     * @return Boolean
     */
    public function execTransaction($arraySql)
    {
        $retval = 1;
        $this-&gt;beginTransaction();
        foreach ($arraySql as $strSql) {
            if ($this-&gt;execSql($strSql) == 0) $retval = 0;
        }
        if ($retval == 0) {
            $this-&gt;rollback();
            return false;
        } else {
            $this-&gt;commit();
            return true;
        }
    }

    /**
     * checkFields 检查指定字段是否在指定数据表中存在
     *
     * @param String $table
     * @param array $arrayField
     */
    private function checkFields($table, $arrayFields)
    {
        $fields = $this-&gt;getFields($table);
        foreach ($arrayFields as $key =&gt; $value) {
            if (!in_array($key, $fields)) {
                $this-&gt;outputError("Unknown column `$key` in field list.");
            }
        }
    }

    /**
     * getFields 获取指定数据表中的全部字段名
     *
     * @param String $table 表名
     * @return array
     */
    private function getFields($table)
    {
        $fields = array();
        $recordset = $this-&gt;dbh-&gt;query("SHOW COLUMNS FROM $table");
        $this-&gt;getPDOError();
        $recordset-&gt;setFetchMode(\PDO::FETCH_ASSOC);
        $result = $recordset-&gt;fetchAll();
        foreach ($result as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }

    /**
     * getPDOError 捕获PDO错误信息
     */
    private function getPDOError()
    {
        if ($this-&gt;dbh-&gt;errorCode() != '00000') {
            $arrayError = $this-&gt;dbh-&gt;errorInfo();
            $this-&gt;outputError($arrayError[2]);
        }
    }

    /**
     * debug
     *
     * @param mixed $debuginfo
     */
    private function debug($debuginfo)
    {
        var_dump($debuginfo);
        exit();
    }

    /**
     * 输出错误信息
     *
     * @param String $strErrMsg
     */
    private function outputError($strErrMsg)
    {
        throw new \Exception('MySQL Error: ' . $strErrMsg);
    }

    /**
     * destruct 关闭数据库连接
     */
    public function destruct()
    {
        $this-&gt;dbh = null;
    }

    /**
     *PDO执行sql语句,返回改变的条数
     *如需调试可选用execSql($sql,true)
     */
    public function exec($sql = '')
    {
        return $this-&gt;dbh-&gt;exec($sql);
    }
}

<p>部分代码参考互联网，廖圣平博客整理</p>
https://blog.csdn.net/qq_22823581/article/details/84426138

            </div>
