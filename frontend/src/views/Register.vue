<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import Card from '../components/ui/Card.vue'
import Input from '../components/ui/Input.vue'
import Button from '../components/ui/Button.vue'
import { RouterLink } from 'vue-router'

const auth = useAuthStore()
const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')

const handleRegister = async () => {
  if (password.value !== confirmPassword.value) {
      alert("Passwords don't match")
      return
  }
  try {
    await auth.register({ name: name.value, email: email.value, password: password.value })
  } catch (e) {
    // Error is handled in store
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-emerald-500 mb-2">Exchange Mini</h1>
            <p class="text-slate-400">Start your trading journey.</p>
        </div>

        <Card glass>
            <form @submit.prevent="handleRegister" class="space-y-4">
                 <Input v-model="name" label="Full Name" placeholder="John Doe" />
                <Input v-model="email" label="Email" type="email" placeholder="you@example.com" />
                <Input v-model="password" label="Password" type="password" placeholder="••••••••" />
                <Input v-model="confirmPassword" label="Confirm Password" type="password" placeholder="••••••••" />
                
                <div v-if="auth.error" class="text-red-400 text-sm text-center">
                    {{ auth.error }}
                </div>

                <Button variant="primary" block :disabled="auth.loading" class="mt-2">
                    {{ auth.loading ? 'Creating Account...' : 'Create Account' }}
                </Button>

                <div class="text-center text-sm text-slate-400 mt-4">
                    Already have an account? 
                    <RouterLink to="/login" class="text-emerald-400 hover:text-emerald-300">Login</RouterLink>
                </div>
            </form>
        </Card>
    </div>
  </div>
</template>
