<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tab from '@/Components/Tab.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    title: String,
});
</script>

<script>
export default {
    data() {
        return { 
            activeTab: 1, 
        }
    },
}
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
                <Tab :href="route('admin.users')" :active="route().current('admin.users')" @click="activeTab = 1">
                    Uporabniki
                </Tab>
            </li>
            <li class="mr-2">
                <Tab :href="route('admin.roles')" :active="route().current('admin.roles')" @click="activeTab = 2">
                    Uporabniške pravice
                </Tab>
            </li>
            <li class="mr-2">
                <Tab :href="route('admin.class')" :active="route().current('admin.class')" @click="activeTab = 3">
                    Razredi
                </Tab>
            </li>
        </ul>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <slot />
                </div>
            </div>
        </div>
    </AppLayout>
</template>