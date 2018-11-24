<div id="content_views" class="markdown_views prism-atom-one-dark">
							<!-- flowchart 箭头图标 勿删 -->
							<svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><path stroke-linecap="round" d="M5,0 0,2.5 5,5z" id="raphael-marker-block" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path></svg>
							<h2><a name="t0"></a><a id="_0" target="_blank"></a>使用方法</h2>
<p><a href="https://github.com/liaoshengping/PDO" rel="nofollow" target="_blank">项目仓库</a><br>
1.namespace 改成自己项目中的<br>
2.new  这个PDO 类，注意是你命名空间的PDO（你也可以把这个类名改掉）</p>
<p>连接数据库</p>
<pre class="prettyprint"><code class="has-numbering">$PDO = PDO::getInstance($dbHost='', $dbUser ='', $dbPasswd  ='', $dbName ='', $dbCharset='');
</code><ul class="pre-numbering" style=""></ul></pre>
<h2><a name="t1"></a><a id="_10" target="_blank"></a>新增</h2>
<p>eg:  users信息</p>
<pre class="prettyprint"><code class="has-numbering">$PDO-&gt;table('users')-&gt;insert(['name'=&gt;'liaosp']);
</code><ul class="pre-numbering" style=""></ul></pre>
<h2><a name="t2"></a><a id="_18" target="_blank"></a>获取</h2>
<p>查询一条：</p>
<pre class="prettyprint"><code class="has-numbering">$data =$PDO-&gt;table('oauth_clients')-&gt;where("client_id != 'admin'")-&gt;find();
</code><ul class="pre-numbering" style=""></ul></pre>
<p>获取多条</p>
<pre class="prettyprint"><code class="has-numbering">$data =$PDO-&gt;table('oauth_clients')-&gt;where("client_id != 'admin'")-&gt;get();
</code><ul class="pre-numbering" style=""></ul></pre>
<h2><a name="t3"></a><a id="_31" target="_blank"></a>更新</h2>
<pre class="prettyprint"><code class="has-numbering">$data =$PDO-&gt;table('oauth_clients')-&gt;where("id = 2")-&gt;update(['admin'=&gt;'liaosp']);
</code><ul class="pre-numbering" style=""></ul></pre>
<p><a href="https://github.com/liaoshengping/PDO" rel="nofollow" target="_blank">项目仓库</a></p>

<p>部分代码参考互联网，廖圣平博客整理</p>
https://blog.csdn.net/qq_22823581/article/details/84426138

