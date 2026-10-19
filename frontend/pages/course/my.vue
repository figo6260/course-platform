<template>
	<view class="my-course">
		<view class="course-item" v-for="item in list" :key="item.course_id || item.id" @click="goDetail(item.course_id || item.id)">
			<view class="cover">
				<image v-if="item.cover" :src="item.cover" mode="aspectFill"></image>
				<view v-else class="cover-placeholder">{{ item.title }}</view>
			</view>
			<view class="info">
				<view class="title">{{ item.title }}</view>
				<view class="teacher">{{ item.teacher_name || "" }}</view>
				<view class="progress-row">
					<view class="bar"><view class="fill" :style="{ width: progressPct(item) + '%' }"></view></view>
					<text class="pct">{{ progressPct(item) }}%</text>
				</view>
				<view class="go">继续学习 →</view>
			</view>
		</view>
		<view class="empty" v-if="!loading && list.length === 0">
			<view>还没有购买课程</view>
			<view class="go-list" @click="goList">去选课</view>
		</view>
		<view class="loading" v-if="loading">加载中...</view>
	</view>
</template>

<script>
	import { getMyCourses } from "@/api/course.js";
	import { toLogin } from "@/libs/login.js";
	import { mapGetters } from "vuex";
	export default {
		data() {
			return { list: [], loading: true };
		},
		computed: { ...mapGetters(["isLogin"]) },
		onLoad() {
			if (!this.isLogin) { toLogin(); return; }
			this.load();
		},
		onShow() {
			if (this.isLogin && !this.loading) this.load();
		},
		methods: {
			load() {
				this.loading = true;
				getMyCourses().then(res => {
					this.list = res || [];
					this.loading = false;
				}).catch(() => { this.loading = false; });
			},
			progressPct(item) {
				// 后端返回 finished_count / lesson_count 或 progress 字段，兼容处理
				if (item.progress_percent !== undefined) return Math.min(100, parseInt(item.progress_percent) || 0);
				if (item.lesson_count && item.finished_count !== undefined) {
					return Math.min(100, Math.round((item.finished_count / item.lesson_count) * 100));
				}
				return 0;
			},
			goDetail(id) {
				uni.navigateTo({ url: "/pages/course/detail?id=" + id });
			},
			goList() {
				uni.navigateTo({ url: "/pages/course/list" });
			}
		}
	};
</script>

<style scoped>
	.my-course { padding: 20rpx; background: #f5f5f5; min-height: 100vh; }
	.course-item { display: flex; background: #fff; border-radius: 16rpx; padding: 20rpx; margin-bottom: 20rpx; }
	.cover { width: 200rpx; height: 150rpx; border-radius: 12rpx; overflow: hidden; background: #e8e8e8; flex-shrink: 0; }
	.cover image { width: 100%; height: 100%; }
	.cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 22rpx; color: #999; text-align: center; padding: 8rpx; }
	.info { flex: 1; padding-left: 20rpx; }
	.title { font-size: 30rpx; font-weight: bold; color: #333; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
	.teacher { font-size: 24rpx; color: #999; margin-top: 6rpx; }
	.progress-row { display: flex; align-items: center; margin-top: 14rpx; }
	.bar { flex: 1; height: 10rpx; background: #eee; border-radius: 5rpx; overflow: hidden; }
	.fill { height: 100%; background: #07c160; border-radius: 5rpx; }
	.pct { font-size: 22rpx; color: #999; margin-left: 10rpx; }
	.go { font-size: 26rpx; color: #1989fa; margin-top: 10rpx; }
	.empty { text-align: center; color: #999; padding: 100rpx 0; font-size: 28rpx; }
	.go-list { color: #e93323; margin-top: 20rpx; font-size: 30rpx; }
	.loading { text-align: center; color: #999; padding: 60rpx 0; }
</style>
