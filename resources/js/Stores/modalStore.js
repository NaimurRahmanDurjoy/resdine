import { reactive, shallowRef } from 'vue'

export const modalStore = reactive({
    isOpen: false,
    component: shallowRef(null),
    props: {},
    title: '',
    maxWidth: '2xl'
})

export function openModal(component, props = {}, options = {}) {
    modalStore.component = shallowRef(component)
    modalStore.props = props
    modalStore.title = options.title || ''
    modalStore.maxWidth = options.maxWidth || '2xl'
    modalStore.isOpen = true
}

export function closeModal() {
    modalStore.isOpen = false
    // Reset after transition
    setTimeout(() => {
        modalStore.component = null
        modalStore.props = {}
        modalStore.title = ''
        modalStore.maxWidth = '2xl'
    }, 300)
}
