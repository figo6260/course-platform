<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块
// +----------------------------------------------------------------------
namespace app\services\course;

use app\dao\course\TeacherDao;
use app\services\BaseServices;

/**
 * 老师服务
 * Class TeacherServices
 * @package app\services\course
 */
class TeacherServices extends BaseServices
{
    /**
     * @var TeacherDao
     */
    protected $dao;

    public function __construct(TeacherDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 老师列表（公开）
     * @param array $where
     * @return array
     */
    public function getTeacherList(array $where = [])
    {
        $where['status'] = 1;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getSearchList($where, $page, $limit, ['*']);
        return $list;
    }

    /**
     * 老师详情（公开）
     * @param int $id
     * @return array
     */
    public function getTeacherDetail(int $id)
    {
        $teacher = $this->dao->getOne(['id' => $id, 'status' => 1]);
        if (!$teacher) {
            return [];
        }
        return $teacher->toArray();
    }
}
