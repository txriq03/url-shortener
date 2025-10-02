<script setup lang="ts">
import ShortenerForm from '@/components/shortener/ShortenerForm.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import { onMounted, ref } from 'vue';

const user = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const res = await fetch('/api/user', {
            method: 'GET',
            credentials: 'include',
            headers: { Accept: 'application/json' },
        });
        if (res.ok) {
            user.value = await res.json();
        } else if (res.status === 401) {
            user.value = null;
        }
    } finally {
        loading.value = false;
        console.log('User:', user);
    }
});

// Needed to get XSRF token
function getCookie(name: string): string | null {
    const match = document.cookie.match(
        new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'),
    );
    return match ? decodeURIComponent(match[3]) : null;
}

// Logout with Sanctum
async function logout() {
    await fetch('/sanctum/csrf-cookie', {
        method: 'GET',
        credentials: 'include',
    });

    const xsrf = getCookie('XSRF-TOKEN');

    // then hit logout
    const res = await fetch('/logout', {
        method: 'POST',
        credentials: 'include',
        headers: {
            Accept: 'application/json',
            'X-XSRF-TOKEN': xsrf ?? '',
        },
    });

    if (res.ok) {
        user.value = null;
    } else {
        console.error('Logout failed', res.status);
    }
}
</script>

<template>
    <main class="flex min-h-screen w-full items-center justify-center px-5">
        <Card
            class="mx-auto flex w-full max-w-5xl flex-row overflow-hidden bg-[#161615] p-0"
        >
            <!-- Main Content -->
            <div class="flex flex-1 flex-col gap-3 p-5 sm:p-10">
                <!-- Heading and Subheading -->
                <div>
                    <h1 class="text-lg font-bold sm:text-lg md:text-xl">
                        URL Shortener
                    </h1>
                    <p class="text-sm text-foreground/50 md:text-base">
                        Shrink your links, share with ease.
                    </p>
                </div>

                <!-- Fields and Generate Button -->
                <ShortenerForm />

                <!-- Footer -->
                <Button
                    v-if="user"
                    class="mt-auto self-start"
                    variant="secondary"
                    @click="logout"
                    >Logout</Button
                >
                <div v-else class="mt-auto space-y-2">
                    <p class="text-sm text-foreground/50">Want analytics?</p>
                    <div class="flex gap-3 text-sm sm:text-base">
                        <Button
                            class="flex-1 bg-red-500 hover:bg-red-600"
                            as="a"
                            href="/login"
                            >Login</Button
                        >
                        <Button
                            class="flex-1"
                            variant="secondary"
                            as="a"
                            href="/register"
                            >Sign up</Button
                        >
                    </div>
                </div>
            </div>

            <!-- Image -->
            <div class="hidden max-w-md flex-1 sm:flex">
                <img src="/lightbulb.jpg" class="aspect-1/1 object-cover" />
            </div>
        </Card>
    </main>
</template>
