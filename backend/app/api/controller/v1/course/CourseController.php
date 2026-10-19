<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 移动端 API
// +----------------------------------------------------------------------
namespace app\api\controller\v1\course;

use app\Request;
use app\services\course\CourseServices;
use app\services\course\TeacherServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 课程类
 * Class CourseController
 * @package app\api\controller\v1\course
 */
class CourseController
{
    /**
     * @var CourseServices
     */
    protected $services;

    public function __construct(CourseServices $services)
    {
        $this->services = $services;
    }

    /**
     * 课程列表（公开）
     * @param Request $request
     * @return mixed
     */
    public function list(Request $request)
    {
        $where = $request->getMore([
            ['keyword', '', '', 'title'],
            [['teacher_id', 'd'], 0],
        ]);
        $list = $this->services->getCourseList($where);
        return app('json')->success($list);
    }

    /**
     * 课程详情（公开）：含章节课时，试看课时才返回 video_url
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function detail(Request $request, $id)
    {
        $uid = (int)$request->uid();
        $detail = $this->services->getCourseDetail((int)$id, $uid);
        if (!$detail) {
            return app('json')->fail('课程不存在或已下架');
        }
        return app('json')->success($detail);
    }

    /**
     * 单节课时播放地址（需登录：已购买或试看）
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function lesson(Request $request, $id)
    {
        $uid = (int)$request->uid();
        if (!$uid) {
            return app('json')->fail('请先登录');
        }
        $lesson = $this->services->getLessonPlay((int)$id, $uid);
        if (!$lesson) {
            return app('json')->fail('无权观看，请先购买课程');
        }
        return app('json')->success($lesson);
    }

    /**
     * 我的课程（需登录）：已购课程+学习进度
     * @param Request $request
     * @return mixed
     */
    public function my(Request $request)
    {
        $uid = (int)$request->uid();
        if (!$uid) {
            return app('json')->fail('请先登录');
        }
        $list = $this->services->getMyCourses($uid);
        return app('json')->success($list);
    }

    /**
     * 保存学习进度（需登录）
     * @param Request $request
     * @return mixed
     */
    public function progress(Request $request)
    {
        $uid = (int)$request->uid();
        if (!$uid) {
            return app('json')->fail('请先登录');
        }
        $data = $request->postMore([
            [['lesson_id', 'd'], 0],
            [['progress', 'd'], 0],
            [['finished', 'd'], 0],
        ]);
        if (!$data['lesson_id']) {
            return app('json')->fail('参数错误');
        }
        $ok = $this->services->saveProgress($uid, $data['lesson_id'], $data['progress'], $data['finished']);
        if (!$ok) {
            return app('json')->fail('保存失败');
        }
        return app('json')->success('保存成功');
    }

    /**
     * 老师列表（公开）
     * @param TeacherServices $services
     * @return mixed
     */
    public function teacherList(TeacherServices $services)
    {
        $list = $services->getTeacherList();
        return app('json')->success($list);
    }

    /**
     * 老师详情（公开）
     * @param TeacherServices $services
     * @param $id
     * @return mixed
     */
    public function teacherDetail(TeacherServices $services, $id)
    {
        $detail = $services->getTeacherDetail((int)$id);
        if (!$detail) {
            return app('json')->fail('老师不存在');
        }
        return app('json')->success($detail);
    }
}
