import { defineStore } from 'pinia'
import api from '../axios'
import echo from '../echo'

interface Order {
    price: number
    amount: number
    total?: number
}

export const useMarketStore = defineStore('market', {
    state: () => ({
        bids: [] as Order[],
        asks: [] as Order[],
        trades: [] as any[],
        loading: false
    }),

    actions: {
        async fetchOrderBook() {
            this.loading = true
            try {
                const response = await api.get('/orderbook')
                this.bids = response.data.bids
                this.asks = response.data.asks
            } catch (err) {
                console.error('Failed to fetch orderbook', err)
            } finally {
                this.loading = false
            }
        },

        connectWebSocket() {
            echo.channel('market')
                .listen('.OrderMatched', (e: any) => {
                    console.log('Order Matched:', e);
                    // In a real app, we would update the orderbook intelligently here
                    // For now, we'll just re-fetch to keep it simple and accurate
                    this.fetchOrderBook();
                })
                .listen('.OrderPlaced', (_e: any) => {
                    this.fetchOrderBook();
                });
        }
    }
})
