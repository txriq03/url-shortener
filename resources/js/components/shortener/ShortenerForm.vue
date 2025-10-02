<script setup lang="ts">
import { reactive, ref } from 'vue';
import Button from '../ui/button/Button.vue';
import Input from '../ui/input/Input.vue';

// Form object
const form = reactive({
    url: '',
    alias: '',
});

// Form states
const loading = ref(false);
const error = ref<string | null>(null);
const result = ref<any>(null);

// Needed to get XSRF token
function getCookie(name: string): string | null {
    const match = document.cookie.match(
        new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'),
    );
    return match ? decodeURIComponent(match[3]) : null;
}

const onSubmit = async (e: Event) => {
    e.preventDefault();

    // Set loading
    loading.value = true;
    error.value = null;
    result.value = null;

    try {
        await fetch('/sanctum/csrf-cookie', {
            method: 'GET',
            credentials: 'include',
        });

        const xsrf = getCookie('XSRF-TOKEN');

        const res = await fetch('/api/links', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': xsrf ?? '',
            },
            body: JSON.stringify({
                url: form.url,
                alias: form.alias || undefined,
            }),
        });

        // Handle errors from API
        if (!res.ok) {
            const payload = await res.json().catch(() => null);
            error.value = payload?.message || 'Something went wrong';
            console.log(error.value);
            return;
        }

        result.value = await res.json();
        console.log(result.value);
    } catch (err) {
        if (!form.url) {
            error.value = 'Please enter your URL';
        } else {
            error.value = 'Network Error';
        }

        console.log(err);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <form class="flex flex-col gap-3" @submit="onSubmit">
        <Input
            v-model="form.url"
            placeholder="Enter your URL"
            id="url"
            class="placeholder:text-sm"
        />
        <Input
            v-model="form.alias"
            placeholder="Enter alias"
            id="alias"
            class="placeholder:text-sm"
        />
        <Button
            class="cursor-pointer self-start"
            type="submit"
            :disabled="loading || !form.url.trim()"
            >{{ loading ? 'Generating...' : 'Generate' }}</Button
        >

        <p v-if="error" class="mt-1 text-sm text-rose-500">{{ error }}</p>

        <!-- Display shortened URL -->
        <Input
            v-if="result"
            :default-value="result.short_url"
            readonly
            class="font-mono text-emerald-400"
        />
    </form>
</template>
