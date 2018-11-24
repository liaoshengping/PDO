使用方法
项目仓库
1.namespace 改成自己项目中的
2.new 这个PDO 类，注意是你命名空间的PDO（你也可以把这个类名改掉）

连接数据库

$PDO = PDO::getInstance($dbHost='', $dbUser ='', $dbPasswd  ='', $dbName ='', $dbCharset='');
1
新增
eg: users信息

$PDO->table('users')->insert(['name'=>'liaosp']);
1
获取
查询一条：

$data =$PDO->table('oauth_clients')->where("client_id != 'admin'")->find();
1
获取多条

$data =$PDO->table('oauth_clients')->where("client_id != 'admin'")->get();
1
更新
$data =$PDO->table('oauth_clients')->where("id = 2")->update(['admin'=>'liaosp']);
--------------------- 
作者：廖圣平 
来源：CSDN 
原文：https://blog.csdn.net/qq_22823581/article/details/84426138 
版权声明：本文为博主原创文章，转载请附上博文链接！
