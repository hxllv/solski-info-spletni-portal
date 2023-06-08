<script setup>
defineProps({
    value: String,
    textSize: String
});
</script>

<template>
    <label class="block font-medium text-gray-700" :class="textSize">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
