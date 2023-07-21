<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tab from '@/Components/Tab.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref  } from 'vue';

defineProps({
    title: String,
    backButtonURL: String,
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