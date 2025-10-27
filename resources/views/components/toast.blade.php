@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if(session($type))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            x-transition
            class="fixed top-6 right-6 z-50 max-w-sm w-full shadow-lg rounded-lg px-5 py-4 text-md 
                {{ $type === 'success' ? 'bg-green-400' : '' }}
                {{ $type === 'error' ? 'bg-red-400' : '' }}
                {{ $type === 'warning' ? 'bg-yellow-400' : '' }}
                {{ $type === 'info' ? 'bg-blue-400' : '' }}
                text-white flex items-center justify-between space-x-4"
        >
            <div class="flex items-center space-x-2">
                <span class="material-icons text-white text-base">
                    {{ 
                        $type === 'success' ? 'check_circle' : 
                        ($type === 'error' ? 'error' : 
                        ($type === 'warning' ? 'warning' : 'info')) 
                    }}
                </span>
                <span>{{ session($type) }}</span>
            </div>
            <button @click="show = false" class="text-white text-xl leading-none focus:outline-none">×</button>
        </div>
    @endif
@endforeach
