<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddUser from '@/Pages/Admin/Partials/AddUser.vue';

const props = defineProps({
    roles: Array,
    users: Array,
    can: Object,
});
</script>

<template>
    <AdminLayout title="Dashboard">
        <AddUser :user="$page.props.auth.user" :roles="roles" v-if="props.can.invite"/>
    </AdminLayout>
</template>