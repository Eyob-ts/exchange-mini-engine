<script setup lang="ts">
import { useAuthStore } from '../stores/auth'
import OrderBook from '../components/trading/OrderBook.vue'
import TradeForm from '../components/trading/TradeForm.vue'
import Wallet from '../components/portfolio/Wallet.vue'
import OrderHistory from '../components/orders/OrderHistory.vue'
import Button from '../components/ui/Button.vue'

const auth = useAuthStore()
</script>

<template>
  <div class="flex flex-col h-screen bg-slate-900 text-slate-200 overflow-hidden">
    <!-- Header -->
    <header class="h-16 border-b border-slate-700/50 bg-slate-800/50 flex items-center justify-between px-6 z-10 shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-500 bg-opacity-20 flex items-center justify-center text-emerald-400 font-black text-lg shadow-lg shadow-emerald-500/10">
                E
            </div>
            <h1 class="font-bold text-lg bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-cyan-400">Exchange Mini</h1>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="text-sm text-slate-400">
                Logged in as <span class="text-slate-200 font-medium">{{ auth.user?.name || 'Trader' }}</span>
            </div>
            <Button variant="ghost" class="!px-3 !py-1 text-sm" @click="auth.logout">
                Sign Out
            </Button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-hidden p-4 grid grid-cols-12 gap-4">
        
        <!-- Left Column: Order Book (3/12) -->
        <div class="col-span-3 h-full overflow-hidden">
            <OrderBook />
        </div>

        <!-- Center Column: Chart & History (6/12) -->
        <div class="col-span-6 flex flex-col gap-4 h-full overflow-hidden">
            <!-- Chart Placeholder -->
            <div class="h-2/3 bg-slate-800 rounded-xl border border-slate-700/50 relative overflow-hidden group">
                 <div class="absolute inset-0 bg-gradient-to-b from-slate-800 to-slate-900 flex items-center justify-center">
                    <div class="text-center">
                        <h3 class="text-slate-500 font-mono text-xs mb-2">BTC/USD</h3>
                        <div class="text-4xl font-bold text-slate-700">CHART_VIEW</div>
                        <p class="text-slate-600 text-xs mt-2">TradingView Integration Coming Soon</p>
                    </div>
                </div>
                <!-- Svg Decor -->
                 <svg class="absolute bottom-0 left-0 right-0 h-32 w-full text-emerald-500/10" viewBox="0 0 100 20" preserveAspectRatio="none">
                    <path fill="currentColor" d="M0 20 L0 10 Q 20 5 40 15 T 80 10 T 100 15 L 100 20 Z" />
                    <path fill="none" stroke="currentColor" stroke-width="0.2" d="M0 10 Q 20 5 40 15 T 80 10 T 100 15" />
                </svg>
            </div>

            <!-- Order History -->
            <div class="h-1/3 overflow-hidden">
                <OrderHistory />
            </div>
        </div>

        <!-- Right Column: Trade & Wallet (3/12) -->
        <div class="col-span-3 flex flex-col gap-4 h-full overflow-y-auto custom-scrollbar pr-2">
            <TradeForm />
            <Wallet />
        </div>

    </main>
  </div>
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
