// 课程相关接口
import request from "@/utils/request.js";

/**
 * 解析后端返回：list 类接口的 msg 可能是 JSON 字符串
 */
function parseList(res) {
	if (Array.isArray(res)) return res;
	if (typeof res === "string") {
		try { return JSON.parse(res); } catch (e) { return []; }
	}
	return res || [];
}

/**
 * 课程列表（公开）
 */
export function getCourseList(data) {
	return request.get('course/list', data || {}, { noAuth: true }).then(res => parseList(res.msg));
}

/**
 * 课程详情（公开），含章节课时、老师信息、is_buy
 */
export function getCourseDetail(id) {
	return request.get('course/detail/' + id, {}, { noAuth: true }).then(res => res.data);
}

/**
 * 获取课时播放地址（需登录，已购或试看）
 */
export function getLessonPlayUrl(id) {
	return request.get('course/lesson/' + id, {});
}

/**
 * 我的课程（需登录）
 */
export function getMyCourses() {
	return request.get('course/my', {}).then(res => parseList(res.data !== undefined ? res.data : res.msg));
}

/**
 * 上报学习进度（需登录）
 */
export function postStudyProgress(data) {
	return request.post('course/progress', data);
}

/**
 * 老师列表（公开）
 */
export function getTeacherList() {
	return request.get('teacher/list', {}, { noAuth: true }).then(res => parseList(res.msg));
}

/**
 * 老师详情（公开）
 */
export function getTeacherDetail(id) {
	return request.get('teacher/detail/' + id, {}, { noAuth: true }).then(res => res.data);
}
