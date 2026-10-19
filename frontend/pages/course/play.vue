<template>
	<view class="play-page">
		<video
			id="courseVideo"
			:src="videoUrl"
			controls
			autoplay
			:title="title"
			style="width: 100%; height: 420rpx; background: #000;"
			@timeupdate="onTimeUpdate"
			@ended="onEnded"
			@error="onError"
		></video>
		<view class="info">
			<view class="title">{{ title }}</view>
			<view class="status">
				<text class="tag try" v-if="isTry">试看</text>
				<text class="tag bought" v-else>已购</text>
				<text class="hint" v-if="isTry">购买完整课程可观看全部课时</text>
			</view>
			<view class="buy-row" v-if="isTry && courseId">
				<view class="buy-btn" @click="goBuy">购买完整课程</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { getLessonPlayUrl, postStudyProgress } from "@/api/course.js";
	import { mapGetters } from "vuex";
	export default {
		data() {
			return {
				lessonId: 0,
				courseId: 0,
				title: "",
				videoUrl: "",
				isTry: false,
				lastReport: 0,
				reportTimer: null
			};
		},
		computed: {
			...mapGetters(["isLogin"])
		},
		onLoad(opt) {
			this.lessonId = opt.lessonId || 0;
			this.courseId = opt.courseId || 0;
			this.title = decodeURIComponent(opt.title || "");
			this.isTry = opt.isTry == "1";
			if (opt.videoUrl) {
				this.videoUrl = decodeURIComponent(opt.videoUrl);
			} else {
				this.fetchUrl();
			}
			// 每30秒上报一次进度（兜底）
			this.reportTimer = setInterval(() => this.reportProgress(false), 30000);
		},
		onUnload() {
			this.reportProgress(true);
			if (this.reportTimer) clearInterval(this.reportTimer);
		},
		methods: {
			fetchUrl() {
				getLessonPlayUrl(this.lessonId).then(res => {
					const url = res.data && res.data.video_url ? res.data.video_url : res.video_url;
					if (url) {
						this.videoUrl = url;
					} else {
						uni.showToast({ title: "获取播放地址失败", icon: "none" });
					}
				}).catch(() => {
					uni.showToast({ title: "请先登录或购买", icon: "none" });
				});
			},
			onTimeUpdate(e) {
				const t = Math.floor(e.detail.currentTime || 0);
				// 每60秒上报一次
				if (t - this.lastReport >= 60) {
					this.lastReport = t;
					this.reportProgress(false, t);
				}
			},
			onEnded() {
				this.reportProgress(true, 0, true);
				uni.showToast({ title: "本节已学完", icon: "none" });
			},
			onError() {
				uni.showToast({ title: "视频加载失败", icon: "none" });
			},
			reportProgress(finished, progress, done) {
				if (!this.isLogin || !this.lessonId) return;
				const ctx = uni.createVideoContext("courseVideo", this);
				const report = (p) => {
					postStudyProgress({
						lesson_id: this.lessonId,
						progress: p,
						finished: finished || done ? 1 : 0
					}).catch(() => {});
				};
				if (progress !== undefined) { report(progress); return; }
				try {
					ctx && report(this.lastReport);
				} catch (e) {}
			},
			goBuy() {
				uni.navigateTo({ url: "/pages/course/detail?id=" + this.courseId });
			}
		}
	};
</script>

<style scoped>
	.play-page { background: #f5f5f5; min-height: 100vh; }
	.info { padding: 24rpx; }
	.title { font-size: 32rpx; font-weight: bold; color: #333; }
	.status { margin-top: 16rpx; display: flex; align-items: center; }
	.tag { font-size: 24rpx; border-radius: 8rpx; padding: 4rpx 14rpx; }
	.tag.try { background: #e6f7ee; color: #07c160; }
	.tag.bought { background: #e8f0fe; color: #1989fa; }
	.hint { font-size: 24rpx; color: #999; margin-left: 16rpx; }
	.buy-row { margin-top: 30rpx; }
	.buy-btn { background: #e93323; color: #fff; text-align: center; font-size: 30rpx; border-radius: 44rpx; padding: 20rpx 0; }
</style>
