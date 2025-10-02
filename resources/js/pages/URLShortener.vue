<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import Button from '@/components/ui/button/Button.vue';
import ShortenerForm from '@/components/shortener/ShortenerForm.vue';
import { ref, onMounted } from 'vue';

const authenticated = ref<boolean | null>(null);

onMounted(async () => {
  try {
    const res = await fetch('/session', {
      method: 'GET',
      credentials: 'include',
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    authenticated.value = data.authenticated
    console.log(authenticated);
  } catch (err) {
    console.error(err)
    authenticated.value = false
  }
})

</script>

<template>
    <main class="flex min-h-screen w-full items-center justify-center px-5">
        <Card class="mx-auto w-full max-w-5xl p-0 bg-[#161615] flex flex-row overflow-hidden">

            <!-- Main Content -->
            <div class="flex flex-col gap-3 flex-1 p-5 sm:p-10">
                
                <!-- Heading and Subheading -->
                <div>
                    <h1 class="text-lg font-bold sm:text-lg md:text-xl">URL Shortener</h1>
                    <p class="text-foreground/50 text-sm md:text-base">Shrink your links, share with ease.</p>
                </div>

                <!-- Fields and Generate Button -->
                <ShortenerForm />
                
                <!-- Footer -->
                 <div v-if="authenticated === false" class="mt-auto space-y-2">
                    <p class="text-foreground/50 text-sm">Want analytics?</p>
                     <div class="flex gap-3 text-sm sm:text-base">
                         <Button class="flex-1 bg-red-500 hover:bg-red-600" as="a" href="/login">Login</Button>
                         <Button class="flex-1" variant="secondary" as="a" href="/register">Sign up</Button>
                     </div>
                 </div>

                 <div v-else class="mt-auto">
                    <Button variant="secondary">Logout</Button>
                 </div>

            </div>

            <!-- Image -->
             <div class="flex-1 max-w-md hidden sm:flex">
                <img src="/lightbulb.jpg" class="object-cover aspect-1/1" />
             </div>
        </Card>
    </main>
</template>