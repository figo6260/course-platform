<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 管理端
// +----------------------------------------------------------------------
namespace app\adminapi\controller\v1\course;

use app\adminapi\controller\AuthController;
use app\services\course\CourseServices;
use think\facade\App;

/**
 * 课程管理
 * Class CourseController
 * @package app\adminapi\controller\v1\course
 */
class CourseController extends AuthController
{
    /**
     * @var CourseServices
     */
    protected $service;

    /**
     * @param App $app
     * @param CourseServices $service
     */
    public function __construct(App $app, CourseServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 课程列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', ''],
            [['teacher_id', 'd'], 0],
            ['status', ''],
        ]);
        $where = array_filter($where, function ($v) {
            return $v !== '' && $v !== 0 && $v !== null;
        });
        [$page, $limit] = $this->service->getPageValue();
        $list = $this->service->getSearchList($where, $page, $limit);
        return app('json')->success($list);
    }

    /**
     * 新增课程（自动创建关联虚拟商品）
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            [['teacher_id', 'd'], 0],
            ['title', ''],
            ['cover', ''],
            ['intro', ''],
            ['price', 0],
            ['market_price', 0],
            [['sort', 'd'], 0],
            [['status', 'd'], 1],
        ]);
        if (!$data['title']) {
            return app('json')->fail('课程标题不能为空');
        }
        if (!$data['teacher_id']) {
            return app('json')->fail('请选择老师');
        }
        $data['add_time'] = time();
        $data['update_time'] = time();
        $id = $this->service->save($data);
        // 自动创建关联虚拟商品，打通购买链路
        if ($id) {
            $this->service->createProductForCourse((int)$id);
        }
        return app('json')->success('添加成功');
    }

    /**
     * 编辑课程
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            [['teacher_id', 'd'], 0],
            ['title', ''],
            ['cover', ''],
            ['intro', ''],
            ['price', 0],
            ['market_price', 0],
            [['sort', 'd'], 0],
            [['status', 'd'], 1],
        ]);
        $data['update_time'] = time();
        $this->service->update((int)$id, $data);
        return app('json')->success('修改成功');
    }

    /**
     * 删除课程
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->service->delete((int)$id);
        return app('json')->success('删除成功');
    }

    /**
     * 上下架
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        $this->service->update((int)$id, ['status' => (int)$status, 'update_time' => time()]);
        return app('json')->success('修改成功');
    }

    /**
     * 课程详情（含章节课时）
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        $detail = $this->service->getCourseDetail((int)$id);
        return app('json')->success($detail);
    }
}
