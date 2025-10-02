<script setup lang="ts">
import Input from '../ui/input/Input.vue';
import Button from '../ui/button/Button.vue';
</script>

<template>
    <form class="flex flex-col gap-3" >
        <Input  placeholder="Enter your URL" id="url" class="placeholder:text-sm" />
        <Input  placeholder="Enter alias" id="alias" class="placeholder:text-sm"/>
        <Button class="cursor-pointer self-start" type="submit">Generate</Button>
    </form>
    
</template>