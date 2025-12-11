<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'danger' | 'ghost' | 'outline'
  block?: boolean
  disabled?: boolean
}>()
</script>

<template>
  <button
    :class="[
      'px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-2',
      variant === 'primary' || !variant ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : '',
      variant === 'danger' ? 'bg-red-500 hover:bg-red-600 text-white shadow-lg shadow-red-500/20' : '',
      variant === 'ghost' ? 'bg-transparent hover:bg-slate-700/50 text-slate-300 hover:text-white' : '',
      variant === 'outline' ? 'bg-transparent border border-slate-600 hover:border-slate-500 text-slate-300' : '',
      block ? 'w-full' : '',
      disabled ? 'opacity-50 cursor-not-allowed' : 'active:scale-95'
    ]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>
