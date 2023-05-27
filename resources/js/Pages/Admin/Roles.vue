<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddRole from '@/Pages/Admin/Partials/AddRole.vue';
</script>

<template>
    <AdminLayout title="Dashboard">
        <AddRole :user="$page.props.auth.user" />
    </AdminLayout>
</template>