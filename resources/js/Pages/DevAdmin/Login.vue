<template>
  <div class="bg-gray-950 flex items-center justify-center min-h-screen soft-grid px-4 relative overflow-hidden">
    <!-- Animated orbs -->
    <div class="orb"></div>
    <div class="orb"></div>

    <!-- PREMIUM LOGIN CARD -->
    <div class="premium-card w-full max-w-md bg-gray-900/60 backdrop-blur-2xl border border-gray-700 rounded-2xl shadow-2xl p-10 relative z-20">
      <h2 class="text-center text-4xl font-extrabold text-cyan-400 drop-shadow-lg tracking-wide mb-8">
        Developer Admin
      </h2>

      <div v-if="$page.props.flash.error" class="mb-4 text-red-400 bg-red-900/30 px-3 py-2 rounded-md backdrop-blur text-sm">
        {{ $page.props.flash.error }}
      </div>
      
      <div v-if="Object.keys(errors).length" class="mb-4 text-red-400 bg-red-900/30 px-3 py-2 rounded-md backdrop-blur text-sm">
        <ul class="list-disc list-inside">
          <li v-for="(error, field) in errors" :key="field">{{ error }}</li>
        </ul>
      </div>

      <form @submit.prevent="submit">
        <div class="mb-6">
          <label class="text-gray-300 text-sm mb-2 block font-medium">Email / Username</label>
          <input 
            type="text" 
            v-model="form.email" 
            required
            class="w-full px-4 py-3 bg-gray-800/70 border border-gray-700 rounded-lg text-gray-200
                   focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition placeholder-gray-500"
            placeholder="Enter your email"
          >
        </div>

        <div class="mb-8">
          <label class="text-gray-300 text-sm mb-2 block font-medium">Password</label>
          <input 
            type="password" 
            v-model="form.password" 
            required
            class="w-full px-4 py-3 bg-gray-800/70 border border-gray-700 rounded-lg text-gray-200
                   focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition placeholder-gray-500"
            placeholder="••••••••"
          >
        </div>

        <button 
          :disabled="form.processing"
          class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500
                 text-white py-3 rounded-lg font-semibold shadow-xl shadow-cyan-500/20 transition hover:scale-[1.02] 
                 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="form.processing">Logging in...</span>
          <span v-else>Login</span>
        </button>
      </form>

      <p class="text-center text-xs text-gray-500 mt-6">
        © {{ currentYear }} Developer Admin Panel
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  errors: Object
})

const form = useForm({
  email: '',
  password: '',
})

const currentYear = new Date().getFullYear()

const submit = () => {
  form.post(route('devAdmin.login.submit'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<style scoped>
/* Animated gradient orb background */
.orb {
  position: absolute;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(0,255,255,0.35), transparent 70%);
  animation: float 8s ease-in-out infinite alternate;
  filter: blur(80px);
  z-index: 10;
  top: -100px;
  left: -100px;
}
.orb:nth-child(2) {
  background: radial-gradient(circle, rgba(0,140,255,0.35), transparent 70%);
  animation-delay: 3s;
  right: -150px;
  bottom: -150px;
  top: auto;
  left: auto;
}

@keyframes float {
  0%   { transform: translate(-30px, -30px); }
  100% { transform: translate(30px, 30px); }
}

/* Luxury glowing border card */
.premium-card {
  position: relative;
  overflow: hidden;
}

.premium-card::before {
  content: "";
  position: absolute;
  inset: 0;
  padding: 2px;
  border-radius: 20px;
  background: linear-gradient(135deg, rgba(0,255,255,0.5), rgba(0,120,255,0.5), rgba(255,255,255,0.15));
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask-composite: exclude;
  pointer-events: none;
}

.soft-grid {
  background: radial-gradient(circle at center,
  rgba(255,255,255,0.04) 0%,
  transparent 60%),
  linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px),
  linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px);
  background-size: 300px 300px, 60px 60px, 60px 60px;
}
</style>
