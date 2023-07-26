<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EditHours from '@/Pages/Admin/Partials/EditHours.vue';

const props = defineProps({
    hours: Object,
    permission: Array,
});
</script>

<template>
    <AdminLayout title="Ostale nastavitve" v-slot="layout" :permission="permission">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('users.invite')">
            <div>
                <EditHours :hours="hours"/>   
            </div>
        </div>
    </AdminLayout>
</template>