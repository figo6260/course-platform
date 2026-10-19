<?php
// +----------------------------------------------------------------------
// | 卖课平台课程模块 - 管理端路由
// +----------------------------------------------------------------------
use think\facade\Route;

Route::group('course', function () {

    /** 老师管理 */
    Route::group(function () {
        Route::get('teacher', 'v1.course.TeacherController/index')->option(['real_name' => '老师列表']);
        Route::post('teacher', 'v1.course.TeacherController/save')->option(['real_name' => '新增老师']);
        Route::put('teacher/:id', 'v1.course.TeacherController/update')->option(['real_name' => '编辑老师']);
        Route::delete('teacher/:id', 'v1.course.TeacherController/delete')->option(['real_name' => '删除老师']);
        Route::put('teacher/set_status/:id/:status', 'v1.course.TeacherController/set_status')->option(['real_name' => '修改老师状态']);
    })->option(['parent' => 'course', 'cate_name' => '老师管理']);

    /** 课程管理 */
    Route::group(function () {
        Route::get('course', 'v1.course.CourseController/index')->option(['real_name' => '课程列表']);
        Route::post('course', 'v1.course.CourseController/save')->option(['real_name' => '新增课程']);
        Route::get('course/:id', 'v1.course.CourseController/read')->option(['real_name' => '课程详情']);
        Route::put('course/:id', 'v1.course.CourseController/update')->option(['real_name' => '编辑课程']);
        Route::delete('course/:id', 'v1.course.CourseController/delete')->option(['real_name' => '删除课程']);
        Route::put('course/set_status/:id/:status', 'v1.course.CourseController/set_status')->option(['real_name' => '课程上下架']);
    })->option(['parent' => 'course', 'cate_name' => '课程管理']);

    /** 章节管理 */
    Route::group(function () {
        Route::get('chapter/:courseId', 'v1.course.ChapterController/index')->option(['real_name' => '章节列表']);
        Route::post('chapter', 'v1.course.ChapterController/save')->option(['real_name' => '新增章节']);
        Route::put('chapter/:id', 'v1.course.ChapterController/update')->option(['real_name' => '编辑章节']);
        Route::delete('chapter/:id', 'v1.course.ChapterController/delete')->option(['real_name' => '删除章节']);
    })->option(['parent' => 'course', 'cate_name' => '章节管理']);

    /** 课时管理 */
    Route::group(function () {
        Route::get('lesson/:courseId', 'v1.course.LessonController/index')->option(['real_name' => '课时列表']);
        Route::post('lesson', 'v1.course.LessonController/save')->option(['real_name' => '新增课时']);
        Route::put('lesson/:id', 'v1.course.LessonController/update')->option(['real_name' => '编辑课时']);
        Route::delete('lesson/:id', 'v1.course.LessonController/delete')->option(['real_name' => '删除课时']);
    })->option(['parent' => 'course', 'cate_name' => '课时管理']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'course', 'mark_name' => '课程管理']);
