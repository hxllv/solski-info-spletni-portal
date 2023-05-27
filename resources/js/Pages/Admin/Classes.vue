<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddClass from '@/Pages/Admin/Partials/AddClass.vue';
</script>

<template>
    <AdminLayout title="Dashboard">
        <AddClass :user="$page.props.auth.user" />
    </AdminLayout>
</template>