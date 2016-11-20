<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    private $collectionDb;//学生收藏的数据库
    private $libDb;//图书馆数据库

    public function index(){

        $stuId = "2015210355";
        $content = "java";
        
        $res = $this->bookSearch($content);

        dump(json_decode($res),True);
    
       
    }
    //收藏

    /**
     * @param $stuId 学生的学号
     *
     * @return $res  array 该同学的收藏书籍
     *
     * 遍历数据库 查找该学生收藏的所有书籍
     *
     */
    public function myCollection($stuId) {

        $data = array(
            'stuId' => $stuId,
        );

        $res = $this->collectionDb->where($data)->select();

        return $res;
    }


    /**
     * @param $stuId 学生的学号
     *
     * @param $bookId 书籍的编号
     *
     * @return flag flag为true表示该书已经被收藏 为false表示该书未被该学生收藏
     *
     * 根据书本的编号判断是否被该学生收藏  已经收藏返回true 未被收藏返回false
     */

    public function isCollected($stuId,$bookId) {

        $data = array(
            'stuId' => $stuId,
            'bookId' => $bookId,
        );
        $res = $this->collectionDb->where($data)->find();
        if($res) {
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    /**
     * @param $stuId 学生的学号
     *
     * @param $bookId 书籍的编号
     *
     * 将书籍与学生添加进数据库 表示该学生要收藏该书
     *
     */
    public function addToCollection($stuId,$bookId) {

        $data = array(
            'stuId' => $stuId,
            'bookId' => $bookId,
        );
        $this->collectionDb->add($data);

    }

    /**
     * @param $stuId 学生的学号
     *
     * @param $bookId 书籍的编号
     *
     * 取消收藏功能
     *
     */
    public function deleteFromCollection($stuId,$bookId) {

        $data = array(
            'stuId' => $stuId,
            'bookId' => $bookId,
        );
        $this->collectionDb->where($data)->delete();

    }


    //新书
    /**
     *
     * @return $data 最近2个月内上架的书的信息  ==应该做不出来
     *
     */
    public function newBook() {}



    //搜索

    /**
     * @param $input 用户输入的搜索条件 
     *
     * @return $data 根据书名和作者搜所到的书籍
     */
    public function bookSearch($content) {

        $begin = 0;
        $timestamp = time();
        $string = "library";
        $secret = sha1(sha1($timestamp).md5($string)."redrock");

        $url = "http://hongyan.cqupt.edu.cn/BookApi/index.php?s=/Home/Index/bookSearch"; 

        $post_data = array(
            'begin' => $begin,
            'content' => $content,
            'string' => $string,
            'timestamp' => $timestamp,
            'secret' => $secret,
        );
        $data = $this->curl_post($url,$post_data);

        return $data;
}


    //个人信息

    /**
     * @param $stuId 学生的学号
     *
     * @return $data 正被学生借阅的书籍
     *
     */

    public function borrowedBook($stuId) {

        $timestamp = time();
        $string = "library";
        $secret = sha1(sha1($timestamp).md5($string)."redrock");

        $url = "http://hongyan.cqupt.edu.cn/BookApi/index.php?s=/Home/Index/borrow"; 

        $post_data = array(
            'readerId' => $stuId,
            'string' => $string,
            'timestamp' => $timestamp,
            'secret' => $secret,
        );
        $data = $this->curl_post($url,$post_data);

        return $data;
    }

    /**
     * @param $stuId 学生的学号
     *
     * @return $data 历史借阅书籍
     *
     */

    public function historyBorrowedBook($stuId) {

 
        $timestamp = time();
        $string = "library";
        $secret = sha1(sha1($timestamp).md5($string)."redrock");

        $url = "http://hongyan.cqupt.edu.cn/BookApi/index.php?s=/Home/Index/libHistoryBook"; 

        $post_data = array(
            'readerId' => $stuId,
            'string' => $string,
            'timestamp' => $timestamp,
            'secret' => $secret,
        );
        $data = $this->curl_post($url,$post_data);

        return $data;

    }

    /**
     * @param $stuId 学生的学号
     *
     * @return $data 欠费书籍,金额
     */   
    public function owedBook($stuId) {
        $timestamp = time();
        $string = "library";
        $secret = sha1(sha1($timestamp).md5($string)."redrock");

        $url = "http://hongyan.cqupt.edu.cn/BookApi/index.php?s=/Home/Index/libOwedBook"; 

        $post_data = array(
            'readerId' => $stuId,
            'string' => $string,
            'timestamp' => $timestamp,
            'secret' => $secret,
        );
        $data = $this->curl_post($url,$post_data);

        return $data;
    }

    /**
     * @param $stuId 学生的学号
     *
     * @return $data 学生基本信息
     */   
    public function readerInfo($stuId) {

        $timestamp = time();
        $string = "library";
        $secret = sha1(sha1($timestamp).md5($string)."redrock");

        $url = "http://hongyan.cqupt.edu.cn/BookApi/index.php?s=/Home/Index/readerInfo"; 

        $post_data = array(
            'readerId' => $stuId,
            'string' => $string,
            'timestamp' => $timestamp,
            'secret' => $secret,
        );
        $data = $this->curl_post($url,$post_data);

        return $data;
    }


    //其他的辅助函数。。。

    /**
     * @param $bookId 书籍的编号tstm
     *
     * @return $bookInfo 书籍的信息
     *
     * 根据书的编号得到书的信息
     */
    private function getBookInfoByBookId($bookId) {}


    /**
     * @param $url 调用的接口的url
     *
     * @param $post_data 需要post去的值
     *
     * @return $data 得到的json形式的返回值
     */
    private function curl_post($url,$post_data) {

        $curl = curl_init();  
        //设置提交的url  
        curl_setopt($curl, CURLOPT_URL, $url);  
        //设置头文件的信息作为数据流输出  
        curl_setopt($curl, CURLOPT_HEADER, 0);  
        //设置获取的信息以文件流的形式返回，而不是直接输出。  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        //设置post方式提交  
        curl_setopt($curl, CURLOPT_POST, 1);  
        //设置post数据  
          
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);  
        //执行命令  
        $data = curl_exec($curl);  
        //关闭URL请求  
        curl_close($curl);  
        //获得数据并返回  
        return $data;
    }

    /**
     * 连接一些基本的数据库
     *  
     *
     */
    private function conn() {
        $this->collectionDb = M('collection','','mysql://root:@127.0.0.1:3306/library');
    }
}