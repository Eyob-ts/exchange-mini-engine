import { defineStore } from 'pinia'
import api from '../axios'
import router from '../router'

interface User {
    id: number
    name: string
    email: string
    // Add other user fields as needed
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        token: localStorage.getItem('token') || null as string | null,
        loading: false,
        error: null as string | null
    }),

    getters: {
        isAuthenticated: (state) => !!state.token
    },

    actions: {
        async login(credentials: any) {
            this.loading = true
            this.error = null
            try {
                const response = await api.post('/auth/login', credentials)
                this.token = response.data.token
                localStorage.setItem('token', this.token!)
                await this.fetchUser()
                router.push('/')
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Login failed'
                throw err
            } finally {
                this.loading = false
            }
        },

        async register(credentials: any) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/auth/register', credentials);
                this.token = response.data.token;
                localStorage.setItem('token', this.token!);
                await this.fetchUser();
                router.push('/');
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Registration failed';
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            if (!this.token) return
            try {
                const response = await api.get('/profile')
                this.user = response.data
            } catch (err) {
                this.logout()
            }
        },

        async logout() {
            this.token = null
            this.user = null
            localStorage.removeItem('token')
            router.push('/login')
        }
    }
})
