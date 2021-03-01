<?php
/*
 * @Author: your name
 * @Date: 2020-07-14 18:44:40
 * @LastEditTime: 2020-07-17 13:55:13
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \thinkphp6_Backend\app\common\exception\Page.php
 */ 
namespace kepei_helper; 


// 实在受不了tp的page了 自己写一个
// 后来发现 可以继承重写 自定义样式 
// 这个 仍然作为 非sql数据的分页
class Page
{
    public $nowPage;// 当前页码
    public $totalPage;// 总页码
    public $offset;// 一页有多少个
    public $num;// 总数
    public $data;// 数据源
    public $rawData; // 原始数据

    /**
     * @description: 初始化
     * @param array||int data 所有数据 || 总数
     * @param int offset 每页行数
     * @param int||string nowPage 当前页码
     * @return: 
     */
    public function init($data,int $offset,$nowPage = 1)
    {
        if(is_array($data)){
            $this->rawData = $data;
            $this->data = array_slice($data,($nowPage-1)*$offset,$offset); // 分页
            array_multisort ($this->data,SORT_DESC ); // 排序
            $this->num = count($data);
        }else{
            $this->num = $data;
        }
        $this->offset = $offset;
        $this->nowPage = $nowPage;
        $this->totalPage = $this->num/$this->offset;
        $this->totalPage = $this->totalPage == intval($this->totalPage)?$this->totalPage:intval($this->totalPage)+1;
        $this->prev = $this->nowPage-1 >0?$this->nowPage-1:1;
        $this->next = $this->nowPage+1 <= $this->totalPage ? $this->nowPage+1 : $this->totalPage;

        
        return $this;
    }
    
}