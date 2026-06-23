<template>
    <Transition name="premium-fade">
        <div v-if="show && activeCampaigns.length > 0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 antialiased">
            <!-- Backdrop with optimized blur -->
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-[8px]" @click="closeModal"></div>

            <!-- Modal Content Wrapper (Taller Aspect Ratio) -->
            <div class="relative w-full max-w-lg bg-white/5 backdrop-blur-3xl rounded-[3.5rem] border border-white/20 shadow-[0_48px_128px_-16px_rgba(0,0,0,0.7)] overflow-hidden transform transition-all duration-700 scale-100 animate-premium-pop will-change-transform"
                role="dialog" aria-modal="true">
                <!-- Top Glows (Optimized) -->
                <div
                    class="absolute -top-32 -left-32 w-80 h-80 bg-amber-500/20 rounded-full blur-[80px] pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-32 -right-32 w-80 h-80 bg-indigo-500/10 rounded-full blur-[80px] pointer-events-none">
                </div>

                <!-- Close Button (Premium SVG Design for 100% reliability) -->
                <div class="absolute top-8 right-8 z-50">
                    <button @click="closeModal"
                        class="bg-white/10 hover:bg-white/20 backdrop-blur-3xl text-white w-12 h-12 rounded-full flex items-center justify-center transition-all hover:rotate-90 hover:scale-110 active:scale-95 border border-white/20 shadow-2xl group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-6 h-6 group-hover:text-amber-300 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Slider Container (Taller Height: 4/5 Aspect Ratio) -->
                <div class="relative aspect-[4/5] overflow-hidden group">
                    <TransitionGroup name="premium-slide">
                        <div v-for="(campaign, index) in activeCampaigns" :key="campaign.id"
                            v-show="currentSlide === index" class="absolute inset-0">
                            <!-- Animated Image (Hardware Accelerated) -->
                            <div class="absolute inset-0 transition-transform duration-[15s] ease-out will-change-transform"
                                :class="{ 'scale-110': currentSlide === index }">
                                <img :src="campaign.full_image_url" :alt="campaign.title"
                                    class="w-full h-full object-cover" />
                            </div>

                            <!-- Refined Gradient Overlays -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent">
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/40 to-transparent opacity-60">
                            </div>

                            <!-- Content Overlay -->
                            <div class="absolute inset-0 p-10 md:p-14 flex flex-col justify-end">
                                <div class="transition-all duration-1000 delay-300 transform"
                                    :class="[currentSlide === index ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0']">
                                    <span
                                        class="inline-block text-amber-400 text-[10px] md:text-xs font-black uppercase tracking-[0.4em] mb-5 bg-amber-400/10 px-4 py-1.5 rounded-full border border-amber-400/20 backdrop-blur-md">
                                        Exclusive Campaign
                                    </span>
                                    <h3
                                        class="text-4xl md:text-6xl font-black text-white mb-6 leading-[1.05] tracking-tight drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]">
                                        {{ campaign.title }}
                                    </h3>

                                    <div v-if="campaign.link_url" class="mt-10 flex items-center gap-6">
                                        <a :href="campaign.link_url" class="premium-btn group">
                                            <span>Claim Invitation</span>
                                            <div class="glow-effect"></div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>

                    <!-- Navigation / Performance-Optimized Progress Indicators -->
                    <div v-if="activeCampaigns.length > 1"
                        class="absolute bottom-12 right-12 flex items-center gap-4 z-10">
                        <div v-for="(_, index) in activeCampaigns" :key="index" @click="setSlide(index)"
                            class="relative h-1.5 cursor-pointer transition-all duration-500 overflow-hidden rounded-full"
                            :class="[currentSlide === index ? 'w-16 bg-white/10' : 'w-4 bg-white/10 hover:bg-white/40']">
                            <!-- Progress Bar -->
                            <div v-if="currentSlide === index"
                                class="absolute inset-y-0 left-0 bg-gradient-to-r from-amber-400 to-amber-200 shadow-[0_0_15px_rgba(251,191,36,0.8)]"
                                :style="{ width: progress + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Optimized Footer -->
                <div
                    class="px-12 py-8 bg-black/50 backdrop-blur-3xl border-t border-white/5 flex items-center justify-between">
                    <label class="flex items-center gap-5 cursor-pointer group">
                        <div
                            class="relative w-14 h-7 bg-white/10 rounded-full border border-white/20 transition-all group-hover:scale-105">
                            <input type="checkbox" v-model="dontShowAgain" class="sr-only peer">
                            <div
                                class="absolute left-1.5 top-1.5 w-4 h-4 bg-white/50 rounded-full transition-all duration-500 peer-checked:left-8 peer-checked:bg-amber-400 peer-checked:shadow-[0_0_15px_rgba(251,191,36,0.6)]">
                            </div>
                        </div>
                        <span
                            class="text-[10px] font-bold text-white/50 group-hover:text-amber-400 transition-colors uppercase tracking-[0.25em] pt-0.5">Don't
                            show again today</span>
                    </label>
                    <div class="hidden md:flex items-center gap-3">
                        <span class="text-[9px] font-black text-white/30 uppercase tracking-[0.4em]">Limited
                            Edition</span>
                        <div
                            class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)] animate-pulse">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    activeCampaigns: {
        type: Array,
        default: () => []
    }
})

const show = ref(false)
const currentSlide = ref(0)
const dontShowAgain = ref(false)
const progress = ref(0)

let rafId = null
let startTime = null
const duration = 6000 // 6 seconds per slide

const closeModal = () => {
    if (dontShowAgain.value) {
        const tomorrow = new Date()
        tomorrow.setHours(24, 0, 0, 0)
        localStorage.setItem('hide_campaign_until', tomorrow.getTime().toString())
    }
    show.value = false
}

// Performance Optimized Animation Loop using requestAnimationFrame
const updateProgress = () => {
    if (!startTime) startTime = Date.now()
    const elapsed = Date.now() - startTime
    progress.value = Math.min((elapsed / duration) * 100, 100)

    if (progress.value >= 100) {
        nextSlide()
    } else {
        rafId = requestAnimationFrame(updateProgress)
    }
}

const startAnimation = () => {
    if (props.activeCampaigns.length === 0) return

    cancelAnimationFrame(rafId)
    startTime = null
    progress.value = 0
    rafId = requestAnimationFrame(updateProgress)
}

const nextSlide = () => {
    if (props.activeCampaigns.length <= 1) {
        progress.value = 100
        return
    }
    currentSlide.value = (currentSlide.value + 1) % props.activeCampaigns.length
    startAnimation()
}

const setSlide = (index) => {
    currentSlide.value = index
    startAnimation()
}

onMounted(() => {
    if (props.activeCampaigns.length === 0) return

    const hideUntil = localStorage.getItem('hide_campaign_until')
    if (!hideUntil || Date.now() > parseInt(hideUntil)) {
        setTimeout(() => {
            show.value = true
            startAnimation()
        }, 1500)
    }
})

onUnmounted(() => {
    cancelAnimationFrame(rafId)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');

* {
    font-family: 'Outfit', sans-serif;
}

/* Base Transitions */
.premium-fade-enter-active,
.premium-fade-leave-active {
    transition: all 0.9s cubic-bezier(0.16, 1, 0.3, 1);
}

.premium-fade-enter-from,
.premium-fade-leave-to {
    opacity: 0;
    backdrop-filter: blur(0);
}

@keyframes premiumPop {
    0% {
        opacity: 0;
        transform: scale(0.96) translateY(30px);
    }

    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-premium-pop {
    animation: premiumPop 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Advanced Slide Animations */
.premium-slide-enter-active {
    transition: all 1.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.premium-slide-leave-active {
    transition: all 1s cubic-bezier(0.7, 0, 0.84, 0);
}

.premium-slide-enter-from {
    opacity: 0;
    transform: scale(1.15) translateY(20px);
    filter: blur(15px);
}

.premium-slide-leave-to {
    opacity: 0;
    transform: scale(0.95);
    filter: blur(30px);
}

/* Premium Button (Hardware Accelerated) */
.premium-btn {
    position: relative;
    background: white;
    color: #020617;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-size: 13px;
    padding: 1.2rem 3rem;
    border-radius: 99px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    box-shadow: 0 10px 40px -10px rgba(255, 255, 255, 0.4);
    will-change: transform, shadow;
}

.premium-btn:hover {
    transform: translateY(-5px) scale(1.03);
    box-shadow: 0 25px 50px -12px rgba(255, 255, 255, 0.5);
}

.premium-btn:active {
    transform: translateY(0) scale(0.97);
}

.glow-effect {
    position: absolute;
    top: 0;
    left: -150%;
    width: 150%;
    height: 100%;
    background: linear-gradient(90deg,
            transparent,
            rgba(255, 255, 255, 0.9),
            transparent);
    transition: 0.7s;
}

.premium-btn:hover .glow-effect {
    left: 150%;
}
</style>
