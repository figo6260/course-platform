<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 订单支付成功后发放课程学习权限
// +----------------------------------------------------------------------
namespace app\listener\course;

use app\dao\course\CourseDao;
use app\dao\course\CourseOrderDao;
use app\dao\order\StoreOrderCartInfoDao;

/**
 * 课程支付成功监听器
 * 挂载在 OrderPaySuccessListener 事件上：订单支付成功后，
 * 若订单商品关联了课程（eb_course.product_id），则写入课程订单并发放学习权限
 * Class CoursePaySuccessListener
 * @package app\listener\course
 */
class CoursePaySuccessListener
{
    /**
     * @param $event
     */
    public function handle($event): void
    {
        [$orderInfo] = $event;

        // 只处理普通商品订单
        if (!empty($orderInfo['seckill_id']) || !empty($orderInfo['bargain_id']) || !empty($orderInfo['combination_id'])) {
            return;
        }

        /** @var StoreOrderCartInfoDao $cartInfoDao */
        $cartInfoDao = app()->make(StoreOrderCartInfoDao::class);
        /** @var CourseDao $courseDao */
        $courseDao = app()->make(CourseDao::class);
        /** @var CourseOrderDao $courseOrderDao */
        $courseOrderDao = app()->make(CourseOrderDao::class);

        $cartInfos = $cartInfoDao->selectList(['oid' => $orderInfo['id']], 'cart_info');
        foreach ($cartInfos as $cartInfo) {
            $cart = is_string($cartInfo['cart_info']) ? json_decode($cartInfo['cart_info'], true) : $cartInfo['cart_info'];
            if (!$cart) {
                continue;
            }
            // 兼容多种 cart_info 结构
            $productId = $cart['product_id']
                ?? $cart['productInfo']['id']
                ?? $cart['productInfo']['product_id']
                ?? 0;
            if (!$productId) {
                continue;
            }
            $course = $courseDao->getOne(['product_id' => (int)$productId, 'status' => 1]);
            if (!$course) {
                continue;
            }
            // 幂等：已发放过则跳过
            $exists = $courseOrderDao->getOne([
                'order_id' => $orderInfo['order_id'],
                'course_id' => $course['id'],
                'paid' => 1,
            ]);
            if ($exists) {
                continue;
            }
            $courseOrderDao->save([
                'order_id' => $orderInfo['order_id'],
                'uid' => $orderInfo['uid'],
                'course_id' => $course['id'],
                'teacher_id' => $course['teacher_id'],
                'price' => $orderInfo['pay_price'] ?? $course['price'],
                'paid' => 1,
                'pay_time' => time(),
                'add_time' => time(),
            ]);
        }
    }
}
