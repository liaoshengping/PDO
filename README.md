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
</code><ul class="pre-numbering" style=""><li style="color: rgb(153, 153, 153);">1</li><li style="color: rgb(153, 153, 153);">2</li><li style="color: rgb(153, 153, 153);">3</li><li style="color: rgb(153, 153, 153);">4</li><li style="color: rgb(153, 153, 153);">5</li><li style="color: rgb(153, 153, 153);">6</li><li style="color: rgb(153, 153, 153);">7</li><li style="color: rgb(153, 153, 153);">8</li><li style="color: rgb(153, 153, 153);">9</li><li style="color: rgb(153, 153, 153);">10</li><li style="color: rgb(153, 153, 153);">11</li><li style="color: rgb(153, 153, 153);">12</li><li style="color: rgb(153, 153, 153);">13</li><li style="color: rgb(153, 153, 153);">14</li><li style="color: rgb(153, 153, 153);">15</li><li style="color: rgb(153, 153, 153);">16</li><li style="color: rgb(153, 153, 153);">17</li><li style="color: rgb(153, 153, 153);">18</li><li style="color: rgb(153, 153, 153);">19</li><li style="color: rgb(153, 153, 153);">20</li><li style="color: rgb(153, 153, 153);">21</li><li style="color: rgb(153, 153, 153);">22</li><li style="color: rgb(153, 153, 153);">23</li><li style="color: rgb(153, 153, 153);">24</li><li style="color: rgb(153, 153, 153);">25</li><li style="color: rgb(153, 153, 153);">26</li><li style="color: rgb(153, 153, 153);">27</li><li style="color: rgb(153, 153, 153);">28</li><li style="color: rgb(153, 153, 153);">29</li><li style="color: rgb(153, 153, 153);">30</li><li style="color: rgb(153, 153, 153);">31</li><li style="color: rgb(153, 153, 153);">32</li><li style="color: rgb(153, 153, 153);">33</li><li style="color: rgb(153, 153, 153);">34</li><li style="color: rgb(153, 153, 153);">35</li><li style="color: rgb(153, 153, 153);">36</li><li style="color: rgb(153, 153, 153);">37</li><li style="color: rgb(153, 153, 153);">38</li><li style="color: rgb(153, 153, 153);">39</li><li style="color: rgb(153, 153, 153);">40</li><li style="color: rgb(153, 153, 153);">41</li><li style="color: rgb(153, 153, 153);">42</li><li style="color: rgb(153, 153, 153);">43</li><li style="color: rgb(153, 153, 153);">44</li><li style="color: rgb(153, 153, 153);">45</li><li style="color: rgb(153, 153, 153);">46</li><li style="color: rgb(153, 153, 153);">47</li><li style="color: rgb(153, 153, 153);">48</li><li style="color: rgb(153, 153, 153);">49</li><li style="color: rgb(153, 153, 153);">50</li><li style="color: rgb(153, 153, 153);">51</li><li style="color: rgb(153, 153, 153);">52</li><li style="color: rgb(153, 153, 153);">53</li><li style="color: rgb(153, 153, 153);">54</li><li style="color: rgb(153, 153, 153);">55</li><li style="color: rgb(153, 153, 153);">56</li><li style="color: rgb(153, 153, 153);">57</li><li style="color: rgb(153, 153, 153);">58</li><li style="color: rgb(153, 153, 153);">59</li><li style="color: rgb(153, 153, 153);">60</li><li style="color: rgb(153, 153, 153);">61</li><li style="color: rgb(153, 153, 153);">62</li><li style="color: rgb(153, 153, 153);">63</li><li style="color: rgb(153, 153, 153);">64</li><li style="color: rgb(153, 153, 153);">65</li><li style="color: rgb(153, 153, 153);">66</li><li style="color: rgb(153, 153, 153);">67</li><li style="color: rgb(153, 153, 153);">68</li><li style="color: rgb(153, 153, 153);">69</li><li style="color: rgb(153, 153, 153);">70</li><li style="color: rgb(153, 153, 153);">71</li><li style="color: rgb(153, 153, 153);">72</li><li style="color: rgb(153, 153, 153);">73</li><li style="color: rgb(153, 153, 153);">74</li><li style="color: rgb(153, 153, 153);">75</li><li style="color: rgb(153, 153, 153);">76</li><li style="color: rgb(153, 153, 153);">77</li><li style="color: rgb(153, 153, 153);">78</li><li style="color: rgb(153, 153, 153);">79</li><li style="color: rgb(153, 153, 153);">80</li><li style="color: rgb(153, 153, 153);">81</li><li style="color: rgb(153, 153, 153);">82</li><li style="color: rgb(153, 153, 153);">83</li><li style="color: rgb(153, 153, 153);">84</li><li style="color: rgb(153, 153, 153);">85</li><li style="color: rgb(153, 153, 153);">86</li><li style="color: rgb(153, 153, 153);">87</li><li style="color: rgb(153, 153, 153);">88</li><li style="color: rgb(153, 153, 153);">89</li><li style="color: rgb(153, 153, 153);">90</li><li style="color: rgb(153, 153, 153);">91</li><li style="color: rgb(153, 153, 153);">92</li><li style="color: rgb(153, 153, 153);">93</li><li style="color: rgb(153, 153, 153);">94</li><li style="color: rgb(153, 153, 153);">95</li><li style="color: rgb(153, 153, 153);">96</li><li style="color: rgb(153, 153, 153);">97</li><li style="color: rgb(153, 153, 153);">98</li><li style="color: rgb(153, 153, 153);">99</li><li style="color: rgb(153, 153, 153);">100</li><li style="color: rgb(153, 153, 153);">101</li><li style="color: rgb(153, 153, 153);">102</li><li style="color: rgb(153, 153, 153);">103</li><li style="color: rgb(153, 153, 153);">104</li><li style="color: rgb(153, 153, 153);">105</li><li style="color: rgb(153, 153, 153);">106</li><li style="color: rgb(153, 153, 153);">107</li><li style="color: rgb(153, 153, 153);">108</li><li style="color: rgb(153, 153, 153);">109</li><li style="color: rgb(153, 153, 153);">110</li><li style="color: rgb(153, 153, 153);">111</li><li style="color: rgb(153, 153, 153);">112</li><li style="color: rgb(153, 153, 153);">113</li><li style="color: rgb(153, 153, 153);">114</li><li style="color: rgb(153, 153, 153);">115</li><li style="color: rgb(153, 153, 153);">116</li><li style="color: rgb(153, 153, 153);">117</li><li style="color: rgb(153, 153, 153);">118</li><li style="color: rgb(153, 153, 153);">119</li><li style="color: rgb(153, 153, 153);">120</li><li style="color: rgb(153, 153, 153);">121</li><li style="color: rgb(153, 153, 153);">122</li><li style="color: rgb(153, 153, 153);">123</li><li style="color: rgb(153, 153, 153);">124</li><li style="color: rgb(153, 153, 153);">125</li><li style="color: rgb(153, 153, 153);">126</li><li style="color: rgb(153, 153, 153);">127</li><li style="color: rgb(153, 153, 153);">128</li><li style="color: rgb(153, 153, 153);">129</li><li style="color: rgb(153, 153, 153);">130</li><li style="color: rgb(153, 153, 153);">131</li><li style="color: rgb(153, 153, 153);">132</li><li style="color: rgb(153, 153, 153);">133</li><li style="color: rgb(153, 153, 153);">134</li><li style="color: rgb(153, 153, 153);">135</li><li style="color: rgb(153, 153, 153);">136</li><li style="color: rgb(153, 153, 153);">137</li><li style="color: rgb(153, 153, 153);">138</li><li style="color: rgb(153, 153, 153);">139</li><li style="color: rgb(153, 153, 153);">140</li><li style="color: rgb(153, 153, 153);">141</li><li style="color: rgb(153, 153, 153);">142</li><li style="color: rgb(153, 153, 153);">143</li><li style="color: rgb(153, 153, 153);">144</li><li style="color: rgb(153, 153, 153);">145</li><li style="color: rgb(153, 153, 153);">146</li><li style="color: rgb(153, 153, 153);">147</li><li style="color: rgb(153, 153, 153);">148</li><li style="color: rgb(153, 153, 153);">149</li><li style="color: rgb(153, 153, 153);">150</li><li style="color: rgb(153, 153, 153);">151</li><li style="color: rgb(153, 153, 153);">152</li><li style="color: rgb(153, 153, 153);">153</li><li style="color: rgb(153, 153, 153);">154</li><li style="color: rgb(153, 153, 153);">155</li><li style="color: rgb(153, 153, 153);">156</li><li style="color: rgb(153, 153, 153);">157</li><li style="color: rgb(153, 153, 153);">158</li><li style="color: rgb(153, 153, 153);">159</li><li style="color: rgb(153, 153, 153);">160</li><li style="color: rgb(153, 153, 153);">161</li><li style="color: rgb(153, 153, 153);">162</li><li style="color: rgb(153, 153, 153);">163</li><li style="color: rgb(153, 153, 153);">164</li><li style="color: rgb(153, 153, 153);">165</li><li style="color: rgb(153, 153, 153);">166</li><li style="color: rgb(153, 153, 153);">167</li><li style="color: rgb(153, 153, 153);">168</li><li style="color: rgb(153, 153, 153);">169</li><li style="color: rgb(153, 153, 153);">170</li><li style="color: rgb(153, 153, 153);">171</li><li style="color: rgb(153, 153, 153);">172</li><li style="color: rgb(153, 153, 153);">173</li><li style="color: rgb(153, 153, 153);">174</li><li style="color: rgb(153, 153, 153);">175</li><li style="color: rgb(153, 153, 153);">176</li><li style="color: rgb(153, 153, 153);">177</li><li style="color: rgb(153, 153, 153);">178</li><li style="color: rgb(153, 153, 153);">179</li><li style="color: rgb(153, 153, 153);">180</li><li style="color: rgb(153, 153, 153);">181</li><li style="color: rgb(153, 153, 153);">182</li><li style="color: rgb(153, 153, 153);">183</li><li style="color: rgb(153, 153, 153);">184</li><li style="color: rgb(153, 153, 153);">185</li><li style="color: rgb(153, 153, 153);">186</li><li style="color: rgb(153, 153, 153);">187</li><li style="color: rgb(153, 153, 153);">188</li><li style="color: rgb(153, 153, 153);">189</li><li style="color: rgb(153, 153, 153);">190</li><li style="color: rgb(153, 153, 153);">191</li><li style="color: rgb(153, 153, 153);">192</li><li style="color: rgb(153, 153, 153);">193</li><li style="color: rgb(153, 153, 153);">194</li><li style="color: rgb(153, 153, 153);">195</li><li style="color: rgb(153, 153, 153);">196</li><li style="color: rgb(153, 153, 153);">197</li><li style="color: rgb(153, 153, 153);">198</li><li style="color: rgb(153, 153, 153);">199</li><li style="color: rgb(153, 153, 153);">200</li><li style="color: rgb(153, 153, 153);">201</li><li style="color: rgb(153, 153, 153);">202</li><li style="color: rgb(153, 153, 153);">203</li><li style="color: rgb(153, 153, 153);">204</li><li style="color: rgb(153, 153, 153);">205</li><li style="color: rgb(153, 153, 153);">206</li><li style="color: rgb(153, 153, 153);">207</li><li style="color: rgb(153, 153, 153);">208</li><li style="color: rgb(153, 153, 153);">209</li><li style="color: rgb(153, 153, 153);">210</li><li style="color: rgb(153, 153, 153);">211</li><li style="color: rgb(153, 153, 153);">212</li><li style="color: rgb(153, 153, 153);">213</li><li style="color: rgb(153, 153, 153);">214</li><li style="color: rgb(153, 153, 153);">215</li><li style="color: rgb(153, 153, 153);">216</li><li style="color: rgb(153, 153, 153);">217</li><li style="color: rgb(153, 153, 153);">218</li><li style="color: rgb(153, 153, 153);">219</li><li style="color: rgb(153, 153, 153);">220</li><li style="color: rgb(153, 153, 153);">221</li><li style="color: rgb(153, 153, 153);">222</li><li style="color: rgb(153, 153, 153);">223</li><li style="color: rgb(153, 153, 153);">224</li><li style="color: rgb(153, 153, 153);">225</li><li style="color: rgb(153, 153, 153);">226</li><li style="color: rgb(153, 153, 153);">227</li><li style="color: rgb(153, 153, 153);">228</li><li style="color: rgb(153, 153, 153);">229</li><li style="color: rgb(153, 153, 153);">230</li><li style="color: rgb(153, 153, 153);">231</li><li style="color: rgb(153, 153, 153);">232</li><li style="color: rgb(153, 153, 153);">233</li><li style="color: rgb(153, 153, 153);">234</li><li style="color: rgb(153, 153, 153);">235</li><li style="color: rgb(153, 153, 153);">236</li><li style="color: rgb(153, 153, 153);">237</li><li style="color: rgb(153, 153, 153);">238</li><li style="color: rgb(153, 153, 153);">239</li><li style="color: rgb(153, 153, 153);">240</li><li style="color: rgb(153, 153, 153);">241</li><li style="color: rgb(153, 153, 153);">242</li><li style="color: rgb(153, 153, 153);">243</li><li style="color: rgb(153, 153, 153);">244</li><li style="color: rgb(153, 153, 153);">245</li><li style="color: rgb(153, 153, 153);">246</li><li style="color: rgb(153, 153, 153);">247</li><li style="color: rgb(153, 153, 153);">248</li><li style="color: rgb(153, 153, 153);">249</li><li style="color: rgb(153, 153, 153);">250</li><li style="color: rgb(153, 153, 153);">251</li><li style="color: rgb(153, 153, 153);">252</li><li style="color: rgb(153, 153, 153);">253</li><li style="color: rgb(153, 153, 153);">254</li><li style="color: rgb(153, 153, 153);">255</li><li style="color: rgb(153, 153, 153);">256</li><li style="color: rgb(153, 153, 153);">257</li><li style="color: rgb(153, 153, 153);">258</li><li style="color: rgb(153, 153, 153);">259</li><li style="color: rgb(153, 153, 153);">260</li><li style="color: rgb(153, 153, 153);">261</li><li style="color: rgb(153, 153, 153);">262</li><li style="color: rgb(153, 153, 153);">263</li><li style="color: rgb(153, 153, 153);">264</li><li style="color: rgb(153, 153, 153);">265</li><li style="color: rgb(153, 153, 153);">266</li><li style="color: rgb(153, 153, 153);">267</li><li style="color: rgb(153, 153, 153);">268</li><li style="color: rgb(153, 153, 153);">269</li><li style="color: rgb(153, 153, 153);">270</li><li style="color: rgb(153, 153, 153);">271</li><li style="color: rgb(153, 153, 153);">272</li><li style="color: rgb(153, 153, 153);">273</li><li style="color: rgb(153, 153, 153);">274</li><li style="color: rgb(153, 153, 153);">275</li><li style="color: rgb(153, 153, 153);">276</li><li style="color: rgb(153, 153, 153);">277</li><li style="color: rgb(153, 153, 153);">278</li><li style="color: rgb(153, 153, 153);">279</li><li style="color: rgb(153, 153, 153);">280</li><li style="color: rgb(153, 153, 153);">281</li><li style="color: rgb(153, 153, 153);">282</li><li style="color: rgb(153, 153, 153);">283</li><li style="color: rgb(153, 153, 153);">284</li><li style="color: rgb(153, 153, 153);">285</li><li style="color: rgb(153, 153, 153);">286</li><li style="color: rgb(153, 153, 153);">287</li><li style="color: rgb(153, 153, 153);">288</li><li style="color: rgb(153, 153, 153);">289</li><li style="color: rgb(153, 153, 153);">290</li><li style="color: rgb(153, 153, 153);">291</li><li style="color: rgb(153, 153, 153);">292</li><li style="color: rgb(153, 153, 153);">293</li><li style="color: rgb(153, 153, 153);">294</li><li style="color: rgb(153, 153, 153);">295</li><li style="color: rgb(153, 153, 153);">296</li><li style="color: rgb(153, 153, 153);">297</li><li style="color: rgb(153, 153, 153);">298</li><li style="color: rgb(153, 153, 153);">299</li><li style="color: rgb(153, 153, 153);">300</li><li style="color: rgb(153, 153, 153);">301</li><li style="color: rgb(153, 153, 153);">302</li><li style="color: rgb(153, 153, 153);">303</li><li style="color: rgb(153, 153, 153);">304</li><li style="color: rgb(153, 153, 153);">305</li><li style="color: rgb(153, 153, 153);">306</li><li style="color: rgb(153, 153, 153);">307</li><li style="color: rgb(153, 153, 153);">308</li><li style="color: rgb(153, 153, 153);">309</li><li style="color: rgb(153, 153, 153);">310</li><li style="color: rgb(153, 153, 153);">311</li><li style="color: rgb(153, 153, 153);">312</li><li style="color: rgb(153, 153, 153);">313</li><li style="color: rgb(153, 153, 153);">314</li><li style="color: rgb(153, 153, 153);">315</li><li style="color: rgb(153, 153, 153);">316</li><li style="color: rgb(153, 153, 153);">317</li><li style="color: rgb(153, 153, 153);">318</li><li style="color: rgb(153, 153, 153);">319</li><li style="color: rgb(153, 153, 153);">320</li><li style="color: rgb(153, 153, 153);">321</li><li style="color: rgb(153, 153, 153);">322</li><li style="color: rgb(153, 153, 153);">323</li><li style="color: rgb(153, 153, 153);">324</li><li style="color: rgb(153, 153, 153);">325</li><li style="color: rgb(153, 153, 153);">326</li><li style="color: rgb(153, 153, 153);">327</li><li style="color: rgb(153, 153, 153);">328</li><li style="color: rgb(153, 153, 153);">329</li><li style="color: rgb(153, 153, 153);">330</li><li style="color: rgb(153, 153, 153);">331</li><li style="color: rgb(153, 153, 153);">332</li><li style="color: rgb(153, 153, 153);">333</li><li style="color: rgb(153, 153, 153);">334</li><li style="color: rgb(153, 153, 153);">335</li><li style="color: rgb(153, 153, 153);">336</li><li style="color: rgb(153, 153, 153);">337</li><li style="color: rgb(153, 153, 153);">338</li><li style="color: rgb(153, 153, 153);">339</li><li style="color: rgb(153, 153, 153);">340</li><li style="color: rgb(153, 153, 153);">341</li><li style="color: rgb(153, 153, 153);">342</li><li style="color: rgb(153, 153, 153);">343</li><li style="color: rgb(153, 153, 153);">344</li><li style="color: rgb(153, 153, 153);">345</li><li style="color: rgb(153, 153, 153);">346</li><li style="color: rgb(153, 153, 153);">347</li><li style="color: rgb(153, 153, 153);">348</li><li style="color: rgb(153, 153, 153);">349</li><li style="color: rgb(153, 153, 153);">350</li><li style="color: rgb(153, 153, 153);">351</li><li style="color: rgb(153, 153, 153);">352</li><li style="color: rgb(153, 153, 153);">353</li><li style="color: rgb(153, 153, 153);">354</li><li style="color: rgb(153, 153, 153);">355</li><li style="color: rgb(153, 153, 153);">356</li><li style="color: rgb(153, 153, 153);">357</li><li style="color: rgb(153, 153, 153);">358</li><li style="color: rgb(153, 153, 153);">359</li><li style="color: rgb(153, 153, 153);">360</li><li style="color: rgb(153, 153, 153);">361</li><li style="color: rgb(153, 153, 153);">362</li><li style="color: rgb(153, 153, 153);">363</li><li style="color: rgb(153, 153, 153);">364</li><li style="color: rgb(153, 153, 153);">365</li><li style="color: rgb(153, 153, 153);">366</li><li style="color: rgb(153, 153, 153);">367</li><li style="color: rgb(153, 153, 153);">368</li><li style="color: rgb(153, 153, 153);">369</li><li style="color: rgb(153, 153, 153);">370</li><li style="color: rgb(153, 153, 153);">371</li><li style="color: rgb(153, 153, 153);">372</li><li style="color: rgb(153, 153, 153);">373</li><li style="color: rgb(153, 153, 153);">374</li><li style="color: rgb(153, 153, 153);">375</li><li style="color: rgb(153, 153, 153);">376</li><li style="color: rgb(153, 153, 153);">377</li><li style="color: rgb(153, 153, 153);">378</li><li style="color: rgb(153, 153, 153);">379</li><li style="color: rgb(153, 153, 153);">380</li><li style="color: rgb(153, 153, 153);">381</li><li style="color: rgb(153, 153, 153);">382</li><li style="color: rgb(153, 153, 153);">383</li><li style="color: rgb(153, 153, 153);">384</li><li style="color: rgb(153, 153, 153);">385</li><li style="color: rgb(153, 153, 153);">386</li><li style="color: rgb(153, 153, 153);">387</li><li style="color: rgb(153, 153, 153);">388</li><li style="color: rgb(153, 153, 153);">389</li><li style="color: rgb(153, 153, 153);">390</li><li style="color: rgb(153, 153, 153);">391</li><li style="color: rgb(153, 153, 153);">392</li><li style="color: rgb(153, 153, 153);">393</li><li style="color: rgb(153, 153, 153);">394</li><li style="color: rgb(153, 153, 153);">395</li><li style="color: rgb(153, 153, 153);">396</li><li style="color: rgb(153, 153, 153);">397</li><li style="color: rgb(153, 153, 153);">398</li><li style="color: rgb(153, 153, 153);">399</li><li style="color: rgb(153, 153, 153);">400</li><li style="color: rgb(153, 153, 153);">401</li><li style="color: rgb(153, 153, 153);">402</li><li style="color: rgb(153, 153, 153);">403</li><li style="color: rgb(153, 153, 153);">404</li><li style="color: rgb(153, 153, 153);">405</li><li style="color: rgb(153, 153, 153);">406</li><li style="color: rgb(153, 153, 153);">407</li><li style="color: rgb(153, 153, 153);">408</li><li style="color: rgb(153, 153, 153);">409</li><li style="color: rgb(153, 153, 153);">410</li><li style="color: rgb(153, 153, 153);">411</li><li style="color: rgb(153, 153, 153);">412</li><li style="color: rgb(153, 153, 153);">413</li><li style="color: rgb(153, 153, 153);">414</li><li style="color: rgb(153, 153, 153);">415</li><li style="color: rgb(153, 153, 153);">416</li><li style="color: rgb(153, 153, 153);">417</li><li style="color: rgb(153, 153, 153);">418</li><li style="color: rgb(153, 153, 153);">419</li><li style="color: rgb(153, 153, 153);">420</li><li style="color: rgb(153, 153, 153);">421</li><li style="color: rgb(153, 153, 153);">422</li><li style="color: rgb(153, 153, 153);">423</li><li style="color: rgb(153, 153, 153);">424</li><li style="color: rgb(153, 153, 153);">425</li><li style="color: rgb(153, 153, 153);">426</li><li style="color: rgb(153, 153, 153);">427</li><li style="color: rgb(153, 153, 153);">428</li><li style="color: rgb(153, 153, 153);">429</li><li style="color: rgb(153, 153, 153);">430</li><li style="color: rgb(153, 153, 153);">431</li><li style="color: rgb(153, 153, 153);">432</li><li style="color: rgb(153, 153, 153);">433</li><li style="color: rgb(153, 153, 153);">434</li><li style="color: rgb(153, 153, 153);">435</li></ul></pre>
<p>部分代码参考互联网，廖圣平博客整理</p>

            </div>
