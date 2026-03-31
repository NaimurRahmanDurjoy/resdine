<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-indigo-600">payments</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Process Payment
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div class="bg-gray-50 p-3 rounded-md flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Remaining Due:</span>
                                    <span class="text-xl font-bold text-red-600">${{ remainingDue.toFixed(2) }}</span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input 
                                            v-model.number="form.amount" 
                                            type="number" 
                                            step="0.01"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="0.00"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                    <select v-model="form.payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="1">Cash</option>
                                        <option value="2">Card</option>
                                        <option value="3">bKash (MFS)</option>
                                        <option value="4">Nagad (MFS)</option>
                                        <option value="5">Wallet</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Transaction Reference (Optional)</label>
                                    <input 
                                        v-model="form.transaction_reference" 
                                        type="text" 
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="TrxID / Receipt #"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button 
                        @click="submit"
                        :disabled="form.processing || form.amount <= 0"
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm disabled:bg-gray-400"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Pay Now</span>
                    </button>
                    <button 
                        @click="$emit('close')"
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    orderId: Number,
    remainingDue: Number
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    amount: props.remainingDue,
    payment_method: '1',
    transaction_reference: '',
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        form.amount = props.remainingDue;
        form.transaction_reference = '';
    }
});

const submit = () => {
    form.post(route('admin.orders.payments.store', props.orderId), {
        onSuccess: () => {
            emit('success');
            emit('close');
        }
    });
};
</script>
