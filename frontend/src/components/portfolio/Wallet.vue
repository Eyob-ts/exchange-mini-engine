<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import Card from '../ui/Card.vue'
import Button from '../ui/Button.vue'
import { PlusIcon } from '@heroicons/vue/24/solid'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

// Mock assets if not present in user object
const assets = computed(() => (user.value as any)?.assets || [
    { symbol: 'BTC', amount: 1.2456, value: 56052 },
    { symbol: 'ETH', amount: 4.5, value: 12500 },
])

const balance = computed(() => (user.value as any)?.balance || 12450.00)
</script>

<template>
  <Card class="flex flex-col gap-4">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-slate-400 text-sm font-medium">Total Balance</h3>
            <div class="text-3xl font-bold text-white mt-1">
                ${{ balance.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
            </div>
        </div>
        <Button variant="primary" class="!p-2">
            <PlusIcon class="w-5 h-5" />
        </Button>
    </div>

    <div class="h-px bg-slate-700/50"></div>

    <div class="space-y-3">
        <div v-for="asset in assets" :key="asset.symbol" class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-xs text-slate-300">
                    {{ asset.symbol }}
                </div>
                <div>
                    <div class="font-medium text-slate-200">{{ asset.symbol }}</div>
                    <div class="text-xs text-slate-400">{{ asset.amount }} {{ asset.symbol }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="font-medium text-slate-200">${{ asset.value.toLocaleString() }}</div>
            </div>
        </div>
    </div>
  </Card>
</template>
