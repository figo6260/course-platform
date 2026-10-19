<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块
// +----------------------------------------------------------------------
namespace app\services\course;

use app\dao\course\CourseChapterDao;
use app\dao\course\CourseDao;
use app\dao\course\CourseLessonDao;
use app\dao\course\CourseOrderDao;
use app\dao\course\CourseStudyDao;
use app\dao\course\TeacherDao;
use app\services\BaseServices;
use app\services\product\product\StoreProductServices;

/**
 * 课程服务
 * Class CourseServices
 * @package app\services\course
 */
class CourseServices extends BaseServices
{
    /**
     * @var CourseDao
     */
    protected $dao;

    public function __construct(CourseDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 课程列表（公开），带老师姓名
     * @param array $where
     * @return array
     */
    public function getCourseList(array $where = [])
    {
        $where = array_filter($where, function ($v) {
            return $v !== '' && $v !== 0 && $v !== null;
        });
        $where['status'] = 1;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getSearchList($where, $page, $limit, ['*']);
        if ($list) {
            /** @var TeacherDao $teacherDao */
            $teacherDao = app()->make(TeacherDao::class);
            foreach ($list as &$item) {
                $teacher = $teacherDao->get((int)$item['teacher_id'], ['name']);
                $item['teacher_name'] = $teacher ? $teacher['name'] : '';
            }
        }
        return $list;
    }

    /**
     * 课程详情（公开）：课程+章节+课时；课时 video_url 仅试看返回
     * @param int $id
     * @param int $uid
     * @return array
     */
    public function getCourseDetail(int $id, int $uid = 0)
    {
        $course = $this->dao->getOne(['id' => $id, 'status' => 1]);
        if (!$course) {
            return [];
        }
        $course = $course->toArray();
        /** @var TeacherDao $teacherDao */
        $teacherDao = app()->make(TeacherDao::class);
        $teacher = $teacherDao->get((int)$course['teacher_id']);
        $course['teacher'] = $teacher ? $teacher->toArray() : [];

        $isBuy = $uid ? $this->checkBuy($uid, $id) : false;
        $course['is_buy'] = $isBuy ? 1 : 0;

        /** @var CourseChapterDao $chapterDao */
        $chapterDao = app()->make(CourseChapterDao::class);
        /** @var CourseLessonDao $lessonDao */
        $lessonDao = app()->make(CourseLessonDao::class);
        $chapters = $chapterDao->getSearchList(['course_id' => $id], 0, 0, ['*']);
        foreach ($chapters as &$chapter) {
            $lessons = $lessonDao->getSearchList(
                ['course_id' => $id, 'chapter_id' => $chapter['id']],
                0, 0,
                ['id', 'title', 'duration', 'is_try', 'sort', 'video_url']
            );
            foreach ($lessons as &$lesson) {
                // 非试看且未购买：隐藏真实播放地址
                if (!$lesson['is_try'] && !$isBuy) {
                    $lesson['video_url'] = '';
                }
            }
            $chapter['lessons'] = $lessons;
        }
        $course['chapters'] = $chapters;
        return $course;
    }

    /**
     * 校验是否已购买课程
     * @param int $uid
     * @param int $courseId
     * @return bool
     */
    public function checkBuy(int $uid, int $courseId): bool
    {
        /** @var CourseOrderDao $orderDao */
        $orderDao = app()->make(CourseOrderDao::class);
        return (bool)$orderDao->getOne(['uid' => $uid, 'course_id' => $courseId, 'paid' => 1], 'id');
    }

    /**
     * 获取单节课时播放地址（需已购买或试看）
     * @param int $lessonId
     * @param int $uid
     * @return array
     */
    public function getLessonPlay(int $lessonId, int $uid)
    {
        /** @var CourseLessonDao $lessonDao */
        $lessonDao = app()->make(CourseLessonDao::class);
        $lesson = $lessonDao->get($lessonId);
        if (!$lesson) {
            return [];
        }
        $lesson = $lesson->toArray();
        if (!$lesson['is_try'] && !$this->checkBuy($uid, (int)$lesson['course_id'])) {
            return [];
        }
        return $lesson;
    }

    /**
     * 已购课程 + 学习进度
     * @param int $uid
     * @return array
     */
    public function getMyCourses(int $uid)
    {
        /** @var CourseOrderDao $orderDao */
        $orderDao = app()->make(CourseOrderDao::class);
        $orders = $orderDao->getSearchList(['uid' => $uid, 'paid' => 1], 0, 0, ['course_id', 'pay_time']);
        $result = [];
        /** @var CourseStudyDao $studyDao */
        $studyDao = app()->make(CourseStudyDao::class);
        /** @var CourseLessonDao $lessonDao */
        $lessonDao = app()->make(CourseLessonDao::class);
        foreach ($orders as $order) {
            $course = $this->dao->get((int)$order['course_id']);
            if (!$course) {
                continue;
            }
            $course = $course->toArray();
            $totalLesson = $lessonDao->count(['course_id' => $course['id']]);
            $finishedLesson = $studyDao->count(['uid' => $uid, 'course_id' => $course['id'], 'finished' => 1]);
            $course['total_lesson'] = $totalLesson;
            $course['finished_lesson'] = $finishedLesson;
            $course['progress'] = $totalLesson ? (int)round($finishedLesson / $totalLesson * 100) : 0;
            $course['pay_time'] = $order['pay_time'];
            $result[] = $course;
        }
        return $result;
    }

    /**
     * 保存学习进度
     * @param int $uid
     * @param int $lessonId
     * @param int $progress
     * @param int $finished
     * @return bool
     */
    public function saveProgress(int $uid, int $lessonId, int $progress, int $finished)
    {
        /** @var CourseLessonDao $lessonDao */
        $lessonDao = app()->make(CourseLessonDao::class);
        $lesson = $lessonDao->get($lessonId);
        if (!$lesson) {
            return false;
        }
        $courseId = (int)$lesson['course_id'];
        if (!$lesson['is_try'] && !$this->checkBuy($uid, $courseId)) {
            return false;
        }
        /** @var CourseStudyDao $studyDao */
        $studyDao = app()->make(CourseStudyDao::class);
        $study = $studyDao->getOne(['uid' => $uid, 'lesson_id' => $lessonId]);
        $data = [
            'uid' => $uid,
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'progress' => max(0, min(100, $progress)),
            'finished' => $finished ? 1 : 0,
            'update_time' => time(),
        ];
        if ($study) {
            return $studyDao->update($study['id'], $data);
        }
        return (bool)$studyDao->save($data);
    }

    /**
     * 创建课程时自动创建一个虚拟商品，product_id 回写到课程
     * @param int $courseId
     * @return int 商品ID
     */
    public function createProductForCourse(int $courseId)
    {
        $course = $this->dao->get($courseId);
        if (!$course || $course['product_id']) {
            return $course ? (int)$course['product_id'] : 0;
        }
        /** @var StoreProductServices $productServices */
        $productServices = app()->make(StoreProductServices::class);
        $product = $productServices->create([
            'store_name' => $course['title'],
            'store_info' => mb_substr(strip_tags($course['intro'] ?? ''), 0, 200),
            'image' => $course['cover'] ?? '',
            'slider_image' => $course['cover'] ? [$course['cover']] : [],
            'price' => $course['price'],
            'ot_price' => $course['market_price'] ?: $course['price'],
            'cost' => $course['price'],
            'stock' => 99999,
            'is_virtual' => 1,
            'virtual_type' => 0,
            'is_show' => 1,
            'is_del' => 0,
            'unit_name' => '门',
            'sort' => $course['sort'] ?? 0,
            'add_time' => time(),
        ]);
        $productId = $product ? (int)$product->getKey() : 0;
        if ($productId) {
            $this->dao->update($courseId, ['product_id' => $productId]);
        }
        return $productId;
    }
}
