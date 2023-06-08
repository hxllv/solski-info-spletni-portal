<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddClass from '@/Pages/Admin/Partials/AddClass.vue';
import ShowClasses from '@/Pages/Admin/Partials/ShowClasses.vue';

const props = defineProps({
    classes: Object,
    users: Array,
    params: Object,
    middleware: Array,
});
</script>

<template>
    <AdminLayout title="Dashboard" v-slot="layout">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="middleware.includes('classes.create')">
            <div>
                <AddClass :users="users" />   
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="middleware.includes('classes.view')">
            <div>
                <ShowClasses :classes="classes" :users="users" :params="params" :buffer="layout.buffer" :middleware="middleware"/>
            </div>
        </div>
    </AdminLayout>
</template>