<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 管理端
// +----------------------------------------------------------------------
namespace app\adminapi\controller\v1\course;

use app\adminapi\controller\AuthController;
use app\dao\course\CourseDao;
use app\dao\course\CourseLessonDao;
use think\facade\App;

/**
 * 课时管理
 * Class LessonController
 * @package app\adminapi\controller\v1\course
 */
class LessonController extends AuthController
{
    /**
     * @var CourseLessonDao
     */
    protected $dao;

    public function __construct(App $app, CourseLessonDao $dao)
    {
        parent::__construct($app);
        $this->dao = $dao;
    }

    /**
     * 课时列表
     * @param $courseId
     * @return mixed
     */
    public function index($courseId)
    {
        $where = $this->request->getMore([
            [['chapter_id', 'd'], 0],
        ]);
        $where = array_filter($where, function ($v) {
            return $v !== '' && $v !== 0 && $v !== null;
        });
        $where['course_id'] = (int)$courseId;
        $list = $this->dao->getSearchList($where, 0, 0, ['*']);
        return app('json')->success($list);
    }

    /**
     * 新增课时
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            [['course_id', 'd'], 0],
            [['chapter_id', 'd'], 0],
            ['title', ''],
            ['video_id', ''],
            ['video_url', ''],
            [['duration', 'd'], 0],
            [['is_try', 'd'], 0],
            [['sort', 'd'], 0],
        ]);
        if (!$data['course_id'] || !$data['title']) {
            return app('json')->fail('参数错误');
        }
        $data['add_time'] = time();
        $id = $this->dao->save($data);
        // 更新课程课时数
        if ($id) {
            $count = $this->dao->count(['course_id' => $data['course_id']]);
            app()->make(CourseDao::class)->update($data['course_id'], ['lesson_count' => $count]);
        }
        return app('json')->success('添加成功');
    }

    /**
     * 编辑课时
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            [['chapter_id', 'd'], 0],
            ['title', ''],
            ['video_id', ''],
            ['video_url', ''],
            [['duration', 'd'], 0],
            [['is_try', 'd'], 0],
            [['sort', 'd'], 0],
        ]);
        $this->dao->update((int)$id, $data);
        return app('json')->success('修改成功');
    }

    /**
     * 删除课时
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $lesson = $this->dao->get((int)$id);
        $this->dao->delete((int)$id);
        if ($lesson) {
            $count = $this->dao->count(['course_id' => $lesson['course_id']]);
            app()->make(CourseDao::class)->update($lesson['course_id'], ['lesson_count' => $count]);
        }
        return app('json')->success('删除成功');
    }
}
