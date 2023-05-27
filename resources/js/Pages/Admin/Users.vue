<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddUser from '@/Pages/Admin/Partials/AddUser.vue';
</script>

<template>
    <AdminLayout title="Dashboard">
        <AddUser :user="$page.props.auth.user" />
    </AdminLayout>
</template>