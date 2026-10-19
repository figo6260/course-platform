<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 管理端
// +----------------------------------------------------------------------
namespace app\adminapi\controller\v1\course;

use app\adminapi\controller\AuthController;
use app\services\course\TeacherServices;
use think\facade\App;

/**
 * 老师管理
 * Class TeacherController
 * @package app\adminapi\controller\v1\course
 */
class TeacherController extends AuthController
{
    /**
     * @var TeacherServices
     */
    protected $service;

    /**
     * @param App $app
     * @param TeacherServices $service
     */
    public function __construct(App $app, TeacherServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 老师列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', ''],
            ['status', ''],
        ]);
        $where = array_filter($where, function ($v) {
            return $v !== '' && $v !== null;
        });
        [$page, $limit] = $this->service->getPageValue();
        $list = $this->service->getSearchList($where, $page, $limit);
        return app('json')->success($list);
    }

    /**
     * 新增老师
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['avatar', ''],
            ['title', ''],
            ['bio', ''],
            ['phone', ''],
            [['share_ratio', 'd'], 0],
            [['sort', 'd'], 0],
            [['status', 'd'], 1],
        ]);
        if (!$data['name']) {
            return app('json')->fail('老师姓名不能为空');
        }
        $data['add_time'] = time();
        $data['update_time'] = time();
        $this->service->save($data);
        return app('json')->success('添加成功');
    }

    /**
     * 编辑老师
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['avatar', ''],
            ['title', ''],
            ['bio', ''],
            ['phone', ''],
            [['share_ratio', 'd'], 0],
            [['sort', 'd'], 0],
            [['status', 'd'], 1],
        ]);
        $data['update_time'] = time();
        $this->service->update((int)$id, $data);
        return app('json')->success('修改成功');
    }

    /**
     * 删除老师
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->service->delete((int)$id);
        return app('json')->success('删除成功');
    }

    /**
     * 修改状态
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        $this->service->update((int)$id, ['status' => (int)$status, 'update_time' => time()]);
        return app('json')->success('修改成功');
    }
}
