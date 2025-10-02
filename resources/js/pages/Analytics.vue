<script setup lang="ts">
import { onMounted, ref } from 'vue'
import Card from '@/components/ui/card/Card.vue'
import Button from '@/components/ui/button/Button.vue'

type User = {
  id: number
  name: string
  email: string
}

type Link = {
  id: number
  alias: string
  url: string
  click_count: number
  created_at: string
}

const user = ref<User | null>(null)
const links = ref<Link[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    // Fetch current user
    const userRes = await fetch('/api/user', {
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })

    if (userRes.ok) {
      user.value = await userRes.json()

      // Fetch user's links only if logged in
      const linkRes = await fetch('/api/links', {
        credentials: 'include',
        headers: { Accept: 'application/json' },
      })

      if (linkRes.ok) {
        links.value = await linkRes.json()
      } else if (linkRes.status === 401) {
        error.value = 'You must be logged in to see your links.'
      } else {
        error.value = 'Failed to load links.'
      }
    } else {
      user.value = null
      error.value = 'You are not logged in.'
    }
  } catch (err) {
    console.error('Error fetching data:', err)
    error.value = 'Network error'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main class="flex min-h-screen w-full items-center justify-center px-5">
    <Card class="mx-auto flex flex-col w-full max-w-2xl overflow-hidden bg-[#161615] p-5">
      <div v-if="loading">Loading...</div>

      <div v-else-if="error" class="text-red-500">
        {{ error }}
      </div>

      <div v-else-if="user">
        <p class="text-sm text-foreground/50">Welcome back</p>
        <p class="text-2xl mb-4">{{ user.name }} 👋</p>

        <h2 class="text-lg font-bold mb-2">Your Shortened Links</h2>
        <ul v-if="links.length > 0" class="space-y-2">
          <li
            v-for="link in links"
            :key="link.id"
            class="p-3 bg-neutral-800/50 rounded"
          >
            <p>
              <span class="font-mono text-emerald-400">/{{ link.alias }}</span>
              → <a
                :href="link.url"
                target="_blank"
                class="underline"
              >{{ link.url }}</a>
            </p>
            <p class="text-sm text-gray-400">
              {{ link.click_count }} clicks • Created
              {{ new Date(link.created_at).toLocaleDateString() }}
            </p>
          </li>
        </ul>
        <p v-else class="text-gray-400">You don’t have any links yet.</p>
      </div>

      <Button class="self-start" variant="secondary" as="a" href="/"> <span><- </span>Back</Button>
    </Card>
  </main>
</template>
