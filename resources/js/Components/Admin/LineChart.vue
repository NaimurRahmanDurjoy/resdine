<template>
  <div class="bg-white p-6 rounded-xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
      <div class="text-lg font-semibold text-gray-800">{{ title }}</div>
      <slot name="controls"></slot>
    </div>
    <canvas ref="chartCanvas" :height="height"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  title: String,
  labels: Array,
  datasets: Array,
  options: Object,
  height: { type: Number, default: 250 }
})

const chartCanvas = ref(null)
let chartInstance = null

onMounted(() => renderChart())
watch([() => props.labels, () => props.datasets], () => renderChart(), { deep: true })

function renderChart() {
  if (chartInstance) chartInstance.destroy()
  chartInstance = new Chart(chartCanvas.value, {
    type: 'line',
    data: { labels: props.labels, datasets: props.datasets },
    options: props.options || { responsive: true }
  })
}
</script>