<template>
	<view class="course-list">
		<view class="course-item" v-for="item in list" :key="item.id" @click="goDetail(item.id)">
			<view class="cover">
				<image v-if="item.cover" :src="item.cover" mode="aspectFill"></image>
				<view v-else class="cover-placeholder">{{ item.title }}</view>
			</view>
			<view class="info">
				<view class="title">{{ item.title }}</view>
				<view class="teacher">{{ item.teacher_name || '' }}</view>
				<view class="meta">
					<text class="price">¥{{ item.price }}</text>
					<text class="market" v-if="item.market_price && item.market_price != item.price">¥{{ item.market_price }}</text>
					<text class="students">{{ item.student_count || 0 }}人在学</text>
				</view>
				<view class="lessons" v-if="item.lesson_count">{{ item.lesson_count }}课时</view>
			</view>
		</view>
		<view class="empty" v-if="!loading && list.length === 0">暂无课程</view>
		<view class="loading" v-if="loading">加载中...</view>
	</view>
</template>

<script>
	import { getCourseList } from "@/api/course.js";
	export default {
		data() {
			return {
				list: [],
				loading: true
			};
		},
		onLoad() {
			this.loadList();
		},
		onPullDownRefresh() {
			this.loadList().finally(() => uni.stopPullDownRefresh());
		},
		methods: {
			loadList() {
				this.loading = true;
				return getCourseList().then(res => {
					this.list = res || [];
					this.loading = false;
				}).catch(() => {
					this.loading = false;
				});
			},
			goDetail(id) {
				uni.navigateTo({ url: "/pages/course/detail?id=" + id });
			}
		}
	};
</script>

<style scoped>
	.course-list { padding: 20rpx; background: #f5f5f5; min-height: 100vh; }
	.course-item { display: flex; background: #fff; border-radius: 16rpx; padding: 20rpx; margin-bottom: 20rpx; }
	.cover { width: 220rpx; height: 160rpx; border-radius: 12rpx; overflow: hidden; background: #e8e8e8; flex-shrink: 0; }
	.cover image { width: 100%; height: 100%; }
	.cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 24rpx; color: #999; padding: 10rpx; text-align: center; }
	.info { flex: 1; padding-left: 20rpx; display: flex; flex-direction: column; justify-content: space-between; }
	.title { font-size: 30rpx; font-weight: bold; color: #333; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
	.teacher { font-size: 24rpx; color: #999; margin-top: 8rpx; }
	.meta { display: flex; align-items: baseline; margin-top: 8rpx; }
	.price { font-size: 32rpx; color: #e93323; font-weight: bold; }
	.market { font-size: 24rpx; color: #bbb; text-decoration: line-through; margin-left: 10rpx; }
	.students { font-size: 22rpx; color: #bbb; margin-left: auto; }
	.lessons { font-size: 22rpx; color: #999; }
	.empty, .loading { text-align: center; color: #999; padding: 60rpx 0; font-size: 28rpx; }
</style>
