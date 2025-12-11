<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Card from '../ui/Card.vue'
import Button from '../ui/Button.vue'
import api from '../../axios'

interface Order {
    id: number
    side: 'buy' | 'sell'
    price: number
    amount: number
    filled: number
    status: 'open' | 'filled' | 'cancelled'
    created_at: string
}

const orders = ref<Order[]>([])
const loading = ref(false)

const fetchOrders = async () => {
    loading.value = true
    try {
        const response = await api.get('/orders')
        orders.value = response.data
    } catch (err) {
        // Mock data for UI development
        orders.value = [
            { id: 1, side: 'buy', price: 44500, amount: 0.5, filled: 0, status: 'open', created_at: '2023-10-25 10:30' },
            { id: 2, side: 'sell', price: 45200, amount: 0.2, filled: 0.2, status: 'filled', created_at: '2023-10-24 14:15' },
            { id: 3, side: 'buy', price: 43000, amount: 1.0, filled: 0, status: 'cancelled', created_at: '2023-10-24 09:00' },
        ]
    } finally {
        loading.value = false
    }
}

const cancelOrder = async (id: number) => {
    try {
        await api.post(`/orders/${id}/cancel`)
        await fetchOrders()
    } catch (err) {
        alert('Failed to cancel order')
    }
}

onMounted(fetchOrders)
</script>

<template>
  <Card class="overflow-hidden !p-0">
    <div class="px-6 py-4 border-b border-slate-700/50">
        <h3 class="font-semibold text-slate-200">Order History</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-slate-400 bg-slate-800/50">
                <tr>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium">Side</th>
                    <th class="px-6 py-3 font-medium">Price</th>
                    <th class="px-6 py-3 font-medium">Amount</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-700/20 text-slate-300">
                    <td class="px-6 py-3 whitespace-nowrap text-slate-400">{{ order.created_at }}</td>
                    <td class="px-6 py-3">
                        <span :class="order.side === 'buy' ? 'text-emerald-400' : 'text-red-400 uppercase font-bold text-xs'">
                            {{ order.side.toUpperCase() }}
                        </span>
                    </td>
                    <td class="px-6 py-3 font-mono">${{ order.price.toLocaleString() }}</td>
                    <td class="px-6 py-3 font-mono">{{ order.amount }}</td>
                    <td class="px-6 py-3">
                        <span 
                            class="px-2 py-0.5 rounded text-xs font-medium border"
                            :class="{
                                'bg-emerald-500/10 text-emerald-400 border-emerald-500/20': order.status === 'filled',
                                'bg-blue-500/10 text-blue-400 border-blue-500/20': order.status === 'open',
                                'bg-slate-500/10 text-slate-400 border-slate-500/20': order.status === 'cancelled'
                            }"
                        >
                            {{ order.status.toUpperCase() }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <Button 
                            v-if="order.status === 'open'" 
                            variant="ghost" 
                            class="!p-1 text-xs text-red-400 hover:text-red-300"
                            @click="cancelOrder(order.id)"
                        >
                            Cancel
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
  </Card>
</template>
