<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useMarketStore } from '../../stores/market'
import Card from '../ui/Card.vue'

const marketStore = useMarketStore()

// Use mock data if empty for visualization
const bids = computed(() => marketStore.bids.length ? marketStore.bids : Array.from({ length: 10 }, (_, i) => ({
  price: 45000 - i * 50,
  amount: Math.random() * 2,
  total: 0
})))

const asks = computed(() => marketStore.asks.length ? marketStore.asks : Array.from({ length: 10 }, (_, i) => ({
  price: 45100 + i * 50,
  amount: Math.random() * 2,
  total: 0
})))

onMounted(() => {
  marketStore.fetchOrderBook()
  marketStore.connectWebSocket()
})

const maxTotal = computed(() => {
    // Mock max for depth bars
    return Math.max(
        ...bids.value.map(o => o.amount),
        ...asks.value.map(o => o.amount)
    ) * 1.5
})
</script>

<template>
  <Card class="h-full flex flex-col overflow-hidden !p-0">
    <div class="px-4 py-3 border-b border-slate-700/50">
        <h3 class="font-semibold text-slate-200">Order Book</h3>
    </div>
    
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <!-- Asks (Red) -->
        <div class="flex flex-col-reverse">
            <div 
                v-for="(ask, i) in asks" 
                :key="`ask-${i}`" 
                class="relative grid grid-cols-3 text-xs py-1 px-4 cursor-pointer hover:bg-slate-700/30"
            >
                <!-- Depth Bar -->
                <div 
                    class="absolute top-0 right-0 bottom-0 bg-red-500/10 transition-all duration-300"
                    :style="{ width: `${(ask.amount / maxTotal) * 100}%` }"
                ></div>
                
                <span class="relative text-red-400 font-mono">{{ ask.price.toLocaleString() }}</span>
                <span class="relative text-right text-slate-400 font-mono">{{ ask.amount.toFixed(4) }}</span>
                <span class="relative text-right text-slate-500 font-mono">{{ (ask.price * ask.amount).toLocaleString() }}</span>
            </div>
        </div>

        <!-- Spread -->
        <div class="py-2 px-4 bg-slate-800/50 border-y border-slate-700/50 text-center">
            <span class="text-emerald-400 text-lg font-mono font-bold">45,050.00</span>
            <span class="text-xs text-slate-500 ml-2">≈ $45,050.00</span>
        </div>

        <!-- Bids (Green) -->
        <div>
            <div 
                v-for="(bid, i) in bids" 
                :key="`bid-${i}`" 
                class="relative grid grid-cols-3 text-xs py-1 px-4 cursor-pointer hover:bg-slate-700/30"
            >
                <!-- Depth Bar -->
                <div 
                    class="absolute top-0 right-0 bottom-0 bg-emerald-500/10 transition-all duration-300"
                    :style="{ width: `${(bid.amount / maxTotal) * 100}%` }"
                ></div>

                <span class="relative text-emerald-400 font-mono">{{ bid.price.toLocaleString() }}</span>
                <span class="relative text-right text-slate-400 font-mono">{{ bid.amount.toFixed(4) }}</span>
                <span class="relative text-right text-slate-500 font-mono">{{ (bid.price * bid.amount).toLocaleString() }}</span>
            </div>
        </div>
    </div>
  </Card>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 4px;
}
</style>
