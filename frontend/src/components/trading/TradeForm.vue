<script setup lang="ts">
import { ref, computed } from 'vue'
import Card from '../ui/Card.vue'
import Button from '../ui/Button.vue'
import Input from '../ui/Input.vue'
import api from '../../axios'
import { useMarketStore } from '../../stores/market'

const marketStore = useMarketStore()

const side = ref<'buy' | 'sell'>('buy')
const price = ref('')
const amount = ref('')
const loading = ref(false)

const total = computed(() => {
    const p = parseFloat(price.value) || 0
    const a = parseFloat(amount.value) || 0
    return p * a
})

const submitOrder = async () => {
    loading.value = true
    try {
        await api.post('/orders', {
            symbol: 'BTCUSD',
            side: side.value,
            price: parseFloat(price.value),
            amount: parseFloat(amount.value)
        })
        // Reset form
        price.value = ''
        amount.value = ''
        // Refresh order book to show the new order
        await marketStore.fetchOrderBook('BTCUSD')
        alert('Order placed successfully!')
    } catch (err: any) {
        const errorMessage = err.response?.data?.message || 
                           (err.response?.data?.errors ? JSON.stringify(err.response.data.errors) : null) ||
                           'Failed to place order'
        alert(errorMessage)
    } finally {
        loading.value = false
    }
}
</script>

<template>
  <Card class="flex flex-col gap-6">
    <!-- Tabs -->
    <div class="flex p-1 bg-slate-900/50 rounded-lg">
        <button 
            @click="side = 'buy'"
            :class="[
                'flex-1 py-2 rounded-md text-sm font-medium transition-all',
                side === 'buy' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-white'
            ]"
        >
            Buy
        </button>
        <button 
            @click="side = 'sell'"
            :class="[
                'flex-1 py-2 rounded-md text-sm font-medium transition-all',
                side === 'sell' ? 'bg-red-500 text-white shadow-lg shadow-red-500/20' : 'text-slate-400 hover:text-white'
            ]"
        >
            Sell
        </button>
    </div>

    <!-- Form -->
    <div class="space-y-4">
        <Input 
            v-model="price" 
            label="Price (USD)" 
            type="number" 
            placeholder="0.00" 
        />
        <Input 
            v-model="amount" 
            label="Amount (BTC)" 
            type="number" 
            placeholder="0.00" 
        />
        
        <!-- Summary -->
        <div class="flex justify-between text-sm py-2 px-1">
            <span class="text-slate-400">Total</span>
            <span class="text-slate-200 font-mono">{{ total.toLocaleString() }} USD</span>
        </div>

        <Button 
            :variant="side === 'buy' ? 'primary' : 'danger'" 
            block 
            class="mt-2"
            :disabled="loading"
            @click="submitOrder"
        >
            {{ loading ? 'Processing...' : (side === 'buy' ? 'Buy BTC' : 'Sell BTC') }}
        </Button>
    </div>
  </Card>
</template>
