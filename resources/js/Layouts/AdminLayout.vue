<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tab from '@/Components/Tab.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref  } from 'vue';

defineProps({
    title: String,
});

const buffer = ref(false)
buffer.value = false;

router.on('start', () => {
    buffer.value = true;
})

router.on('finish', () => {
    buffer.value = false;
})
</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin
            </h2>
        </template>

        <ul class="m-2 max-w-7xl mt-0 sm:px-6 lg:px-8 mx-auto flex flex-wrap text-sm font-medium text-center text-gray-500">
            <li class="mr-2">
                <Tab :href="route('users', 1)" :active="route().current('users')">
                    Uporabniki
                </Tab>
            </li>
            <li class="mr-2">
                <Tab :href="route('roles')" :active="route().current('roles')">
                    Uporabniške pravice
                </Tab>
            </li>
            <li class="mr-2">
                <Tab :href="route('classes')" :active="route().current('classes')">
                    Razredi
                </Tab>
            </li>
        </ul>

        <div>
            <slot :buffer="buffer" />
        </div>
    </AppLayout>
</template>