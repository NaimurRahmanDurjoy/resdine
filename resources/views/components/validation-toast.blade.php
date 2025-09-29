@if ($errors->any())
    <div 
        x-data="{ show: true }" 
        x-init="setTimeout(() => show = false, 5000)" 
        x-show="show"
        x-transition
        role="alert"
        aria-live="assertive"
        class="fixed top-5 right-5 max-w-sm w-full z-50 bg-red-600 text-white px-6 py-4 rounded shadow-lg flex flex-col space-y-2"
    >
        <div class="flex justify-between items-center">
            <div class="font-semibold text-lg">Validation Error</div>
            <button @click="show = false" aria-label="Close notification" class="text-white hover:text-red-300 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
