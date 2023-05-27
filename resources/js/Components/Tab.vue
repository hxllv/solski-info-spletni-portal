<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-block p-4 text-blue-600 bg-white rounded-b-lg shadow-inner transition duration-150 ease-in-out'
        : 'inline-block p-4 rounded-b-lg hover:text-gray-600 hover:bg-gray-50 transition duration-150 ease-in-out';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
