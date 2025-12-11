<script setup lang="ts">
defineProps<{
  glass?: boolean
}>()
</script>

<template>
  <div
    :class="[
      'rounded-xl border border-slate-700/50 p-6',
      glass ? 'bg-slate-800/50 backdrop-blur-md' : 'bg-slate-800'
    ]"
  >
    <slot />
  </div>
</template>
