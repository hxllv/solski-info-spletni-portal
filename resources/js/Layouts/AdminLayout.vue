<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tab from '@/Components/Tab.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref  } from 'vue';

defineProps({
    title: String,
    backButtonURL: String,
    header: String,
    permission: Array,
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
    <AppLayout :title="title" :permission="permission">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ header ? header : 'Admin' }}
            </h2>
        </template>

        <ul class="m-2 max-w-7xl mt-0 px-2 sm:px-6 lg:px-8 mx-auto flex flex-wrap text-sm font-medium text-center text-gray-500" v-if="!backButtonURL">
            <li class="mr-2" v-if="permission.includes('users.view')">
                <Tab :href="route('users', 1)" :active="route().current('users')">
                    Uporabniki
                </Tab>
            </li>
            <li class="mr-2" v-if="permission.includes('roles.view')">
                <Tab :href="route('roles')" :active="route().current('roles')">
                    Uporabniške pravice
                </Tab>
            </li>
            <li class="mr-2" v-if="permission.includes('classes.view')">
                <Tab :href="route('classes')" :active="route().current('classes')">
                    Razredi
                </Tab>
            </li>
            <li class="mr-2" v-if="permission.includes('subjects.view')">
                <Tab :href="route('subjects')" :active="route().current('subjects')">
                    Predmeti
                </Tab>
            </li>
        </ul>

        <div class="m-2 max-w-7xl mt-0 sm:px-6 lg:px-8 mx-auto flex flex-wrap ">
            <Link class="p-2 pt-4 scale-150" :href="backButtonURL" v-if="backButtonURL">
                <font-awesome-icon :icon="['fas', 'arrow-left']" />
            </Link>
        </div>
        

        <div>
            <slot :buffer="buffer" />
        </div>
    </AppLayout>
</template>