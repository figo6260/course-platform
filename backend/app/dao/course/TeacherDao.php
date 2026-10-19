<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块
// +----------------------------------------------------------------------
namespace app\dao\course;

use app\dao\BaseDao;
use app\model\course\Teacher;

/**
 * Class TeacherDao
 * @package app\dao\course
 */
class TeacherDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return Teacher::class;
    }

    /**
     * 条件搜索列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param array $field
     * @return mixed
     */
    public function getSearchList(array $where, int $page = 0, int $limit = 0, array $field = ['*'])
    {
        return $this->search($where)->order('sort desc,id desc')
            ->field($field)
            ->when($page > 0 || $limit > 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })
            ->select();
    }
}
