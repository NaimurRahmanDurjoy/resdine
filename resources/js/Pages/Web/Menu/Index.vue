<template>
  <WebLayout>
    <template #nav-actions>
      <!-- v-show ব্যবহার করার জন্য ক্লিক হ্যান্ডলার চেঞ্জ করার প্রয়োজন নেই, জাস্ট ফ্ল্যাগ টগল হবে -->
      <button @click="isCartOpen = true"
        class="relative bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 md:px-6 py-2.5 rounded-full font-black shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40 transition-all flex items-center gap-2 tracking-wide group">
        <span class="material-symbols-outlined text-[20px] transition-transform group-hover:scale-110">shopping_basket</span>
        <span class="hidden md:inline">My Basket</span>

        <span v-if="cartTotalItems > 0"
          class="absolute -top-1.5 -right-1.5 bg-slate-950 text-amber-400 text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-black shadow-sm ring-2 ring-amber-500">
          {{ cartTotalItems }}
        </span>
      </button>
    </template>

    <!-- Luxury Hero Section -->
    <div class="relative bg-slate-950 overflow-hidden min-h-[50vh] md:min-h-[70vh] flex items-center">
      <div class="absolute inset-0 z-0 opacity-25">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(#334155_1px,transparent_1px)] [background-size:24px_24px]"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-amber-500/20 rounded-full mix-blend-screen filter blur-[120px] animate-blob"></div>
        <div class="absolute bottom-10 -left-40 w-[500px] h-[500px] bg-orange-600/10 rounded-full mix-blend-screen filter blur-[120px] animate-blob animation-delay-2000"></div>
      </div>

      <div class="max-w-7xl mx-auto px-4 relative z-10 w-full py-16 md:py-24 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-amber-400 font-bold tracking-widest uppercase text-xs mb-6 animate-fade-in">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
          Freshly Prepared • Fast Delivery 
        </div>
        <h1 class="text-4xl sm:text-6xl md:text-7xl font-black leading-[0.95] tracking-tight text-white max-w-6xl mx-auto">
            Experience
            <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-orange-400 to-yellow-300">
                Delicious Food & Fresh Flavors.
            </span>
        </h1>

        <p class="max-w-3xl mx-auto text-lg md:text-xl text-slate-400 my-7">
            Discover handcrafted dishes, reserve your table, or order your favorites—all in just a few clicks.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <a href="#menu-section" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-8 py-4 rounded-full shadow-xl shadow-amber-500/10 hover:shadow-amber-500/30 transition-all transform hover:-translate-y-0.5">
            Explore Full Menu
          </a>
          <a href="#features-section" class="bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 font-bold px-8 py-4 rounded-full transition-all">
            Our Story
          </a>
        </div>
      </div>
    </div>

    <!-- Core Features Section -->
    <div id="features-section" class="bg-white py-12 md:py-20 border-b border-slate-100">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
          <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-100/50">
            <div class="p-3 bg-amber-500/10 text-amber-600 rounded-xl shrink-0">
              <span class="material-symbols-outlined text-3xl block">restaurant_menu</span>
            </div>
            <div>
              <h4 class="font-black text-lg text-slate-900 mb-1">Premium Ingredients</h4>
              <p class="text-slate-500 text-sm leading-relaxed">We source organic, local, and premium global ingredients to craft zero-compromise dishes.</p>
            </div>
          </div>
          <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-100/50">
            <div class="p-3 bg-amber-500/10 text-amber-600 rounded-xl shrink-0">
              <span class="material-symbols-outlined text-3xl block">skillet</span>
            </div>
            <div>
              <h4 class="font-black text-lg text-slate-900 mb-1">Master Chef Crafted</h4>
              <p class="text-slate-500 text-sm leading-relaxed">Every recipe is engineered by culinary veterans who live to redefine modern gastronomy.</p>
            </div>
          </div>
          <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-100/50">
            <div class="p-3 bg-amber-500/10 text-amber-600 rounded-xl shrink-0">
              <span class="material-symbols-outlined text-3xl block">speed</span>
            </div>
            <div>
              <h4 class="font-black text-lg text-slate-900 mb-1">Seamless Digital Flow</h4>
              <p class="text-slate-500 text-sm leading-relaxed">Order for instant Dine-In, quick Takeaway, or blazing fast Home Delivery with zero friction.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 🌟 OPTIMIZED HORIZONTAL LUXURY SLIDER -->
    <div v-if="featuredItems && featuredItems.length > 0" class="bg-slate-950 py-10 md:py-16 border-b border-slate-900 relative overflow-hidden layer-accelerated">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-amber-500/5 rounded-full filter blur-[100px] pointer-events-none"></div>

      <div class="max-w-7xl mx-auto relative">
        <div class="flex items-end justify-between mb-6 md:mb-8 px-4 sm:px-6 lg:px-8">
          <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 font-bold tracking-widest uppercase text-[10px] mb-2">
              <span class="material-symbols-outlined text-xs fill-current">star</span> Chef's Curation
            </span>
            <h2 class="text-2xl md:text-4xl font-black text-white tracking-tight">The Chef's Signature</h2>
            <p class="text-slate-400 text-xs mt-1 font-light">আমাদের এক্সক্লুসিভ সিগনেচার ডিশগুলো ডানে-বামে স্ক্রোল করে দেখুন।</p>
          </div>
          
          <div class="hidden md:flex items-center gap-2">
            <button @click="scrollFeatured(-340)" class="w-10 h-10 rounded-full border border-slate-800 bg-slate-900/50 text-slate-400 hover:text-amber-400 hover:border-amber-500/50 transition-all flex items-center justify-center active:scale-95">
              <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            <button @click="scrollFeatured(340)" class="w-10 h-10 rounded-full border border-slate-800 bg-slate-900/50 text-slate-400 hover:text-amber-400 hover:border-amber-500/50 transition-all flex items-center justify-center active:scale-95">
              <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
          </div>
        </div>

        <!-- GPU Accelerated Scroll Container -->
        <div ref="featuredContainer" class="flex gap-4 md:gap-6 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory pb-6 px-4 sm:px-6 lg:px-8 gpu-scroll">
          <div v-for="item in featuredItems" :key="item.id" 
            class="group bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800/80 rounded-[2rem] p-4 md:p-5 flex flex-col justify-between transition-all duration-300 hover:border-amber-500/40 hover:shadow-[0_20px_50px_rgba(245,158,11,0.08)] relative shrink-0 w-[260px] sm:w-[300px] md:w-[320px] snap-center hardware-card">
            
            <div class="absolute top-4 right-4 z-10 bg-gradient-to-r from-amber-400 to-orange-500 text-slate-950 text-[9px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
              Must Try
            </div>

            <div>
              <!-- ⚡ Image Optimization: loading="lazy" & decoding="async" -->
              <div class="relative h-36 md:h-40 w-full bg-slate-900 rounded-2xl overflow-hidden mb-3 md:mb-4 border border-slate-800/40">
                <img v-if="item.image_url" :src="item.image_url" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-700 bg-gradient-to-br from-slate-900 to-slate-950">
                  <span class="material-symbols-outlined text-4xl opacity-30 text-amber-500 mb-1">restaurant</span>
                  <span class="text-[9px] uppercase font-bold tracking-widest opacity-25">Signature</span>
                </div>
              </div>

              <span class="text-[9px] font-black text-amber-500/90 uppercase tracking-widest">{{ item.category?.name || 'Exclusive' }}</span>
              <h3 class="font-black text-white text-sm md:text-lg mt-0.5 truncate transition-colors group-hover:text-amber-400">
                {{ item.name }}
              </h3>
              <p class="text-slate-400 text-[11px] md:text-xs mt-1 line-clamp-2 leading-relaxed font-light">
                {{ item.description || 'Experience a unique fusion of premium ingredients engineered for culinary excellence.' }}
              </p>
            </div>

            <div class="flex items-center justify-between pt-3 md:pt-4 mt-3 md:mt-4 border-t border-slate-800/60">
              <div>
                <span class="text-[9px] font-bold text-slate-500 block uppercase">Price</span>
                <span class="font-black text-amber-400 text-base md:text-lg tracking-tight">
                  {{ currency() }}{{ item.variants?.[0]?.price || item.price }}
                </span>
              </div>
              
              <template v-if="availabilityMap[item.id]">
                <button @click="addToCart(item)" 
                  class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-3.5 py-2 md:px-4 md:py-2.5 rounded-xl font-black text-[11px] md:text-xs transition-all flex items-center gap-1 active:scale-95">
                  <span class="material-symbols-outlined text-xs md:text-sm">add_shopping_cart</span>
                  <span>Order</span>
                </button>
              </template>
              <template v-else>
                <button disabled class="bg-slate-900 text-slate-600 px-3.5 py-2 md:px-4 md:py-2.5 rounded-xl font-bold text-[11px] md:text-xs cursor-not-allowed opacity-50 flex items-center gap-1">
                  <span class="material-symbols-outlined text-xs md:text-sm">inventory_2</span>
                  <span>Sold Out</span>
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Menu Anchor Section -->
    <div id="menu-section" class="scroll-mt-28 py-12 md:py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 text-center mb-10">
        <span class="text-amber-600 font-bold tracking-widest uppercase text-xs block mb-2">Our Culinary Vault</span>
        <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Browse Our Live Menu</h2>
      </div>

      <!-- Sticky Category Navigation -->
      <div class="sticky top-14 md:top-16 z-40 bg-white/95 backdrop-blur-md border-y border-slate-100 shadow-sm transition-all py-3 mb-8">
        <div class="max-w-7xl mx-auto px-2 md:px-4 relative">
          <div class="hidden md:flex absolute inset-y-0 left-0 items-center pl-1 z-10">
            <button type="button" @click="scrollCategory(-280)" class="p-2 rounded-full bg-white shadow-md text-slate-600 hover:text-slate-900 transition"><span class="material-symbols-outlined">chevron_left</span></button>
          </div>

          <!-- Passive Wheel Scroll added -->
          <div ref="categoryNav" class="overflow-x-auto no-scrollbar scroll-smooth px-4 md:px-6 gpu-scroll" @wheel="onCategoryWheel" tabindex="0" role="group" aria-label="Category navigation">
            <div class="flex space-x-2 w-max py-1">
              <button @click="activeCategory = null"
                :class="[activeCategory === null ? 'bg-slate-950 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
                class="px-6 py-2.5 rounded-full font-black text-xs md:text-sm transition-all whitespace-nowrap">
                All Collections
              </button>
              <button v-for="cat in categories" :key="cat.id" @click="activeCategory = cat.id"
                :class="[activeCategory === cat.id ? 'bg-slate-950 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
                class="px-6 py-2.5 rounded-full font-black text-xs md:text-sm transition-all whitespace-nowrap">
                {{ cat.name }}
              </button>
            </div>
          </div>

          <div class="hidden md:flex absolute inset-y-0 right-0 items-center pr-1 z-10">
            <button type="button" @click="scrollCategory(280)" class="p-2 rounded-full bg-white shadow-md text-slate-600 hover:text-slate-900 transition"><span class="material-symbols-outlined">chevron_right</span></button>
          </div>
        </div>
      </div>

      <!-- Menu Grid -->
      <div class="max-w-7xl mx-auto px-3 md:px-4">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-8">
          <div v-for="item in filteredItems" :key="item.id"
            class="group bg-white rounded-2xl md:rounded-[2.5rem] shadow-sm hover:shadow-xl border border-slate-100 overflow-hidden transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col hardware-card">
            
            <!-- ⚡ Main Menu Images Optimized -->
            <div class="relative h-32 sm:h-40 md:h-56 bg-slate-50 overflow-hidden rounded-t-2xl md:rounded-t-[2.5rem]">
              <img v-if="item.image_url" :src="item.image_url" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
              <div v-else class="w-full h-full flex items-center justify-center text-slate-300 bg-gradient-to-br from-slate-50 to-slate-100">
                <span class="material-symbols-outlined text-4xl md:text-6xl opacity-40">fastfood</span>
              </div>
              <div class="absolute bottom-3 right-3 md:bottom-5 md:right-5 bg-slate-950 text-amber-400 font-black px-3 py-1 md:px-4 md:py-1.5 rounded-xl shadow-lg text-xs md:text-base">
                {{ currency() }}{{ item.price }}
              </div>
            </div>

            <div class="p-3 md:p-6 flex-1 flex flex-col justify-between gap-3">
              <div>
                <h3 class="font-black text-sm md:text-xl text-slate-900 mb-1 leading-tight line-clamp-1 group-hover:text-amber-600 transition-colors">{{ item.name }}</h3>
                <p class="hidden md:block text-slate-500 text-xs md:text-sm line-clamp-2 leading-relaxed">{{ item.description || 'Deliciously prepared using the freshest ingredients.' }}</p>
              </div>

              <template v-if="availabilityMap[item.id]">
                <button @click="addToCart(item)"
                  class="w-full bg-slate-50 hover:bg-slate-950 text-slate-900 hover:text-white border border-slate-200 hover:border-slate-950 py-2.5 md:py-3.5 rounded-xl md:rounded-2xl font-black text-xs md:text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-inner">
                  <span class="material-symbols-outlined text-base md:text-lg">add_shopping_cart</span>
                  <span>Add to Order</span>
                </button>
              </template>
              <template v-else>
                <button disabled class="w-full bg-slate-100 text-slate-400 border border-slate-200 py-2.5 md:py-3.5 rounded-xl md:rounded-2xl font-bold text-xs md:text-sm cursor-not-allowed flex items-center justify-center gap-1.5 opacity-60">
                  <span class="material-symbols-outlined text-base md:text-lg">inventory_2</span>
                  <span>Sold Out</span>
                </button>
              </template>
            </div>
          </div>
        </div>

        <div v-if="filteredItems.length === 0" class="py-20 text-center">
          <span class="material-symbols-outlined text-6xl text-slate-200 mb-4 inline-block">search_off</span>
          <h2 class="text-xl font-bold text-slate-700">No dishes inside this collection</h2>
          <p class="text-slate-500 mt-1 text-sm">Looks like this category is currently empty.</p>
        </div>
      </div>
    </div>

    <!-- Luxury Review Section -->
    <div class="bg-slate-950 text-white py-16 md:py-24 relative overflow-hidden">
      <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
        <span class="text-amber-400 font-bold tracking-widest uppercase text-xs block mb-4">Critique & Reviews</span>
        <h2 class="text-3xl md:text-6xl font-black tracking-tight mb-12">What Connoisseurs Say</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
          <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl relative">
            <span class="text-6xl text-slate-800 font-serif absolute top-4 right-6 pointer-events-none">“</span>
            <div class="flex gap-1 mb-4 text-amber-400">
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
            </div>
            <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-6 font-light">"The Dine-In system via ResDine was flawless. We scanned, ordered, and our steaks arrived precisely cooked within minutes. The signature dishes are a work of art."</p>
            <div class="font-bold text-white text-sm">Naimur Rahman <span class="text-slate-500 font-normal ml-1">— Food Critic</span></div>
          </div>
          <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl relative">
            <span class="text-6xl text-slate-800 font-serif absolute top-4 right-6 pointer-events-none">“</span>
            <div class="flex gap-1 mb-4 text-amber-400">
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
              <span class="material-symbols-outlined fill-current text-sm">star</span>
            </div>
            <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-6 font-light">"Blown away by the speed of their delivery checkout. Order processing is lightning fast, and the packaging kept the food perfectly fresh and warm."</p>
            <div class="font-bold text-white text-sm">Sarah Jenkins <span class="text-slate-500 font-normal ml-1">— Verified Patron</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Premium Footer Section -->
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 text-sm py-12">
      <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div>
          <div class="text-white font-black text-xl mb-4 tracking-tight flex items-center gap-1">
            <span class="material-symbols-outlined text-amber-500">restaurant</span> ResDine
          </div>
          <p class="text-slate-500 leading-relaxed text-xs">Automating next-generation restaurant operations, from contactless dine-in architectures to high-velocity delivery streams.</p>
        </div>
        <div>
          <h5 class="text-white font-bold mb-4 uppercase text-xs tracking-wider">Operation Hours</h5>
          <ul class="space-y-2 text-xs">
            <li>Sunday – Thursday: 11:00 AM – 11:00 PM</li>
            <li>Friday – Saturday: 10:00 AM – 12:00 AM</li>
          </ul>
        </div>
        <div>
          <h5 class="text-white font-bold mb-4 uppercase text-xs tracking-wider">Legal</h5>
          <ul class="space-y-2 text-xs">
            <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
            <li><a href="#" class="hover:text-white transition">Privacy Architecture</a></li>
            <li><a href="#" class="hover:text-white transition">Refund Operations</a></li>
          </ul>
        </div>
        <div>
          <h5 class="text-white font-bold mb-4 uppercase text-xs tracking-wider">Support Matrix</h5>
          <p class="text-xs text-slate-500 leading-relaxed mb-2">Need real-time corporate assistance or tech queries?</p>
          <div class="text-amber-400 font-bold">support@resdine.com</div>
        </div>
      </div>
    </footer>

    <!-- Sticky bottom Mobile View Cart Bar -->
    <Transition name="slide-up">
      <button v-show="cartTotalItems > 0 && !isCartOpen" @click="isCartOpen = true"
        class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-slate-950 text-white px-5 py-4 flex items-center justify-between shadow-[0_-8px_40px_rgba(0,0,0,0.4)] border-t border-slate-800 layer-accelerated"
        style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
        <span class="flex items-center gap-2.5 font-black text-sm">
          <span class="bg-amber-500 text-slate-950 text-xs w-6 h-6 rounded-full flex items-center justify-center font-black">{{ cartTotalItems }}</span>
          View Basket
        </span>
        <span class="font-black text-amber-400 text-base">{{ currency() }}{{ cartTotal.toFixed(2) }}</span>
      </button>
    </Transition>

    <!-- ⚡ OPTIMIZED: Sliding Cart Overlay using v-show to avoid layout thrashing -->
    <div v-show="isCartOpen" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
      <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity" @click="closeCart"></div>

      <div class="fixed inset-y-0 right-0 w-full md:max-w-md flex">
        <div class="w-full transform transition-transform duration-500 ease-in-out shadow-2xl layer-accelerated">
          <div class="h-full flex flex-col bg-white shadow-xl md:rounded-l-[2.5rem] overflow-hidden">

            <!-- Cart Header -->
            <div class="px-6 py-5 bg-white border-b border-slate-100 flex items-center justify-between z-10 sticky top-0">
              <div class="flex items-center gap-2">
                <button v-show="checkoutStep === 2" @click="checkoutStep = 1" class="text-slate-400 hover:text-slate-800 p-1 -ml-1 rounded-full transition">
                  <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                  <span class="material-symbols-outlined text-amber-500">{{ checkoutStep === 1 ? 'shopping_bag' : 'receipt_long' }}</span>
                  {{ checkoutStep === 1 ? 'Your Basket' : 'Checkout details' }}
                </h2>
              </div>
              <button @click="closeCart" class="text-slate-400 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 p-2 rounded-full transition-all">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>

            <!-- Cart Body Container -->
            <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 relative no-scrollbar">
              <div v-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-center opacity-40 space-y-3">
                <span class="material-symbols-outlined text-6xl block text-slate-400">lunch_dining</span>
                <p class="text-base font-black">Your basket is completely empty.</p>
              </div>

              <!-- STEP 1: Review items -->
              <div v-show="cart.length > 0 && checkoutStep === 1" class="space-y-3">
                <div v-for="(item, index) in cart" :key="index"
                  class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100/60 flex items-center gap-4 asset-card">
                  <img v-if="item.product.image_url" :src="item.product.image_url" loading="lazy" class="w-16 h-16 rounded-xl object-cover shadow-inner shrink-0" />
                  <div v-else class="w-16 h-16 rounded-xl bg-slate-100 flex shrink-0 items-center justify-center"><span class="material-symbols-outlined text-slate-300">restaurant</span></div>

                  <div class="flex-1 min-w-0">
                    <h4 class="font-black text-sm md:text-base text-slate-900 truncate">
                      {{ item.product.name }}
                      <span v-if="item.variant_name" class="text-xs text-slate-500 font-normal ml-1 border-l pl-1 border-slate-300">{{ item.variant_name }}</span>
                    </h4>
                    <div class="text-amber-600 font-black text-sm md:text-base">{{ currency() }}{{ (item.price * item.quantity).toFixed(2) }}</div>
                  </div>

                  <div class="flex flex-col items-center bg-slate-50 rounded-xl p-1 border border-slate-200/60 shadow-inner shrink-0">
                    <button @click="updateQuantity(index, 1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"><span class="material-symbols-outlined text-[16px]">add</span></button>
                    <span class="font-black text-sm my-0.5">{{ item.quantity }}</span>
                    <button @click="updateQuantity(index, -1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"><span class="material-symbols-outlined text-[16px]">remove</span></button>
                  </div>
                </div>
              </div>

              <!-- STEP 2: Checkout Form -->
              <div v-show="cart.length > 0 && checkoutStep === 2" class="space-y-4">
                <div>
                  <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Select Branch</label>
                  <select v-model.number="selectedBranch" @change="form.branch_id = selectedBranch; form.table_number = ''"
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                    <option value="">Choose a Branch</option>
                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                  </select>
                </div>

                <div class="flex p-1 bg-slate-200/60 rounded-xl">
                  <button @click="form.order_type = 1; form.payment_method = null; form.table_number = ''" :class="form.order_type === 1 ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 font-medium'" class="flex-1 py-2 text-xs rounded-lg transition-all">Dine In</button>
                  <button @click="form.order_type = 2" :class="form.order_type === 2 ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 font-medium'" class="flex-1 py-2 text-xs rounded-lg transition-all">Takeaway</button>
                  <button @click="form.order_type = 3" :class="form.order_type === 3 ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 font-medium'" class="flex-1 py-2 text-xs rounded-lg transition-all">Delivery</button>
                </div>

                <div v-show="form.order_type === 1">
                  <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1">Table Number</label>
                  <select v-model="form.table_number" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                    <option value="">Select Your Table</option>
                    <option v-for="table in filteredTables" :key="table.id" :value="table.name">{{ table.name }}</option>
                  </select>
                </div>

                <div v-show="form.order_type === 3" class="space-y-3">
                  <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1">Delivery Address *</label>
                    <textarea v-model="form.delivery_address" placeholder="Enter your full street address, flat no., etc." rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500 transition shadow-sm resize-none"></textarea>
                  </div>
                  <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1">Delivery Instructions</label>
                    <input v-model="form.delivery_instructions" type="text" placeholder="e.g. Leave at door" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500 shadow-sm">
                  </div>
                </div>

                <div v-show="form.order_type === 2 || form.order_type === 3">
                  <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Payment Method</label>
                  <div class="grid grid-cols-2 gap-2">
                    <button @click="form.payment_method = 1" type="button" :class="form.payment_method === 1 ? 'bg-slate-950 text-white border-slate-950' : 'bg-white text-slate-600 border-slate-200'" class="flex items-center justify-center gap-1.5 py-3 rounded-xl border font-bold text-xs transition-all shadow-sm"><span class="material-symbols-outlined text-sm">payments</span> Cash</button>
                    <button @click="form.payment_method = 3" type="button" :class="form.payment_method === 3 ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-slate-600 border-slate-200'" class="flex items-center justify-center gap-1.5 py-3 rounded-xl border font-bold text-xs transition-all shadow-sm"><span class="material-symbols-outlined text-sm">smartphone</span> bKash</button>
                    <button @click="form.payment_method = 2" type="button" :class="form.payment_method === 2 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200'" class="flex items-center justify-center gap-1.5 py-3 rounded-xl border font-bold text-xs transition-all shadow-sm"><span class="material-symbols-outlined text-sm">credit_card</span> Stripe</button>
                    <button @click="form.payment_method = 4" type="button" :class="form.payment_method === 4 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200'" class="flex items-center justify-center gap-1.5 py-3 rounded-xl border font-bold text-xs transition-all shadow-sm"><span class="material-symbols-outlined text-sm">account_balance_wallet</span> SSLCommerz</button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1">Your Name *</label>
                  <input v-model="form.customer_name" type="text" placeholder="Please enter your name" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500 shadow-sm">
                </div>

                <div>
                  <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1">Phone Number *</label>
                  <input v-model="form.customer_phone" type="tel" placeholder="Please enter your phone number" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500 shadow-sm">
                </div>
              </div>
            </div>

            <!-- Cart Footer Summary -->
            <div v-show="cart.length > 0" class="bg-white p-6 border-t border-slate-100 z-10 shadow-[0_-10px_35px_rgba(0,0,0,0.05)] space-y-3" style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
              <div class="flex justify-between items-center text-xs text-slate-500">
                <span>Subtotal</span>
                <span class="font-bold text-slate-800">{{ currency() }}{{ cartSubtotal.toFixed(2) }}</span>
              </div>
              <div v-show="cartVatAmount > 0" class="flex justify-between items-center text-xs text-slate-500">
                <span>VAT ({{ branchSetting.vat_percentage }}%{{ branchSetting.is_vat_inclusive ? ' Incl.' : ' Excl.' }})</span>
                <span class="font-bold text-slate-800">{{ currency() }}{{ cartVatAmount.toFixed(2) }}</span>
              </div>
              <div v-show="cartServiceChargeAmount > 0" class="flex justify-between items-center text-xs text-slate-500">
                <span>Service Charge ({{ branchSetting.service_charge_percentage }}%)</span>
                <span class="font-bold text-slate-800">{{ currency() }}{{ cartServiceChargeAmount.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between items-center pt-3 border-t border-slate-100 mb-2">
                <span class="text-slate-900 font-black text-base">Grand Total</span>
                <span class="text-2xl font-black text-slate-950">{{ currency() }}{{ cartTotal.toFixed(2) }}</span>
              </div>

              <button v-show="checkoutStep === 1" @click="checkoutStep = 2"
                class="w-full bg-slate-950 text-white font-black text-base py-4 rounded-full shadow-xl shadow-slate-950/10 hover:bg-amber-500 hover:text-slate-950 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <span>Proceed to Checkout</span>
                <span class="material-symbols-outlined">arrow_right_alt</span>
              </button>

              <button v-show="checkoutStep === 2" @click="submitOrder" :disabled="isSubmitting"
                class="w-full bg-slate-950 text-white font-black text-base py-4 rounded-full shadow-xl shadow-slate-950/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined animate-spin" v-if="isSubmitting">autorenew</span>
                <span>{{ isSubmitting ? 'Processing Order...' : 'Confirm & Place Order' }}</span>
                <span class="material-symbols-outlined" v-if="!isSubmitting">check_circle</span>
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>

    <VariantSelectionModal :show="!!selectedProductForVariant" :product="selectedProductForVariant" theme="amber" @close="closeVariantModal" @selected="selectVariant" />
    <CampaignModal :activeCampaigns="activeCampaigns" />
  </WebLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import WebLayout from '@/Layouts/WebLayout.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import VariantSelectionModal from '@/Components/VariantSelectionModal.vue'
import CampaignModal from '@/Components/CampaignModal.vue'

const page = usePage()

const props = defineProps({
  categories: Array,
  items: Array,
  activeCampaigns: Array,
  featuredItems: Array,
  branchSetting: Object,
  availabilityMap: Object,
  resTables: Array,
  branches: Array,
  defaultBranchId: Number
})

const activeCategory = ref(null)
const isCartOpen = ref(false)
const checkoutStep = ref(1)
const cart = ref([])
const isSubmitting = ref(false)
const selectedProductForVariant = ref(null)
const categoryNav = ref(null)
const featuredContainer = ref(null)
const selectedBranch = ref(props.defaultBranchId || null)

const form = reactive({
  customer_name: '',
  customer_phone: '',
  order_type: 1,
  branch_id: props.defaultBranchId || null,
  table_number: '',
  payment_method: null,
  delivery_address: '',
  delivery_instructions: ''
})

const filteredItems = computed(() => activeCategory.value ? props.items.filter(i => i.category_id === activeCategory.value) : props.items)
const filteredTables = computed(() => selectedBranch.value ? props.resTables.filter(t => t.branch_id === selectedBranch.value) : [])
const cartTotalItems = computed(() => cart.value.reduce((acc, curr) => acc + curr.quantity, 0))
const cartSubtotal = computed(() => cart.value.reduce((acc, curr) => acc + (curr.price * curr.quantity), 0))
const cartVatAmount = computed(() => {
  const vatPercent = parseFloat(props.branchSetting?.vat_percentage || 0)
  if (vatPercent <= 0) return 0
  return props.branchSetting?.is_vat_inclusive ? cartSubtotal.value - (cartSubtotal.value / (1 + vatPercent / 100)) : cartSubtotal.value * (vatPercent / 100)
})
const cartServiceChargeAmount = computed(() => {
  const serviceChargePercent = parseFloat(props.branchSetting?.service_charge_percentage || 0)
  return serviceChargePercent <= 0 ? 0 : cartSubtotal.value * (serviceChargePercent / 100)
})
const cartTotal = computed(() => props.branchSetting?.is_vat_inclusive ? cartSubtotal.value + cartServiceChargeAmount.value : cartSubtotal.value + cartVatAmount.value + cartServiceChargeAmount.value)

const currency = () => page.props.currency_symbol || '$'

// ⚡ RequestAnimationFrame ব্যবহার করে ডম থ্রোটলিং (Smooth scroll animation trigger)
const scrollFeatured = (distance) => {
  requestAnimationFrame(() => {
    featuredContainer.value?.scrollBy({ left: distance, behavior: 'smooth' })
  })
}

const addToCart = (product) => {
  if (product.variants && product.variants.length > 0) { selectedProductForVariant.value = product; return }
  doAddToCart(product, null, null, product.price)
}
const selectVariant = (variant) => {
  doAddToCart(selectedProductForVariant.value, variant.id, variant.name, variant.price || selectedProductForVariant.value.price)
  closeVariantModal()
}
const closeVariantModal = () => { selectedProductForVariant.value = null }
const doAddToCart = (product, variantId, variantName, price) => {
  const existing = cart.value.find(i => i.item_id === product.id && i.variant_id === variantId)
  if (existing) { existing.quantity++ } else {
    cart.value.push({ item_id: product.id, product: product, price: parseFloat(price), quantity: 1, variant_id: variantId, variant_name: variantName })
  }
}
const updateQuantity = (index, delta) => {
  const item = cart.value[index]; const newQ = item.quantity + delta
  if (newQ > 0) { item.quantity = newQ } else {
    cart.value.splice(index, 1)
    if (cart.value.length === 0) { isCartOpen.value = false; checkoutStep.value = 1 }
  }
}
const closeCart = () => { isCartOpen.value = false; checkoutStep.value = 1 }

const scrollCategory = (distance) => {
  requestAnimationFrame(() => {
    categoryNav.value?.scrollBy({ left: distance, behavior: 'smooth' })
  })
}

// ⚡ passive true এবং ticking মেকানিজম ছাড়াই মাউস হুইল ইভেন্ট অপ্টিমাইজেশন
const onCategoryWheel = (event) => {
  event.preventDefault()
  requestAnimationFrame(() => {
    categoryNav.value?.scrollBy({ left: event.deltaY, behavior: 'auto' }) // হুইলে অটো স্মুথনেস বেটার পারফর্ম করে
  })
}

const submitOrder = async () => {
  if (cart.value.length === 0) return
  if (!selectedBranch.value) { Swal.fire('Required Fields', 'Please select a branch.', 'error'); return }
  if (!form.customer_name || !form.customer_phone) { Swal.fire('Required Fields', 'Please enter your name and phone number.', 'error'); return }
  if (form.order_type === 1 && !form.table_number) { Swal.fire('Required Fields', 'Please enter your table number for Dine In.', 'error'); return }
  if ((form.order_type === 2 || form.order_type === 3) && !form.payment_method) { Swal.fire('Required Fields', `Please select a payment method.`, 'error'); return }
  if (form.order_type === 3 && !form.delivery_address) { Swal.fire('Required Fields', 'Please enter a delivery address.', 'error'); return }

  const payload = {
    ...form, subtotal: cartSubtotal.value, vat_amount: cartVatAmount.value, service_charge_amount: cartServiceChargeAmount.value, total_amount: cartTotal.value,
    items: cart.value.map(i => ({ item_id: i.item_id, variant_id: i.variant_id, quantity: i.quantity, price: i.price }))
  }
  isSubmitting.value = true
  try {
    const res = await axios.post(route('web.order.submit'), payload)
    if (res.data.success) {
      isCartOpen.value = false
      if (res.data.redirect_url) { cart.value = []; window.location.href = res.data.redirect_url; return }
      Swal.fire({ title: 'Order Confirmed!', text: `Order #${res.data.order_number} success.`, icon: 'success', confirmButtonColor: '#f59e0b' }).then(() => {
        const orderNum = res.data.order_number; cart.value = []; checkoutStep.value = 1
        window.location.href = route('web.order.track', { orderNumber: orderNum })
      })
    }
  } catch (error) { Swal.fire('Oops...', error.response?.data?.message || 'Something went wrong!', 'error') } finally { isSubmitting.value = false }
}

onMounted(() => {
  const flash = page.props.flash || {}
  if (flash.success) { Swal.fire({ title: 'Success!', text: flash.success, icon: 'success', confirmButtonColor: '#f59e0b' }) }
  else if (flash.error) { Swal.fire({ title: 'Error!', text: flash.error, icon: 'error', confirmButtonColor: '#ef4444' }) }
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* ⚡ Hardware Layers Optimization */
.layer-accelerated {
  will-change: transform, opacity;
}
.gpu-scroll {
  will-change: scroll-position;
  -webkit-overflow-scrolling: touch;
}
.hardware-card {
  contain: content; /* ব্রাউজারকে অপ্রয়োজনীয় রি-পেইন্ট থেকে বিরত রাখে */
  backface-visibility: hidden;
  transform: translateZ(0);
}

@keyframes blob {
  0%, 100% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(40px, -60px) scale(1.15); }
  66% { transform: translate(-30px, 30px) scale(0.9); }
}
.animate-blob { animation: blob 10s infinite ease-in-out; }
.animation-delay-2000 { animation-delay: 3s; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-fade-in { animation: fadeInUp 0.3s ease-out forwards; }

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); opacity: 0; }
</style>