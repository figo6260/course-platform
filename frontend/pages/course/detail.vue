<template>
	<view class="course-detail" v-if="course">
		<view class="cover">
			<image v-if="course.cover" :src="course.cover" mode="aspectFill"></image>
			<view v-else class="cover-placeholder">{{ course.title }}</view>
		</view>
		<view class="body">
			<view class="title">{{ course.title }}</view>
			<view class="teacher-row" v-if="course.teacher">
				<text class="teacher-name">{{ course.teacher.name }}</text>
				<text class="teacher-title" v-if="course.teacher.title">{{ course.teacher.title }}</text>
			</view>
			<view class="price-row">
				<text class="price">¥{{ course.price }}</text>
				<text class="market" v-if="course.market_price && course.market_price != course.price">¥{{ course.market_price }}</text>
				<text class="students">{{ course.student_count || 0 }}人在学 · {{ course.lesson_count || 0 }}课时</text>
			</view>
			<view class="intro" v-if="course.intro">
				<view class="sec-title">课程介绍</view>
				<view class="intro-text">{{ course.intro }}</view>
			</view>
			<view class="chapters">
				<view class="sec-title">课程目录</view>
				<view class="chapter" v-for="(ch, ci) in course.chapters" :key="ch.id">
					<view class="chapter-title" @click="toggleChapter(ci)">
						<text>{{ ch.title }}</text>
						<text class="arrow">{{ openChapters[ci] ? '▲' : '▼' }}</text>
					</view>
					<view class="lesson-list" v-if="openChapters[ci]">
						<view class="lesson" v-for="les in ch.lessons" :key="les.id" @click="playLesson(les)">
							<text class="lesson-title">{{ les.title }}</text>
							<text class="try-tag" v-if="les.is_try == 1">试看</text>
							<text class="lock" v-else-if="!canWatch(les)">🔒</text>
							<text class="duration" v-if="les.duration">{{ fmtDuration(les.duration) }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="bottom-bar" v-if="!course.is_buy">
			<view class="buy-btn" @click="buyNow">立即购买 ¥{{ course.price }}</view>
		</view>
		<view class="bottom-bar" v-else>
			<view class="study-btn" @click="studyNow">继续学习</view>
		</view>
	</view>
	<view class="loading" v-else>加载中...</view>
</template>

<script>
	import { getCourseDetail } from "@/api/course.js";
	import { postCartAdd } from "@/api/store.js";
	import { toLogin } from "@/libs/login.js";
	import { mapGetters } from "vuex";
	export default {
		data() {
			return {
				id: 0,
				course: null,
				openChapters: {}
			};
		},
		computed: {
			...mapGetters(["isLogin"])
		},
		onLoad(opt) {
			this.id = opt.id || 0;
			this.loadDetail();
		},
		methods: {
			loadDetail() {
				getCourseDetail(this.id).then(res => {
					this.course = res;
					// 默认展开第一章
					if (res.chapters && res.chapters.length) {
						this.$set(this.openChapters, 0, true);
					}
				});
			},
			toggleChapter(i) {
				this.$set(this.openChapters, i, !this.openChapters[i]);
			},
			canWatch(les) {
				return this.course.is_buy == 1 || les.is_try == 1;
			},
			playLesson(les) {
				if (!this.canWatch(les)) {
					uni.showToast({ title: "购买后可观看", icon: "none" });
					return;
				}
				let url = "/pages/course/play?lessonId=" + les.id + "&courseId=" + this.id
					+ "&isTry=" + (les.is_try == 1 ? 1 : 0)
					+ "&title=" + encodeURIComponent(les.title || "");
				if (les.is_try == 1 && les.video_url) {
					url += "&videoUrl=" + encodeURIComponent(les.video_url);
				}
				uni.navigateTo({ url: url });
			},
			studyNow() {
				// 跳到第一课
				const ch = this.course.chapters && this.course.chapters[0];
				const les = ch && ch.lessons && ch.lessons[0];
				if (les) this.playLesson(les);
			},
			fmtDuration(s) {
				s = parseInt(s) || 0;
				const m = Math.floor(s / 60), sec = s % 60;
				return (m < 10 ? "0" + m : m) + ":" + (sec < 10 ? "0" + sec : sec);
			},
			buyNow() {
				if (!this.isLogin) { toLogin(); return; }
				const pid = this.course.product_id;
				if (!pid) { uni.showToast({ title: "课程商品未配置", icon: "none" }); return; }
				uni.showLoading({ title: "正在下单" });
				postCartAdd({ productId: pid, cartNum: 1, new: 1 }).then(res => {
					uni.hideLoading();
					uni.navigateTo({ url: "/pages/goods/order_confirm/index?new=1&cartId=" + res.data.cartId });
				}).catch(() => uni.hideLoading());
			}
		}
	};
</script>

<style scoped>
	.course-detail { background: #f5f5f5; min-height: 100vh; padding-bottom: 130rpx; }
	.cover { width: 100%; height: 400rpx; background: #e8e8e8; }
	.cover image { width: 100%; height: 100%; }
	.cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 32rpx; color: #999; }
	.body { padding: 24rpx; }
	.title { font-size: 36rpx; font-weight: bold; color: #333; }
	.teacher-row { margin-top: 12rpx; }
	.teacher-name { font-size: 28rpx; color: #666; }
	.teacher-title { font-size: 24rpx; color: #999; margin-left: 12rpx; }
	.price-row { display: flex; align-items: baseline; margin-top: 16rpx; }
	.price { font-size: 40rpx; color: #e93323; font-weight: bold; }
	.market { font-size: 26rpx; color: #bbb; text-decoration: line-through; margin-left: 12rpx; }
	.students { font-size: 24rpx; color: #bbb; margin-left: auto; }
	.sec-title { font-size: 30rpx; font-weight: bold; color: #333; margin: 28rpx 0 16rpx; }
	.intro-text { font-size: 28rpx; color: #666; line-height: 1.7; background: #fff; border-radius: 12rpx; padding: 20rpx; }
	.chapter { background: #fff; border-radius: 12rpx; margin-bottom: 16rpx; overflow: hidden; }
	.chapter-title { padding: 24rpx 20rpx; font-size: 30rpx; font-weight: bold; color: #333; display: flex; justify-content: space-between; }
	.arrow { color: #bbb; font-size: 24rpx; }
	.lesson { display: flex; align-items: center; padding: 20rpx; border-top: 1rpx solid #f0f0f0; font-size: 28rpx; color: #555; }
	.lesson-title { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
	.try-tag { font-size: 22rpx; color: #fff; background: #07c160; border-radius: 8rpx; padding: 4rpx 12rpx; margin-left: 12rpx; }
	.lock { margin-left: 12rpx; font-size: 26rpx; }
	.duration { font-size: 24rpx; color: #bbb; margin-left: 12rpx; }
	.bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 20rpx 24rpx calc(20rpx + env(safe-area-inset-bottom)); box-shadow: 0 -2rpx 10rpx rgba(0,0,0,0.06); }
	.buy-btn, .study-btn { background: #e93323; color: #fff; text-align: center; font-size: 32rpx; font-weight: bold; border-radius: 44rpx; padding: 22rpx 0; }
	.study-btn { background: #07c160; }
	.loading { text-align: center; padding: 100rpx 0; color: #999; }
</style>
