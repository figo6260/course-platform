<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块
// +----------------------------------------------------------------------
namespace app\model\course;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * 课程Model
 * Class Course
 * @package app\model\course
 */
class Course extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $autoWriteTimestamp = 'int';

    protected $name = 'course';
}
