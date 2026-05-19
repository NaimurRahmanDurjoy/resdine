<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4 selection:bg-amber-500 selection:text-slate-950">
    <!-- Visual background glow elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-md bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 shadow-2xl relative z-10">
      
      <!-- Brand Logo / Title -->
      <div class="text-center mb-6">
        <span class="text-xs font-black uppercase tracking-widest text-amber-500 bg-amber-500/10 px-3 py-1.5 rounded-full">
          ResDine Secure Sandbox
        </span>
        <h1 class="text-2xl font-black text-white mt-3">Payment Simulator</h1>
        <p class="text-sm text-slate-400 mt-1">Review your details and complete test checkout below</p>
      </div>

      <!-- Invoice Summary Card -->
      <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-4 mb-6">
        <div class="flex justify-between items-center mb-2">
          <span class="text-xs font-bold text-slate-500 uppercase">Order Ref</span>
          <span class="text-sm font-black text-white">#{{ orderNumber }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-xs font-bold text-slate-500 uppercase">Amount Due</span>
          <span class="text-xl font-black text-amber-500">{{ currency }}{{ amount }}</span>
        </div>
      </div>

      <!-- 1. bKash Simulation Form -->
      <div v-if="gateway === 'bkash'" class="space-y-4">
        <!-- bKash Header branding -->
        <div class="bg-pink-600 rounded-2xl p-4 flex items-center justify-between text-white shadow-lg">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-2xl">smartphone</span>
            <div class="text-left">
              <div class="text-xs font-bold uppercase tracking-wider opacity-90">Tokenized Checkout</div>
              <div class="text-sm font-black">bKash Payment Gateway</div>
            </div>
          </div>
          <span class="text-xs font-black bg-white/20 px-2.5 py-1 rounded">Sandbox</span>
        </div>

        <form @submit.prevent="submitPayment" class="space-y-4">
          <!-- Step 1: Wallet Number -->
          <div v-if="bkashStep === 1">
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">bKash Wallet Number</label>
            <input v-model="walletNumber" type="text" placeholder="e.g. 017XXXXXXXX"
              class="w-full bg-slate-950 border border-slate-850 focus:border-pink-500 text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 transition-all font-mono"
              required>
            <p class="text-[11px] text-slate-500 mt-1.5">Enter any 11-digit mobile wallet number to simulate bKash Checkout OTP verification.</p>
            <button type="button" @click="bkashStep = 2" :disabled="!walletNumber"
              class="w-full bg-pink-600 hover:bg-pink-700 text-white font-black text-sm py-4 rounded-xl shadow-lg hover:shadow-pink-600/10 transition-all mt-4 disabled:opacity-50 disabled:cursor-not-allowed">
              Send verification OTP
            </button>
          </div>

          <!-- Step 2: OTP & Pin -->
          <div v-else class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Enter verification OTP</label>
              <input v-model="otpCode" type="text" placeholder="e.g. 123456"
                class="w-full bg-slate-950 border border-slate-850 focus:border-pink-500 text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 transition-all font-mono text-center tracking-widest"
                required>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Enter Wallet PIN</label>
              <input v-model="pinCode" type="password" placeholder="••••" maxlength="4"
                class="w-full bg-slate-950 border border-slate-850 focus:border-pink-500 text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 transition-all font-mono text-center tracking-widest"
                required>
            </div>
            <button type="submit" :disabled="isPaying"
              class="w-full bg-pink-600 hover:bg-pink-700 text-white font-black text-sm py-4 rounded-xl shadow-lg hover:shadow-pink-600/10 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
              <span v-if="isPaying" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              {{ isPaying ? 'Processing Transaction...' : `Confirm Pay BDT ${amount}` }}
            </button>
            <button type="button" @click="bkashStep = 1" class="w-full text-xs text-slate-500 hover:text-slate-400 font-bold transition-all text-center">
              Back to wallet entry
            </button>
          </div>
        </form>
      </div>

      <!-- 2. Stripe Simulation Form -->
      <div v-else-if="gateway === 'stripe'" class="space-y-4">
        <!-- Stripe Header branding -->
        <div class="bg-indigo-600 rounded-2xl p-4 flex items-center justify-between text-white shadow-lg">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-2xl">credit_card</span>
            <div class="text-left">
              <div class="text-xs font-bold uppercase tracking-wider opacity-90">Secure Element</div>
              <div class="text-sm font-black">Stripe Payments Checkout</div>
            </div>
          </div>
          <span class="text-xs font-black bg-white/20 px-2.5 py-1 rounded">Sandbox</span>
        </div>

        <form @submit.prevent="submitPayment" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Cardholder Name</label>
            <input v-model="cardName" type="text" placeholder="John Doe"
              class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all"
              required>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Card Number</label>
            <div class="relative">
              <input v-model="cardNumber" type="text" placeholder="4242 4242 4242 4242" maxlength="19"
                class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 text-white rounded-xl pl-4 pr-10 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all font-mono"
                required>
              <span class="absolute right-3.5 top-3.5 material-symbols-outlined text-slate-500 text-lg">credit_card</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Expiry Date</label>
              <input v-model="cardExpiry" type="text" placeholder="MM/YY" maxlength="5"
                class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all font-mono text-center"
                required>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">CVC / CVV</label>
              <input v-model="cardCvc" type="password" placeholder="•••" maxlength="3"
                class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all font-mono text-center"
                required>
            </div>
          </div>

          <div class="bg-indigo-500/5 rounded-2xl border border-indigo-500/10 p-3 flex gap-2.5 items-start">
            <span class="material-symbols-outlined text-indigo-400 text-sm mt-0.5">lock</span>
            <p class="text-[10px] text-slate-400 leading-normal">
              Your details are protected under standard AES-256 secure sandbox rules. Feel free to use Stripe's standard test card <code class="text-indigo-400 font-mono">4242...</code>.
            </p>
          </div>

          <button type="submit" :disabled="isPaying"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm py-4 rounded-xl shadow-lg hover:shadow-indigo-600/10 transition-all disabled:opacity-50 flex items-center justify-center gap-2 mt-4">
            <span v-if="isPaying" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ isPaying ? 'Authorizing Card...' : `Pay secure ${currency}${amount}` }}
          </button>
        </form>
      </div>

      <!-- 3. SSLCommerz Simulation Form -->
      <div v-else-if="gateway === 'sslcommerz'" class="space-y-4">
        <!-- SSLCommerz Header branding -->
        <div class="bg-blue-600 rounded-2xl p-4 flex items-center justify-between text-white shadow-lg">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            <div class="text-left">
              <div class="text-xs font-bold uppercase tracking-wider opacity-90">EasyCheckout Aggregator</div>
              <div class="text-sm font-black">SSLCommerz Hosted Gateway</div>
            </div>
          </div>
          <span class="text-xs font-black bg-white/20 px-2.5 py-1 rounded">Sandbox</span>
        </div>

        <!-- Tab selection -->
        <div class="flex border-b border-slate-800 text-xs font-bold text-slate-400">
          <button type="button" @click="sslTab = 'cards'" :class="sslTab === 'cards' ? 'border-b-2 border-blue-500 text-white' : ''" class="flex-1 py-2.5 transition">Cards</button>
          <button type="button" @click="sslTab = 'mobile'" :class="sslTab === 'mobile' ? 'border-b-2 border-blue-500 text-white' : ''" class="flex-1 py-2.5 transition">Mobile Financials</button>
        </div>

        <form @submit.prevent="submitPayment" class="space-y-4 text-left">
          <!-- Cards Tab -->
          <div v-if="sslTab === 'cards'" class="space-y-3 pt-2">
            <div class="grid grid-cols-3 gap-2">
              <div class="bg-slate-950 border border-slate-850 hover:border-blue-500/50 rounded-xl p-2.5 flex flex-col items-center justify-center cursor-pointer transition">
                <span class="material-symbols-outlined text-xl text-blue-500">credit_card</span>
                <span class="text-[9px] font-black text-slate-400 mt-1">VISA</span>
              </div>
              <div class="bg-slate-950 border border-slate-850 hover:border-blue-500/50 rounded-xl p-2.5 flex flex-col items-center justify-center cursor-pointer transition">
                <span class="material-symbols-outlined text-xl text-orange-500">credit_card</span>
                <span class="text-[9px] font-black text-slate-400 mt-1">MASTERCARD</span>
              </div>
              <div class="bg-slate-950 border border-slate-850 hover:border-blue-500/50 rounded-xl p-2.5 flex flex-col items-center justify-center cursor-pointer transition">
                <span class="material-symbols-outlined text-xl text-emerald-500">credit_card</span>
                <span class="text-[9px] font-black text-slate-400 mt-1">DBBL NEXUS</span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Dummy card number</label>
              <input v-model="sslCardNumber" type="text" placeholder="e.g. 4242 4242 4242 4242"
                class="w-full bg-slate-950 border border-slate-850 focus:border-blue-500 text-white rounded-xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all font-mono" required>
            </div>
          </div>

          <!-- Mobile Tab -->
          <div v-else class="space-y-3 pt-2">
            <div class="grid grid-cols-3 gap-2">
              <div type="button" @click="sslMobileCarrier = 'bkash'" :class="sslMobileCarrier === 'bkash' ? 'border-pink-500 bg-pink-500/5' : 'border-slate-850 bg-slate-950'" class="border hover:border-pink-500/50 rounded-xl p-2.5 flex flex-col items-center justify-center cursor-pointer transition">
                <span class="material-symbols-outlined text-lg text-pink-500">smartphone</span>
                <span class="text-[9px] font-black text-slate-400 mt-1">bKash</span>
              </div>
              <div type="button" @click="sslMobileCarrier = 'nagad'" :class="sslMobileCarrier === 'nagad' ? 'border-orange-500 bg-orange-500/5' : 'border-slate-850 bg-slate-950'" class="border hover:border-orange-500/50 rounded-xl p-2.5 flex flex-col items-center justify-center cursor-pointer transition">
                <span class="material-symbols-outlined text-lg text-orange-500">smartphone</span>
                <span class="text-[9px] font-black text-slate-400 mt-1">Nagad</span>
              </div>
              <div type="button" @click="sslMobileCarrier = 'rocket'" :class="sslMobileCarrier === 'rocket' ? 'border-purple-500 bg-purple-500/5' : 'border-slate-850 bg-slate-950'" class="border hover:border-purple-500/50 rounded-xl p-2.5 flex flex-col items-center justify-center cursor-pointer transition">
                <span class="material-symbols-outlined text-lg text-purple-500">smartphone</span>
                <span class="text-[9px] font-black text-slate-400 mt-1">Rocket</span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Dummy Mobile Number</label>
              <input v-model="sslMobileNumber" type="text" placeholder="e.g. 01XXXXXXXXX"
                class="w-full bg-slate-950 border border-slate-850 focus:border-blue-500 text-white rounded-xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all font-mono" required>
            </div>
          </div>

          <button type="submit" :disabled="isPaying"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black text-sm py-4 rounded-xl shadow-lg hover:shadow-blue-600/10 transition-all disabled:opacity-50 flex items-center justify-center gap-2 mt-4">
            <span v-if="isPaying" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ isPaying ? 'Verifying payment aggregator...' : `Pay secure ${currency}${amount}` }}
          </button>
        </form>
      </div>

      <!-- Cancel transaction link -->
      <a href="/menu?payment_cancelled=true" class="block text-center text-xs text-slate-500 hover:text-slate-400 font-bold transition-all mt-6">
        Cancel transaction and return to store
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  gateway: String,
  orderId: [Number, String],
  orderNumber: String,
  amount: [Number, String],
  currency: String,
  callbackUrl: String,
})

// UI state
const isPaying = ref(false)

// bKash steps & input fields
const bkashStep = ref(1)
const walletNumber = ref('')
const otpCode = ref('')
const pinCode = ref('')

// Stripe input fields
const cardName = ref('')
const cardNumber = ref('')
const cardExpiry = ref('')
const cardCvc = ref('')

// SSLCommerz input fields
const sslTab = ref('cards')
const sslCardNumber = ref('')
const sslMobileCarrier = ref('bkash')
const sslMobileNumber = ref('')

const submitPayment = () => {
  isPaying.value = true
  
  // Simulate network transaction latency (1.5 seconds)
  setTimeout(() => {
    // Generate secure redirect payload pointing to callback controller
    let redirectDestination = props.callbackUrl
    
    if (props.gateway === 'bkash') {
      redirectDestination += `?status=success&paymentID=mock_bkash_${Math.random().toString(36).substring(7)}&order_id=${props.orderId}&amount=${props.amount}`
    } else if (props.gateway === 'stripe') {
      redirectDestination += `?session_id=mock_session_${Math.random().toString(36).substring(7)}&order_id=${props.orderId}&amount=${props.amount}`
    } else if (props.gateway === 'sslcommerz') {
      redirectDestination += `?status=success&val_id=mock_val_${Math.random().toString(36).substring(7)}&order_id=${props.orderId}&amount=${props.amount}`
    }
    
    // Perform redirection
    window.location.href = redirectDestination
  }, 1500)
}
</script>

<style scoped>
/* Chrome, Safari, Edge, Opera: remove number arrows */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
