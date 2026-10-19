<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 管理端
// +----------------------------------------------------------------------
namespace app\adminapi\controller\v1\course;

use app\adminapi\controller\AuthController;
use app\dao\course\CourseChapterDao;
use think\facade\App;

/**
 * 章节管理
 * Class ChapterController
 * @package app\adminapi\controller\v1\course
 */
class ChapterController extends AuthController
{
    /**
     * @var CourseChapterDao
     */
    protected $dao;

    public function __construct(App $app, CourseChapterDao $dao)
    {
        parent::__construct($app);
        $this->dao = $dao;
    }

    /**
     * 章节列表
     * @param $courseId
     * @return mixed
     */
    public function index($courseId)
    {
        $list = $this->dao->getSearchList(['course_id' => (int)$courseId], 0, 0, ['*']);
        return app('json')->success($list);
    }

    /**
     * 新增章节
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            [['course_id', 'd'], 0],
            ['title', ''],
            [['sort', 'd'], 0],
        ]);
        if (!$data['course_id'] || !$data['title']) {
            return app('json')->fail('参数错误');
        }
        $data['add_time'] = time();
        $this->dao->save($data);
        return app('json')->success('添加成功');
    }

    /**
     * 编辑章节
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['title', ''],
            [['sort', 'd'], 0],
        ]);
        $this->dao->update((int)$id, $data);
        return app('json')->success('修改成功');
    }

    /**
     * 删除章节
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->dao->delete((int)$id);
        return app('json')->success('删除成功');
    }
}
