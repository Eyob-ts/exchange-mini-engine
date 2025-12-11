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
        async fetchOrderBook(symbol: string = 'BTCUSD') {
            this.loading = true
            try {
                const response = await api.get('/orderbook', { params: { symbol } })
                this.bids = response.data.bids
                this.asks = response.data.asks
            } catch (err) {
                console.error('Failed to fetch orderbook', err)
            } finally {
                this.loading = false
            }
        },

        connectWebSocket() {
            // Only connect if Echo is properly initialized
            if (!echo || !import.meta.env.VITE_REVERB_APP_KEY) {
                console.warn('WebSocket not available: Echo not initialized. Set VITE_REVERB_APP_KEY and VITE_REVERB_HOST to enable real-time updates.');
                return;
            }

            try {
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
            } catch (error) {
                console.error('Failed to connect WebSocket:', error);
            }
        }
    }
})
